<?php

namespace App\Controllers;

use App\Core\Config;
use App\Core\Controller;
use App\Core\Database;
use App\Core\MigrationRunner;
use App\Core\Security;
use App\Core\Session;
use App\Core\Settings;
use PDO;

/**
 * Web Installer สำหรับ Production/Shared Hosting
 * 3 ขั้น: ตรวจสภาพแวดล้อม → ฐานข้อมูล → บัญชีผู้ดูแลระบบ
 * ล็อกด้วย storage/installed.lock — ติดตั้งแล้วไม่สามารถติดตั้งซ้ำได้
 */
class InstallController extends Controller
{
    private string $lockFile;
    private string $envFile;
    private string $envExampleFile;

    public function __construct()
    {
        $this->lockFile = __DIR__ . '/../../storage/installed.lock';
        $this->envFile = __DIR__ . '/../../.env';
        $this->envExampleFile = __DIR__ . '/../../.env.example';
    }

    /**
     * Check if system is already installed
     */
    private function isInstalled(): bool
    {
        return file_exists($this->lockFile);
    }

    /**
     * Get pending migrations that have not yet been applied
     */
    private function getPendingMigrations(): array
    {
        try {
            $db = Database::getInstance();
            $stmt = $db->prepare("SELECT migration FROM migrations WHERE ? = 1");
            $stmt->execute([1]);
            $applied = $stmt->fetchAll(PDO::FETCH_COLUMN) ?: [];

            $migrationsDir = __DIR__ . '/../../database/migrations';
            $files = glob($migrationsDir . '/*.php') ?: [];
            $all = array_map(function($f) {
                return basename($f, '.php');
            }, $files);

            sort($all);
            return array_values(array_diff($all, $applied));
        } catch (\Throwable $e) {
            return [];
        }
    }

