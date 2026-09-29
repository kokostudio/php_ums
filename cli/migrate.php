<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Config;
use App\Core\Database;

// Load config
Config::load(__DIR__ . '/../.env');

class MigrationRunner
{
    private \PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->createMigrationsTable();
    }

    private function createMigrationsTable()
    {
        $sql = "CREATE TABLE IF NOT EXISTS migrations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            migration VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        $this->db->exec($sql);
    }

    private function getAppliedMigrations(): array
    {
        $stmt = $this->db->query("SELECT migration FROM migrations");
        return $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }

    private function saveMigration(string $migration)
    {
        $stmt = $this->db->prepare("INSERT INTO migrations (migration) VALUES (:migration)");
        $stmt->execute(['migration' => $migration]);
    }

    public function dropAllTables()
    {
        $this->db->exec('SET FOREIGN_KEY_CHECKS = 0');
        $tables = $this->db->query('SHOW TABLES')->fetchAll(\PDO::FETCH_COLUMN);
        foreach ($tables as $table) {
            $this->db->exec("DROP TABLE `$table`");
        }
        $this->db->exec('SET FOREIGN_KEY_CHECKS = 1');
        echo "All tables dropped." . PHP_EOL;
    }

    public function applyMigrations()
    {
        $appliedMigrations = $this->getAppliedMigrations();
        $files = scandir(__DIR__ . '/../database/migrations');
        $toApply = array_diff($files, $appliedMigrations);

        foreach ($toApply as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }

            require_once __DIR__ . '/../database/migrations/' . $file;
            $className = pathinfo($file, PATHINFO_FILENAME);
            
            // Extract the class name by removing the timestamp prefix if it exists
            // Assuming format: 20260819120000_CreateUsersTable.php
            if (preg_match('/^[0-9]+_(.+)$/', $className, $matches)) {
                $className = $matches[1];
            }

            echo "Applying migration: {$file}" . PHP_EOL;
            
            $migration = new $className();
            $migration->up();
            
            $this->saveMigration($file);
            echo "Applied migration: {$file}" . PHP_EOL;
        }

        echo "All migrations applied." . PHP_EOL;
    }
}

// Create migrations directory if it doesn't exist
$migrationsDir = __DIR__ . '/../database/migrations';
if (!is_dir($migrationsDir)) {
    mkdir($migrationsDir, 0755, true);
}

$options = getopt('', ['refresh']);
$isRefresh = isset($options['refresh']);

if ($isRefresh) {
    echo "Refreshing migration database...\n";
    $tempRunner = new class {
        private \PDO $db;
        public function __construct() { $this->db = \App\Core\Database::getInstance(); }
        public function dropAll() {
            $this->db->exec('SET FOREIGN_KEY_CHECKS = 0');
            $tables = $this->db->query('SHOW TABLES')->fetchAll(\PDO::FETCH_COLUMN);
            foreach ($tables as $table) { $this->db->exec("DROP TABLE `$table`"); }
            $this->db->exec('SET FOREIGN_KEY_CHECKS = 1');
        }
    };
    $tempRunner->dropAll();
    echo "All tables dropped.\n";
}

$runner = new MigrationRunner();
$runner->applyMigrations();
