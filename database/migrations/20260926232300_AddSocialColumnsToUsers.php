<?php

use App\Core\Migration;

class AddSocialColumnsToUsers extends Migration
{
    public function up(): void
    {
        // Add columns if they don't exist
        $this->db->exec("
            ALTER TABLE users 
            ADD COLUMN google_id VARCHAR(255) NULL AFTER two_factor_enabled,
            ADD COLUMN line_id VARCHAR(255) NULL AFTER google_id,
            ADD COLUMN avatar_url VARCHAR(255) NULL AFTER line_id;
        ");
        
        // Optionally drop old columns if they exist, ignoring errors if they don't
        try {
            $this->db->exec("ALTER TABLE users DROP COLUMN provider");
        } catch (\Exception $e) {}
        try {
            $this->db->exec("ALTER TABLE users DROP COLUMN provider_id");
        } catch (\Exception $e) {}
        try {
            $this->db->exec("ALTER TABLE users DROP COLUMN avatar");
        } catch (\Exception $e) {}
    }

    public function down(): void
    {
        $this->db->exec("
            ALTER TABLE users 
            DROP COLUMN google_id,
            DROP COLUMN line_id,
            DROP COLUMN avatar_url;
        ");
    }
}
