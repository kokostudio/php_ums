<?php

use App\Core\Migration;

class CreateSystemSettingsTable extends Migration
{
    public function up(): void
    {
        $this->db->exec("
            CREATE TABLE IF NOT EXISTS system_settings (
                setting_key VARCHAR(50) PRIMARY KEY,
                setting_value TEXT NULL,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // Insert default settings
        $this->db->exec("
            INSERT INTO system_settings (setting_key, setting_value) VALUES 
            ('site_name', 'Standard Core System'),
            ('site_logo', '/assets/img/default_logo.png'),
            ('site_favicon', '/assets/img/favicon.ico'),
            ('site_footer', '© 2026 Standard Core System. All rights reserved.')
            ON DUPLICATE KEY UPDATE setting_key=setting_key;
        ");
    }

    public function down(): void
    {
        $this->db->exec("DROP TABLE IF EXISTS system_settings");
    }
}
