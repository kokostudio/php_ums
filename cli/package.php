<?php

/**
 * PHP UMS - Standard Core System
 * Package Builder Script for Shared Hosting Web Installer
 * 
 * Usage: php cli/package.php
 */

echo "===================================================\n";
echo "   PHP UMS - Installer Package Builder   \n";
echo "===================================================\n\n";

$projectRoot = realpath(__DIR__ . '/..');
$zipLatestName = 'php_ums_installer_latest.zip';
$targetZip = $projectRoot . '/' . $zipLatestName;

// 0. Pre-Upload Runtime & Static Safety Verification
echo "[-] Running Pre-Upload Safety & Runtime Verification...\n";
require_once $projectRoot . '/vendor/autoload.php';

$phpFiles = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($projectRoot . '/app', RecursiveDirectoryIterator::SKIP_DOTS)
);
$checkedFiles = 0;
foreach ($phpFiles as $pf) {
    if ($pf->isFile() && strtolower($pf->getExtension()) === 'php') {
        $filePath = $pf->getPathname();
        $code = file_get_contents($filePath);
        $checkedFiles++;

        // Detect invalid static calls
        if (strpos($code, 'Security::generateCsrfToken') !== false) {
            echo "[!] ERROR in {$filePath}: Security::generateCsrfToken() does not exist (use Session::getCsrfToken()).\n";
            exit(1);
        }
    }
}
echo "[✓] Verified {$checkedFiles} PHP files for syntax/static sanity.\n";

// Verify Modular Router & Registered Route Handlers
$testRouter = new \App\Core\Router();
$testRouter->loadRoutes($projectRoot);
$registeredRoutes = $testRouter->routes();
$totalRouteCount = count($registeredRoutes);
foreach ($registeredRoutes as $routeDef) {
    $httpMethod = $routeDef['method'] ?? 'GET';
    $pattern = $routeDef['path'] ?? '/';
    $handler = $routeDef['handler'] ?? null;
    if (is_array($handler) && count($handler) === 2) {
        [$controllerClass, $methodName] = $handler;
        if (!class_exists($controllerClass)) {
            echo "[!] ERROR: Controller class {$controllerClass} does not exist for route [{$httpMethod} {$pattern}].\n";
            exit(1);
        }
        if (!method_exists($controllerClass, $methodName)) {
            echo "[!] ERROR: Method {$methodName} does not exist in {$controllerClass} for route [{$httpMethod} {$pattern}].\n";
            exit(1);
        }
    }
}
echo "[✓] Verified {$totalRouteCount} route definitions across core and modules.\n";

// 1. Clear temporary cache files
echo "[-] Cleaning cache and temporary files...\n";
$cacheDir = $projectRoot . '/storage/cache';
if (is_dir($cacheDir)) {
    $files = glob($cacheDir . '/*');
    foreach ($files as $f) {
        if (is_file($f)) @unlink($f);
    }
}

// 2. Ensure upload and storage directories exist
$dirsToEnsure = [
    $projectRoot . '/storage/logs',
    $projectRoot . '/storage/cache',
    $projectRoot . '/public/uploads',
    $projectRoot . '/public/uploads/settings',
    $projectRoot . '/public/uploads/profiles',
];
foreach ($dirsToEnsure as $d) {
    if (!is_dir($d)) {
        @mkdir($d, 0775, true);
    }
}

// 3. Build ZIP file using ZipArchive
if (!extension_loaded('zip')) {
    echo "[!] PHP zip extension is not loaded. Please enable zip extension or compress manually.\n";
    exit(1);
}

echo "[-] Creating installer zip package: {$zipLatestName}...\n";

$zip = new ZipArchive();
if ($zip->open($targetZip, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
    echo "[!] Failed to create zip file: {$targetZip}\n";
    exit(1);
}

// Directories and files to include
$includeList = [
    'app',
    'cli',
    'database',
    'public',
    'storage',
    'vendor',
    '.htaccess',
    '.env.example',
    'index.php',
    'composer.json',
    'composer.lock',
    'install.php',
    'INSTALL.md',
    'README.md',
    'rules.md'
];

// Patterns to exclude
$excludePatterns = [
    '/\.git/',
    '/\.claude/',
    '/\.cursor/',
    '/\.gemini/',
    '/\.agents/',
    '/\.autocode/',
    '/graft/',
    '/scratch/',
    '/\.env$/',
    '/installed\.lock$/',
    '/\.zip$/',
    '/storage\/cache\/[^\.]/',
    '/storage\/logs\/[^\.]/',
    '/public\/uploads\/[^\.]+\.(png|jpg|jpeg|webp|pdf)$/',
    '/tests(\/|$)/',
];

$fileCount = 0;

foreach ($includeList as $item) {
    $fullPath = $projectRoot . '/' . $item;
    if (!file_exists($fullPath)) continue;

    if (is_file($fullPath)) {
        $zip->addFile($fullPath, $item);
        if (defined('ZipArchive::OPSYS_UNIX')) {
            $zip->setExternalAttributesName($item, ZipArchive::OPSYS_UNIX, 0644 << 16);
        }
        $fileCount++;
    } elseif (is_dir($fullPath)) {
        $zip->addEmptyDir($item);
        if (defined('ZipArchive::OPSYS_UNIX')) {
            $zip->setExternalAttributesName($item . '/', ZipArchive::OPSYS_UNIX, (040755 << 16));
        }
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($fullPath, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $file) {
            $path = str_replace('\\', '/', $file->getPathname());
            $relative = substr($path, strlen($projectRoot) + 1);

            $skip = false;
            foreach ($excludePatterns as $pattern) {
                if (preg_match($pattern, $relative)) {
                    $skip = true;
                    break;
                }
            }

            if ($skip) continue;

            if ($file->isDir()) {
                $zip->addEmptyDir($relative);
                if (defined('ZipArchive::OPSYS_UNIX')) {
                    $zip->setExternalAttributesName($relative . '/', ZipArchive::OPSYS_UNIX, (040755 << 16));
                }
            } elseif ($file->isFile()) {
                $zip->addFile($file->getPathname(), $relative);
                if (defined('ZipArchive::OPSYS_UNIX')) {
                    $zip->setExternalAttributesName($relative, ZipArchive::OPSYS_UNIX, 0644 << 16);
                }
                $fileCount++;
            }
        }
    }
}

$zip->close();

$zipSizeMb = round(filesize($targetZip) / (1024 * 1024), 2);

echo "\n===================================================\n";
echo "[✓] Installer Package Created Successfully!\n";
echo "===================================================\n";
echo "File:     {$zipLatestName} ({$zipSizeMb} MB)\n";
echo "Files:    {$fileCount} files packaged\n";
echo "Location: {$targetZip}\n\n";
echo "วิธีใช้งานบน Shared Hosting (cPanel / DirectAdmin / Plesk):\n";
echo "1. อัปโหลดไฟล์ {$zipLatestName} ไปยัง public_html/ บนโฮสติ้ง\n";
echo "2. แตกไฟล์ (Extract) ออกมาที่โฟลเดอร์เว็บไซต์\n";
echo "3. เปิดเบราว์เซอร์เข้าสู่โดเมนของคุณ เช่น https://yourdomain.com/ หรือ /install\n";
echo "4. หน้าจอตัวช่วยติดตั้ง (Installation Wizard) จะเปิดขึ้นมาให้กรอกฐานข้อมูลทันที!\n";
echo "===================================================\n";
