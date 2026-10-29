<?php

namespace App\Core;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $instance = null;

    private function __construct()
    {
        // Private constructor for Singleton
    }

    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            $host = Config::get('DB_HOST', '127.0.0.1');
            $port = Config::get('DB_PORT', '3306');
            $dbName = Config::get('DB_DATABASE', '');
            $username = Config::get('DB_USERNAME', 'root');
            $password = Config::get('DB_PASSWORD', '');

            $dsn = "mysql:host={$host};port={$port};dbname={$dbName};charset=utf8mb4";
            
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false, // Forces native prepared statements
            ];

            try {
                self::$instance = new PDO($dsn, $username, $password, $options);
            } catch (PDOException $e) {
                // Do not expose DB credentials on failure, just throw general exception or log it
                throw new \Exception("Database connection failed. Please check your configuration.");
            }
        }

        return self::$instance;
    }

    public static function setInstance(PDO $pdo): void
    {
        self::$instance = $pdo;
    }

    public static function reset(): void
    {
        self::$instance = null;
    }

    /**
     * Test database connection with given credentials (used during installation)
     */
    public static function testConnection(string $host, int $port, string $dbName, string $username, string $password): array
    {
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_TIMEOUT => 8,
        ];

        try {
            $dsn = "mysql:host={$host};port={$port};dbname={$dbName};charset=utf8mb4";
            new PDO($dsn, $username, $password, $options);
            return ['success' => true, 'message' => 'เชื่อมต่อฐานข้อมูลสำเร็จ'];
        } catch (PDOException $e) {
            if ($e->getCode() == 1049) {
                return ['success' => false, 'message' => 'ไม่พบฐานข้อมูล "' . $dbName . '" (ระบบจะสร้างให้อัตโนมัติตอนติดตั้ง)'];
            }
            if ($e->getCode() == 1045) {
                return ['success' => false, 'message' => 'ชื่อผู้ใช้หรือรหัสผ่านฐานข้อมูลไม่ถูกต้อง'];
            }
            if ($e->getCode() == 2002) {
                return ['success' => false, 'message' => 'ไม่สามารถเชื่อมต่อไปยังเซิร์ฟเวอร์ฐานข้อมูลได้ (ตรวจสอบ Host และ Port)'];
            }
            return ['success' => false, 'message' => 'การเชื่อมต่อฐานข้อมูลล้มเหลว: ' . $e->getMessage()];
        }
    }
}
