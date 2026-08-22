<?php

namespace App\Core;

class RBAC
{
    /**
     * Check if the current user has a specific permission
     */
    public static function hasPermission(string $module, string $action): bool
    {
        $roleId = Session::get('role_id');
        
        if (!$roleId) {
            return false;
        }

        // If it's a Super Admin (id=1), grant all permissions automatically
        if ($roleId == 1) {
            return true;
        }

        // To optimize, we can cache this in the session.
        // But for strict security, we'll fetch from DB (or use Redis in the future).
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT p.id 
            FROM permissions p
            JOIN role_permissions rp ON p.id = rp.permission_id
            WHERE rp.role_id = :role_id 
              AND p.module = :module 
              AND p.action = :action
            LIMIT 1
        ");
        
        $stmt->execute([
            'role_id' => $roleId,
            'module' => $module,
            'action' => $action
        ]);

        return (bool) $stmt->fetchColumn();
    }
}
