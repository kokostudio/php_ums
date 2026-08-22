<?php

use App\Core\Migration;

class CreateAuditLogsTable extends Migration
{
    public function up(): void
    {
        $this->db->exec("
            CREATE TABLE IF NOT EXISTS audit_logs (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NULL,
                table_name VARCHAR(50) NOT NULL,
                record_id INT NOT NULL,
                action ENUM('insert', 'update', 'delete') NOT NULL,
                old_values JSON NULL,
                new_values JSON NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");
    }

    public function down(): void
    {
        $this->db->exec("DROP TABLE IF EXISTS audit_logs");
    }
}
