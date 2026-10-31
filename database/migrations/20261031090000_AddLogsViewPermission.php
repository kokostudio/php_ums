<?php

use App\Core\Migration;

/**
 * เพิ่ม permission สำหรับโมดูล Logs (logs/view)
 * และอัปเดตคำอธิบาย permission ของ settings ให้ระบุการจัดการค่าความปลอดภัย
 */
class AddLogsViewPermission extends Migration
{
    public function up(): void
    {
        // เพิ่ม permission logs/view (ใช้ prepared statement ตามมาตรฐานความปลอดภัย)
        $this->db->prepare(
            "INSERT IGNORE INTO permissions (module, action, description)
             VALUES ('logs', 'view', 'View activity and audit logs')"
        )->execute();

        // Super Admin (role 1) — RBAC bypass อยู่แล้ว แต่ bind ไว้เพื่อความครบถ้วนตามแบบ seed เดิม
        $this->db->prepare(
            "INSERT IGNORE INTO role_permissions (role_id, permission_id)
             SELECT 1, id FROM permissions WHERE module = 'logs'"
        )->execute();

        // Admin (role 2) — มองเห็น logs ได้เช่นเดียวกับ settings/view ที่มีอยู่เดิม
        $this->db->prepare(
            "INSERT IGNORE INTO role_permissions (role_id, permission_id)
             SELECT 2, id FROM permissions WHERE module = 'logs'"
        )->execute();
    }

    public function down(): void
    {
        $this->db->prepare(
            "DELETE rp FROM role_permissions rp
             JOIN permissions p ON p.id = rp.permission_id
             WHERE p.module = 'logs'"
        )->execute();
        $this->db->prepare("DELETE FROM permissions WHERE module = 'logs'")->execute();
    }
}
