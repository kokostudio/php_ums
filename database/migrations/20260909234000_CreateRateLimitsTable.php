<?php

class CreateRateLimitsTable
{
    public function up(): void
    {
        $db = \App\Core\Database::getInstance();
        $sql = "CREATE TABLE IF NOT EXISTS rate_limits (
            id INT AUTO_INCREMENT PRIMARY KEY,
            ip_address VARCHAR(45) NOT NULL,
            endpoint VARCHAR(255) NOT NULL,
            attempts INT DEFAULT 1,
            lockout_time TIMESTAMP NULL DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY ip_endpoint (ip_address, endpoint)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
        
        $db->exec($sql);
    }

    public function down(): void
    {
        $db = \App\Core\Database::getInstance();
        $db->exec("DROP TABLE IF EXISTS rate_limits;");
    }
}