    /**
     * Check if users table exists and has at least one Super Admin
     */
    private function hasSuperAdmin(): bool
    {
        try {
            $db = Database::getInstance();
            $checkTable = $db->prepare("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = ?");
            $checkTable->execute(['users']);
            if ((int)$checkTable->fetchColumn() === 0) {
                return false;
            }
            $count = $db->prepare("SELECT COUNT(*) FROM users WHERE role_id = ?");
            $count->execute([1]);
            return (int)$count->fetchColumn() > 0;
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Main installer wizard page
     */
    public function index(): void
    {
        if ($this->isInstalled()) {
            $pendingMigrations = $this->getPendingMigrations();
            $hasSuperAdmin = $this->hasSuperAdmin();
            $isSuperAdmin = (\App\Core\Session::get('role_id') == 1);
            $currentAdminName = \App\Core\Session::get('username') ?? '';

            $this->view('install/already_installed', [
                'installedAt' => date('d/m/Y H:i:s', filemtime($this->lockFile) ?: time()),
                'pendingMigrations' => $pendingMigrations,
                'hasSuperAdmin' => $hasSuperAdmin,
                'isSuperAdmin' => $isSuperAdmin,
                'currentAdminName' => $currentAdminName
            ], null);
            return;
        }

        // 1. Check PHP version
        $phpVersion = PHP_VERSION;
        $phpPass = version_compare($phpVersion, '8.2.0', '>=');

        // 2. Check PHP Extensions
        $requiredExtensions = [
            'pdo' => 'PDO',
            'pdo_mysql' => 'PDO MySQL Driver',
            'mbstring' => 'Multibyte String',
            'json' => 'JSON',
            'curl' => 'cURL Library',
            'openssl' => 'OpenSSL',
            'fileinfo' => 'Fileinfo',
            'gd' => 'GD Image Processing'
        ];

        $extensionStatus = [];
        $extensionsAllPass = true;
        foreach ($requiredExtensions as $ext => $label) {
            $loaded = extension_loaded($ext);
            $extensionStatus[$ext] = [
                'label' => $label,
                'pass' => $loaded
            ];
            if (!$loaded && in_array($ext, ['pdo', 'pdo_mysql', 'mbstring', 'json', 'openssl'])) {
                $extensionsAllPass = false;
            }
        }

        // 3. Check and create required directories
        $requiredDirs = [
            'storage' => __DIR__ . '/../../storage',
            'storage/logs' => __DIR__ . '/../../storage/logs',
            'storage/cache' => __DIR__ . '/../../storage/cache',
            'public/uploads' => __DIR__ . '/../../public/uploads',
            'public/uploads/settings' => __DIR__ . '/../../public/uploads/settings',
            'public/uploads/profiles' => __DIR__ . '/../../public/uploads/profiles',
        ];

        $dirStatus = [];
        $dirsAllPass = true;
        foreach ($requiredDirs as $name => $path) {
            if (!is_dir($path)) {
                @mkdir($path, 0775, true);
            }
            $isWritable = is_writable($path);
            $dirStatus[$name] = [
                'path' => $name,
                'pass' => $isWritable
            ];
            if (!$isWritable) {
                $dirsAllPass = false;
            }
        }

        $allRequirementsPassed = $phpPass && $extensionsAllPass && $dirsAllPass;

        // Suggested DB parameters from existing .env (if present)
        $defaultHost = '127.0.0.1';
        $defaultPort = '3306';
        $defaultDb = 'php_ums';
        $defaultUser = 'root';
        $defaultUrl = 'http://' . ($_SERVER['HTTP_HOST'] ?? 'localhost');

        if (file_exists($this->envFile)) {
            $defaultHost = Config::get('DB_HOST', $defaultHost);
            $defaultPort = Config::get('DB_PORT', $defaultPort);
            $defaultDb = Config::get('DB_DATABASE', $defaultDb);
            $defaultUser = Config::get('DB_USERNAME', $defaultUser);
            $defaultUrl = Config::get('APP_URL', $defaultUrl);
        }

        $this->view('install/index', [
            'phpVersion' => $phpVersion,
            'phpPass' => $phpPass,
            'extensionStatus' => $extensionStatus,
            'extensionsAllPass' => $extensionsAllPass,
            'dirStatus' => $dirStatus,
            'dirsAllPass' => $dirsAllPass,
            'allPassed' => $allRequirementsPassed,
            'defaultHost' => $defaultHost,
            'defaultPort' => $defaultPort,
            'defaultDb' => $defaultDb,
            'defaultUser' => $defaultUser,
            'defaultUrl' => $defaultUrl,
        ], null);
    }

    /**
     * AJAX: Test database connection
     */
    public function testDb(): void
    {
        if ($this->isInstalled()) {
            $this->json(['success' => false, 'message' => 'ระบบได้รับการติดตั้งเรียบร้อยแล้ว'], 400);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;

        $host = trim($input['host'] ?? '127.0.0.1');
        $port = (int)($input['port'] ?? 3306);
        $dbName = trim($input['database'] ?? '');
        $username = trim($input['username'] ?? '');
        $password = (string)($input['password'] ?? '');

        if (empty($dbName)) {
            $this->json(['success' => false, 'message' => 'กรุณาระบุชื่อฐานข้อมูล'], 422);
            return;
        }

        $result = Database::testConnection($host, $port, $dbName, $username, $password);
        $this->json($result);
    }

    /**
     * AJAX/POST: Process installation
     */
    public function process(): void
    {
        if ($this->isInstalled()) {
            $this->json(['success' => false, 'message' => 'ระบบได้รับการติดตั้งเรียบร้อยแล้ว'], 400);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;

        // 1. Gather & Validate inputs
        $dbHost = trim($input['db_host'] ?? '127.0.0.1');
        $dbPort = (int)($input['db_port'] ?? 3306);
        $dbName = trim($input['db_name'] ?? '');
        $dbUser = trim($input['db_user'] ?? '');
        $dbPass = (string)($input['db_pass'] ?? '');

        $appName = trim($input['app_name'] ?? 'PHP UMS - Standard Core System');
        $appUrl = rtrim(trim($input['app_url'] ?? 'http://localhost'), '/');
        $appEnv = in_array($input['app_env'] ?? 'production', ['production', 'local']) ? $input['app_env'] : 'production';

        $adminUsername = trim($input['admin_username'] ?? 'superadmin');
        $adminEmail = trim($input['admin_email'] ?? 'admin@example.com');
        $adminPassword = (string)($input['admin_password'] ?? '');
        $adminPasswordConfirm = (string)($input['admin_password_confirmation'] ?? '');

        if (empty($dbName) || empty($dbUser)) {
            $this->json(['success' => false, 'message' => 'กรุณากรอกข้อมูลฐานข้อมูลให้ครบถ้วน'], 422);
            return;
        }

        if (!preg_match('/^[a-zA-Z0-9_]+$/', $dbName)) {
            $this->json(['success' => false, 'message' => 'ชื่อฐานข้อมูลใช้ได้เฉพาะตัวอักษรอังกฤษ ตัวเลข และขีดล่างเท่านั้น'], 422);
            return;
        }

        if (empty($adminUsername) || empty($adminEmail) || empty($adminPassword)) {
            $this->json(['success' => false, 'message' => 'กรุณากรอกข้อมูลผู้ดูแลระบบให้ครบถ้วน'], 422);
            return;
        }

        if (!filter_var($adminEmail, FILTER_VALIDATE_EMAIL)) {
            $this->json(['success' => false, 'message' => 'รูปแบบอีเมลไม่ถูกต้อง'], 422);
            return;
        }

        if (strlen($adminPassword) < 6) {
            $this->json(['success' => false, 'message' => 'รหัสผ่านต้องมีความยาวอย่างน้อย 6 ตัวอักษร'], 422);
            return;
        }

        if ($adminPassword !== $adminPasswordConfirm) {
            $this->json(['success' => false, 'message' => 'การยืนยันรหัสผ่านไม่ตรงกัน'], 422);
            return;
        }

        try {
            // 2. Connect or auto-create database
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_TIMEOUT => 8,
            ];

            try {
                $dsn = "mysql:host={$dbHost};port={$dbPort};dbname={$dbName};charset=utf8mb4";
                $pdo = new PDO($dsn, $dbUser, $dbPass, $options);
            } catch (\PDOException $e) {
                if ($e->getCode() == 1049) {
                    $dsnNoDb = "mysql:host={$dbHost};port={$dbPort};charset=utf8mb4";
                    $tempPdo = new PDO($dsnNoDb, $dbUser, $dbPass, $options);
                    $createStmt = $tempPdo->prepare("CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                    $createStmt->execute();
                    $pdo = new PDO($dsn, $dbUser, $dbPass, $options);
                } else {
                    throw $e;
                }
            }

            // 3. Generate App Key & write .env
            $appKey = 'base64:' . base64_encode(random_bytes(32));
            $this->writeEnvFile([
                'APP_NAME' => '"' . addslashes($appName) . '"',
                'APP_ENV' => $appEnv,
                'APP_DEBUG' => $appEnv === 'local' ? 'true' : 'false',
                'APP_URL' => $appUrl,
                'DB_CONNECTION' => 'mysql',
                'DB_HOST' => $dbHost,
                'DB_PORT' => (string)$dbPort,
                'DB_DATABASE' => $dbName,
                'DB_USERNAME' => $dbUser,
                'DB_PASSWORD' => $dbPass,
                'APP_KEY' => $appKey,
            ]);

            // Reload Config
            Config::load($this->envFile);
            Database::reset();
            Database::setInstance($pdo);

            // 4. Run Migrations
            $migrationsPath = realpath(__DIR__ . '/../../database/migrations') ?: (__DIR__ . '/../../database/migrations');
            if (!is_dir($migrationsPath)) {
                throw new \RuntimeException("ไม่พบโฟลเดอร์ database/migrations กรุณาตรวจสอบว่าได้อัปโหลดโฟลเดอร์ database/migrations มายังเซิร์ฟเวอร์เรียบร้อยแล้ว");
            }

            // If migrations table exists but users table doesn't, reset migrations table
            $tableCheck = $pdo->prepare("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = ?");
            $tableCheck->execute(['migrations']);
            $hasMigrationsTable = (int)$tableCheck->fetchColumn() > 0;
            $tableCheck->execute(['users']);
            $hasUsersTable = (int)$tableCheck->fetchColumn() > 0;
            if ($hasMigrationsTable && !$hasUsersTable) {
                $pdo->prepare("DROP TABLE IF EXISTS migrations")->execute();
            }

            $runner = new MigrationRunner($pdo, $migrationsPath);
            $applied = $runner->applyMigrations();

            // Verify that users table exists
            $tableCheck->execute(['users']);
            if ((int)$tableCheck->fetchColumn() === 0) {
                throw new \RuntimeException("กระบวนการสร้างตารางฐานข้อมูลไม่สมบูรณ์ ไม่พบตาราง users กรุณาล้างตารางเดิมแล้วลองใหม่อีกครั้ง");
            }

            // 5. Update or Insert Super Admin credentials (id=1)
            $passwordHash = password_hash($adminPassword, PASSWORD_BCRYPT, ['cost' => 12]);

            $cleanOther = $pdo->prepare("DELETE FROM users WHERE (username = ? OR email = ?) AND id != 1");
            $cleanOther->execute([$adminUsername, $adminEmail]);

            $checkUser = $pdo->prepare("SELECT id FROM users WHERE id = ?");
            $checkUser->execute([1]);
            $existingAdminRow = $checkUser->fetch();

            if ($existingAdminRow) {
                $stmt = $pdo->prepare("
                    UPDATE users
                    SET username = ?, email = ?, password = ?, role_id = 1, status = 'active'
                    WHERE id = ?
                ");
                $stmt->execute([$adminUsername, $adminEmail, $passwordHash, 1]);
            } else {
                $cleanAll = $pdo->prepare("DELETE FROM users WHERE username = ? OR email = ?");
                $cleanAll->execute([$adminUsername, $adminEmail]);

                $insertStmt = $pdo->prepare("
                    INSERT INTO users (id, username, email, password, role_id, status)
                    VALUES (?, ?, ?, ?, 1, 'active')
                ");
                $insertStmt->execute([1, $adminUsername, $adminEmail, $passwordHash]);
            }

            // 6. Update System Settings
            Settings::set('site_name', $appName);
            Settings::set('site_tagline', 'ระบบจัดการผู้ใช้งานและสิทธิ์ (PHP UMS Core)');
            Settings::set('app_url', $appUrl);

            // 7. Create storage/installed.lock
            $lockContent = json_encode([
                'installed_at' => date('Y-m-d H:i:s'),
                'app_name' => $appName,
                'app_url' => $appUrl,
                'admin_username' => $adminUsername,
                'admin_email' => $adminEmail,
                'version' => '1.0.0',
                'php_version' => PHP_VERSION
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

            file_put_contents($this->lockFile, $lockContent);

            // Save installation summary to session for completion page
            Session::set('install_summary', [
                'app_name' => $appName,
                'app_url' => $appUrl,
                'admin_username' => $adminUsername,
                'admin_email' => $adminEmail
            ]);

            while (ob_get_level() > 0) {
                ob_end_clean();
            }

            // 8. Installation complete
            $this->json([
                'success' => true,
                'message' => 'ติดตั้งระบบสำเร็จเรียบร้อยแล้ว',
                'redirect' => '/install/complete'
            ]);
        } catch (\Throwable $e) {
            while (ob_get_level() > 0) {
                ob_end_clean();
            }

            $this->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในการติดตั้ง: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Completion screen
     */
    public function complete(): void
    {
        $summary = Session::get('install_summary') ?? [
            'app_name' => Config::get('APP_NAME', 'PHP UMS - Standard Core System'),
            'app_url' => Config::get('APP_URL', 'http://localhost'),
            'admin_username' => 'superadmin',
            'admin_email' => 'admin@example.com'
        ];

        $this->view('install/complete', [
            'summary' => $summary
        ], null);
    }

    /**
     * Write updated variables into .env file
     */
    private function writeEnvFile(array $variables): void
    {
        $baseContent = file_exists($this->envExampleFile)
            ? file_get_contents($this->envExampleFile)
            : '';

        $lines = explode("\n", $baseContent);
        $keysUpdated = [];

        $newLines = [];
        foreach ($lines as $line) {
            $trimmed = trim($line);
            if (empty($trimmed) || str_starts_with($trimmed, '#')) {
                $newLines[] = $line;
                continue;
            }

            if (str_contains($line, '=')) {
                [$key] = explode('=', $line, 2);
                $key = trim($key);
                if (array_key_exists($key, $variables)) {
                    $newLines[] = "{$key}={$variables[$key]}";
                    $keysUpdated[$key] = true;
                    continue;
                }
            }

            $newLines[] = $line;
        }

        foreach ($variables as $key => $val) {
            if (!isset($keysUpdated[$key])) {
                $newLines[] = "{$key}={$val}";
            }
        }

        file_put_contents($this->envFile, implode("\n", $newLines));
    }

    /**
     * Web Upgrade API for Production (Applies pending migrations without losing data)
     */
    public function upgrade(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $isSuperAdmin = (\App\Core\Session::get('role_id') == 1);

        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        $username = trim($input['admin_username'] ?? '');
        $password = trim($input['admin_password'] ?? '');

        if (!$isSuperAdmin) {
            if (empty($username) || empty($password)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'กรุณากรอกชื่อผู้ใช้และรหัสผ่าน Super Admin เพื่อยืนยันการอัปเกรดระบบ'
                ]);
                return;
            }

            try {
                $db = Database::getInstance();
                $stmt = $db->prepare("SELECT id, username, password, role_id, status FROM users WHERE (username = ? OR email = ?) LIMIT 1");
                $stmt->execute([$username, $username]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$user || !password_verify($password, $user['password']) || (int)$user['role_id'] !== 1) {
                    echo json_encode([
                        'success' => false,
                        'message' => 'ชื่อผู้ใช้หรือรหัสผ่าน Super Admin ไม่ถูกต้อง'
                    ]);
                    return;
                }
            } catch (\Throwable $e) {
                echo json_encode([
                    'success' => false,
                    'message' => 'เกิดข้อผิดพลาดในการตรวจสอบสิทธิ์: ' . $e->getMessage()
                ]);
                return;
            }
        }

        try {
            $runner = new MigrationRunner();
            $logs = [];
            $applied = $runner->applyMigrations(function(string $msg) use (&$logs) {
                $logs[] = $msg;
            });

            // Clear file-based cache if any
            $cacheDir = __DIR__ . '/../../storage/cache';
            if (is_dir($cacheDir)) {
                $files = glob($cacheDir . '/*');
                foreach ($files as $file) {
                    if (is_file($file)) @unlink($file);
                }
            }

            // Update installed.lock metadata
            $lockData = [];
            if (file_exists($this->lockFile)) {
                $lockData = json_decode(file_get_contents($this->lockFile), true) ?: [];
            }
            $lockData['version'] = '1.0.0';
            $lockData['last_upgraded_at'] = date('Y-m-d H:i:s');
            $lockData['upgrade_method'] = 'web_installer';
            @file_put_contents($this->lockFile, json_encode($lockData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

            echo json_encode([
                'success' => true,
                'message' => empty($applied)
                    ? 'ฐานข้อมูลเป็นเวอร์ชันล่าสุดเรียบร้อยแล้ว ไม่มีการเปลี่ยนแปลง'
                    : 'อัปเกรดระบบและฐานข้อมูลเรียบร้อยแล้ว (' . count($applied) . ' migrations สำเร็จ)',
                'applied' => $applied,
                'logs' => $logs
            ]);
        } catch (\Throwable $e) {
            echo json_encode([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในการรัน Migration: ' . $e->getMessage()
            ]);
        }
    }
}
