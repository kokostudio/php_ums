<?php

use App\Core\Migration;

class CreateUserSessionsTable extends Migration
{
    public function up(): void
    {
        $this->db->exec("
            CREATE TABLE IF NOT EXISTS user_sessions (
                id VARCHAR(128) PRIMARY KEY,
                user_id INT NULL,
                ip_address VARCHAR(45) NULL,
                user_agent TEXT NULL,
                payload TEXT NOT NULL,
                last_activity INT NOT NULL,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");
        
        echo "Created user_sessions table.\n";
    }

    public function down(): void
    {
        $this->db->exec("DROP TABLE IF EXISTS user_sessions;");
        echo "Dropped user_sessions table.\n";
    }
}
