<?php

use App\Core\Migration;

class CreateUsersTable extends Migration
{
    public function up(): void
    {
        $this->db->exec("
            CREATE TABLE IF NOT EXISTS users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(50) NOT NULL UNIQUE,
                email VARCHAR(100) NOT NULL UNIQUE,
                password VARCHAR(255) NOT NULL,
                role_id INT NULL,
                status ENUM('active', 'inactive', 'banned') DEFAULT 'active',
                last_login TIMESTAMP NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // Insert default Super Admin and Admin users
        $hash = password_hash('password', PASSWORD_BCRYPT, ['cost' => 12]);
        $this->db->exec("
            INSERT INTO roles (id, name, description) VALUES (1, 'Super Admin', 'System Administrator') ON DUPLICATE KEY UPDATE name=name;
            INSERT INTO users (username, email, password, role_id) VALUES ('superadmin', 'superadmin@example.com', '{$hash}', 1) ON DUPLICATE KEY UPDATE username=username;
            
            INSERT INTO roles (id, name, description) VALUES (2, 'Admin', 'ผู้ดูแลระบบ (บางส่วน)') ON DUPLICATE KEY UPDATE name=name;
            INSERT INTO users (username, email, password, role_id) VALUES ('admin', 'admin@example.com', '{$hash}', 2) ON DUPLICATE KEY UPDATE username=username;
        ");
    }

    public function down(): void
    {
        $this->db->exec("DROP TABLE IF EXISTS users");
    }
}
