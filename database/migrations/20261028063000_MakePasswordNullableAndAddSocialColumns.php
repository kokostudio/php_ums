<?php

use App\Core\Migration;

class MakePasswordNullableAndAddSocialColumns extends Migration
{
    public function up(): void
    {
        // 1. Make password nullable for OAuth/Social login users
        try {
            $this->db->exec("ALTER TABLE users MODIFY COLUMN password VARCHAR(255) NULL DEFAULT NULL");
        } catch (\Exception $e) {
            // Ignore if already nullable
        }

        // 2. Add facebook_id and github_id columns if they don't exist
        try {
            $cols = $this->db->query("SHOW COLUMNS FROM users LIKE 'facebook_id'")->fetchAll(\PDO::FETCH_COLUMN);
            if (empty($cols)) {
                $this->db->exec("ALTER TABLE users ADD COLUMN facebook_id VARCHAR(255) NULL AFTER line_id");
            }
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            $cols = $this->db->query("SHOW COLUMNS FROM users LIKE 'github_id'")->fetchAll(\PDO::FETCH_COLUMN);
            if (empty($cols)) {
                $this->db->exec("ALTER TABLE users ADD COLUMN github_id VARCHAR(255) NULL AFTER facebook_id");
            }
        } catch (\Exception $e) {
            // Ignore
        }
    }

    public function down(): void
    {
        try {
            $this->db->exec("ALTER TABLE users MODIFY COLUMN password VARCHAR(255) NOT NULL");
        } catch (\Exception $e) {}

        try {
            $this->db->exec("ALTER TABLE users DROP COLUMN facebook_id, DROP COLUMN github_id");
        } catch (\Exception $e) {}
    }
}
