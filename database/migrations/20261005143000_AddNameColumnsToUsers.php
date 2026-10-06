<?php

use App\Core\Migration;

class AddNameColumnsToUsers extends Migration
{
    public function up(): void
    {
        // Add first_name and last_name if they don't exist
        try {
            $this->db->exec("ALTER TABLE users ADD COLUMN first_name VARCHAR(100) NULL AFTER username");
        } catch (\PDOException $e) {
            // Ignore if already exists
        }
        
        try {
            $this->db->exec("ALTER TABLE users ADD COLUMN last_name VARCHAR(100) NULL AFTER first_name");
        } catch (\PDOException $e) {
            // Ignore if already exists
        }
    }

    public function down(): void
    {
        try {
            $this->db->exec("ALTER TABLE users DROP COLUMN first_name");
            $this->db->exec("ALTER TABLE users DROP COLUMN last_name");
        } catch (\PDOException $e) {
            // Ignore
        }
    }
}
