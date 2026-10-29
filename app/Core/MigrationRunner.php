<?php

namespace App\Core;

class MigrationRunner
{
    private \PDO $db;
    private string $migrationsPath;

    public function __construct(?\PDO $db = null, ?string $migrationsPath = null)
    {
        $this->db = $db ?? Database::getInstance();
        $this->migrationsPath = $migrationsPath ?? (__DIR__ . '/../../database/migrations');
        $this->createMigrationsTable();
    }

    private function createMigrationsTable(): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS migrations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            migration VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        $this->db->prepare($sql)->execute();
    }

    public function getAppliedMigrations(): array
    {
        $stmt = $this->db->prepare("SELECT migration FROM migrations WHERE ? = 1");
        $stmt->execute([1]);
        return $stmt->fetchAll(\PDO::FETCH_COLUMN) ?: [];
    }

    private function saveMigration(string $migration): void
    {
        $stmt = $this->db->prepare("INSERT INTO migrations (migration) VALUES (?)");
        $stmt->execute([$migration]);
    }

    /**
     * Run all pending migrations
     *
     * @param callable|null $logger Function to receive progress messages (message, is_success)
     * @return array List of applied migration filenames
     */
    public function applyMigrations(?callable $logger = null): array
    {
        if (!is_dir($this->migrationsPath)) {
            throw new \RuntimeException("ไม่พบโฟลเดอร์ไฟล์ฐานข้อมูล (Migrations directory not found): {$this->migrationsPath}");
        }

        $allFiles = scandir($this->migrationsPath);
        if ($allFiles === false) {
            throw new \RuntimeException("ไม่สามารถเปิดอ่านโฟลเดอร์ฐานข้อมูลได้: {$this->migrationsPath}");
        }

        sort($allFiles);

        $phpFiles = array_values(array_filter($allFiles, fn($f) => str_ends_with($f, '.php')));
        if (empty($phpFiles)) {
            throw new \RuntimeException("ไม่พบไฟล์ Schema ฐานข้อมูลในโฟลเดอร์: {$this->migrationsPath} กรุณาตรวจสอบว่าได้อัปโหลดโฟลเดอร์ database/migrations ครบถ้วนแล้ว");
        }

        $appliedMigrations = $this->getAppliedMigrations();
        $toApply = array_diff($phpFiles, $appliedMigrations);
        $executed = [];

        foreach ($toApply as $file) {
            $filePath = $this->migrationsPath . '/' . $file;
            require_once $filePath;

            $className = pathinfo($file, PATHINFO_FILENAME);
            if (preg_match('/^[0-9]+_(.+)$/', $className, $matches)) {
                $className = $matches[1];
            }

            if (!class_exists($className)) {
                throw new \RuntimeException("Class {$className} not found in {$file}");
            }

            if ($logger) {
                $logger("Migrating: {$file}...", true);
            }

            ob_start();
            try {
                /** @var Migration $migration */
                $migration = new $className($this->db);
                $migration->up();
                ob_end_clean();

                $this->saveMigration($file);
                $executed[] = $file;

                if ($logger) {
                    $logger("Migrated:  {$file}", true);
                }
            } catch (\Throwable $e) {
                ob_end_clean();
                $err = "Migration failed in {$file}: " . $e->getMessage();
                if ($logger) {
                    $logger($err, false);
                }
                throw new \RuntimeException($err, 0, $e);
            }
        }

        return $executed;
    }
}