<?php

use App\Core\Migration;

class AddUsernameChangesCountToUsersTable extends Migration
{
    public function up(): void
    {
        try {
            $cols = $this->db->query("SHOW COLUMNS FROM users LIKE 'username_changes_count'")->fetchAll(\PDO::FETCH_COLUMN);
            if (empty($cols)) {
                $this->db->exec("ALTER TABLE users ADD COLUMN username_changes_count INT NOT NULL DEFAULT 0 AFTER username");
            }
        } catch (\Exception $e) {
            // Ignore if already exists
        }

        try {
            $cols = $this->db->query("SHOW COLUMNS FROM users LIKE 'username_changed_at'")->fetchAll(\PDO::FETCH_COLUMN);
            if (empty($cols)) {
                $this->db->exec("ALTER TABLE users ADD COLUMN username_changed_at TIMESTAMP NULL DEFAULT NULL AFTER username_changes_count");
            }
        } catch (\Exception $e) {
            // Ignore if already exists
        }
    }

    public function down(): void
    {
        try {
            $this->db->exec("ALTER TABLE users DROP COLUMN username_changes_count, DROP COLUMN username_changed_at");
        } catch (\Exception $e) {}
    }
}
