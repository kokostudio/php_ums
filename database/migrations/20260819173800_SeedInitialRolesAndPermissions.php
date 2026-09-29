<?php

use App\Core\Migration;

class SeedInitialRolesAndPermissions extends Migration
{
    public function up(): void
    {
        // Insert basic modules and actions
        $permissions = [
            ['module' => 'users', 'action' => 'view', 'description' => 'View users list'],
            ['module' => 'users', 'action' => 'create', 'description' => 'Create new user'],
            ['module' => 'users', 'action' => 'edit', 'description' => 'Edit user data'],
            ['module' => 'users', 'action' => 'delete', 'description' => 'Delete user'],
            ['module' => 'roles', 'action' => 'view', 'description' => 'View roles and permissions'],
            ['module' => 'roles', 'action' => 'create', 'description' => 'Create new role'],
            ['module' => 'roles', 'action' => 'edit', 'description' => 'Edit role and permissions'],
            ['module' => 'roles', 'action' => 'delete', 'description' => 'Delete role'],
            ['module' => 'settings', 'action' => 'view', 'description' => 'View system settings'],
            ['module' => 'settings', 'action' => 'edit', 'description' => 'Edit system settings'],
        ];

        foreach ($permissions as $perm) {
            $this->db->exec("INSERT IGNORE INTO permissions (module, action, description) VALUES ('{$perm['module']}', '{$perm['action']}', '{$perm['description']}')");
        }

        // Insert Super Admin
        $this->db->exec("INSERT IGNORE INTO roles (id, name, description) VALUES (1, 'Super Admin', 'Has access to all system features.')");

        // The Super Admin usually bypasses all checks via RBAC.php (we implemented id = 1 bypass),
        // But let's also bind all permissions just for completeness if needed.
        $this->db->exec("
            INSERT IGNORE INTO role_permissions (role_id, permission_id) 
            SELECT 1, id FROM permissions
        ");

        // Grant some permissions to Admin (role 2) - e.g., view users, create users, edit users, view roles, view settings
        // IDs: 1, 2, 3, 5, 9
        $adminPerms = [1, 2, 3, 5, 9];
        foreach ($adminPerms as $permId) {
            $this->db->exec("INSERT IGNORE INTO role_permissions (role_id, permission_id) VALUES (2, $permId)");
        }
    }

    public function down(): void
    {
        // Don't reverse the seed on down
    }
}
