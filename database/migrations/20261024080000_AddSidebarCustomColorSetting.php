<?php

use App\Core\Migration;

class AddSidebarCustomColorSetting extends Migration
{
    public function up(): void
    {
        // sidebar_theme รองรับค่า 'custom' เพิ่มเติมจาก preset เดิม
        $this->db->exec("
            INSERT INTO system_settings (setting_key, setting_value) VALUES
            ('sidebar_custom_color', '#1E1B4B')
            ON DUPLICATE KEY UPDATE setting_key = setting_key;
        ");

        // ค่าเริ่มต้นของ sidebar_theme ยังคงเป็น preset เดิม เพื่อไม่กระทบผู้ใช้ที่มีอยู่
        $this->db->exec("
            INSERT INTO system_settings (setting_key, setting_value) VALUES
            ('sidebar_theme', 'slate')
            ON DUPLICATE KEY UPDATE setting_key = setting_key;
        ");
    }

    public function down(): void
    {
        $this->db->exec("DELETE FROM system_settings WHERE setting_key = 'sidebar_custom_color'");
    }
}
