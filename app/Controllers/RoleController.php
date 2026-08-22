<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Security;
use App\Core\Session;
use App\Core\Logger;

class RoleController extends Controller
{
    public function index()
    {
        $db = Database::getInstance();
        $roles = $db->query("
            SELECT r.*, COUNT(u.id) as users_count 
            FROM roles r 
            LEFT JOIN users u ON r.id = u.role_id 
            GROUP BY r.id 
            ORDER BY r.id ASC
        ")->fetchAll();

        foreach ($roles as &$role) {
            $role['hash_id'] = Security::encodeId((int)$role['id']);
        }

        $this->view('roles/index', ['pageTitle' => 'Role & Permission Management', 'roles' => $roles]);
    }

    private function getGroupedPermissions()
    {
        $db = Database::getInstance();
        $permissions = $db->query("SELECT * FROM permissions ORDER BY module, id")->fetchAll();
        $grouped = [];
        foreach ($permissions as $p) {
            $grouped[$p['module']][] = $p;
        }
        return $grouped;
    }

    public function create()
    {
        $this->view('roles/create', [
            'pageTitle' => 'Create New Role',
            'permissions' => $this->getGroupedPermissions()
        ]);
    }

    public function store()
    {
        Security::checkCsrf();
        $db = Database::getInstance();

        $name = Security::sanitize($_POST['name'] ?? '');
        $description = Security::sanitize($_POST['description'] ?? '');
        $perms = $_POST['permissions'] ?? [];

        if (empty($name)) {
            Session::setFlash('error', 'Role name is required.');
            $this->redirect('/roles/create');
        }

        $stmt = $db->prepare("SELECT id FROM roles WHERE name = :name");
        $stmt->execute(['name' => $name]);
        if ($stmt->fetch()) {
            Session::setFlash('error', 'Role name already exists.');
            $this->redirect('/roles/create');
        }

        try {
            $db->beginTransaction();

            $stmt = $db->prepare("INSERT INTO roles (name, description) VALUES (:name, :desc)");
            $stmt->execute(['name' => $name, 'desc' => $description]);
            $roleId = $db->lastInsertId();

            if (!empty($perms) && is_array($perms)) {
                $insertPerms = $db->prepare("INSERT INTO role_permissions (role_id, permission_id) VALUES (:role, :perm)");
                foreach ($perms as $permId) {
                    $insertPerms->execute(['role' => $roleId, 'perm' => $permId]);
                }
            }

            $db->commit();
            Logger::activity("Created role: {$name}");
            Logger::audit('roles', $roleId, 'insert', null, ['name' => $name, 'description' => $description, 'permissions' => $perms]);

            Session::setFlash('success', 'Role created successfully.');
            $this->redirect('/roles');
        } catch (\Exception $e) {
            $db->rollBack();
            Session::setFlash('error', 'Failed to create role: ' . $e->getMessage());
            $this->redirect('/roles/create');
        }
    }

    public function edit($id)
    {
        $realId = Security::decodeId($id);
        if (!$realId) {
            Session::setFlash('error', 'Invalid role ID.');
            $this->redirect('/roles');
        }

        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM roles WHERE id = :id");
        $stmt->execute(['id' => $realId]);
        $role = $stmt->fetch();

        if (!$role) {
            Session::setFlash('error', 'Role not found.');
            $this->redirect('/roles');
        }

        $stmtPerms = $db->prepare("SELECT permission_id FROM role_permissions WHERE role_id = :id");
        $stmtPerms->execute(['id' => $realId]);
        $assignedPerms = $stmtPerms->fetchAll(\PDO::FETCH_COLUMN);

        $this->view('roles/edit', [
            'pageTitle' => 'Edit Role',
            'role' => $role,
            'assignedPerms' => $assignedPerms,
            'permissions' => $this->getGroupedPermissions()
        ]);
    }

    public function update($id)
    {
        Security::checkCsrf();
        $realId = Security::decodeId($id);
        if (!$realId) {
            Session::setFlash('error', 'Invalid role ID.');
            $this->redirect('/roles');
        }

        $db = Database::getInstance();

        $name = Security::sanitize($_POST['name'] ?? '');
        $description = Security::sanitize($_POST['description'] ?? '');
        $perms = $_POST['permissions'] ?? [];

        if (empty($name)) {
            Session::setFlash('error', 'Role name is required.');
            $this->redirect("/roles/{$id}/edit");
        }

        if ($realId == 1 && $name !== 'Super Admin') {
            Session::setFlash('error', 'Cannot rename the Super Admin role.');
            $this->redirect("/roles/{$id}/edit");
        }

        $checkStmt = $db->prepare("SELECT id FROM roles WHERE name = :name AND id != :id");
        $checkStmt->execute(['name' => $name, 'id' => $realId]);
        if ($checkStmt->fetch()) {
            Session::setFlash('error', 'Role name already exists.');
            $this->redirect("/roles/{$id}/edit");
        }

        try {
            $db->beginTransaction();
            
            $stmt = $db->prepare("SELECT * FROM roles WHERE id = :id");
            $stmt->execute(['id' => $realId]);
            $oldRole = $stmt->fetch();

            $stmt = $db->prepare("UPDATE roles SET name = :name, description = :desc WHERE id = :id");
            $stmt->execute(['name' => $name, 'desc' => $description, 'id' => $realId]);

            // Only update permissions if it's not the Super Admin
            if ($realId != 1) {
                $db->prepare("DELETE FROM role_permissions WHERE role_id = :id")->execute(['id' => $realId]);
            
                if (!empty($perms) && is_array($perms)) {
                    $insertPerms = $db->prepare("INSERT INTO role_permissions (role_id, permission_id) VALUES (:role, :perm)");
                    foreach ($perms as $permId) {
                        $insertPerms->execute(['role' => $realId, 'perm' => $permId]);
                    }
                }
            }
            
            // Enforce Super Admin rules
            if ($realId == 1) {
                $db->prepare("DELETE FROM role_permissions WHERE role_id = 1")->execute();
                $db->prepare("INSERT INTO role_permissions (role_id, permission_id) SELECT 1, id FROM permissions")->execute();
            }

            $db->commit();
            Logger::activity("Updated role: {$name}");
            Logger::audit('roles', $realId, 'update', ['name' => $oldRole['name'], 'description' => $oldRole['description']], ['name' => $name, 'description' => $description, 'permissions' => $perms]);

            Session::setFlash('success', 'Role updated successfully.');
            $this->redirect('/roles');
        } catch (\Exception $e) {
            $db->rollBack();
            Session::setFlash('error', 'Failed to update role.');
            $this->redirect("/roles/{$id}/edit");
        }
    }

    public function delete($id)
    {
        Security::checkCsrf();
        $realId = Security::decodeId($id);
        if (!$realId) {
            $this->json(['status' => 'error', 'message' => 'Invalid role ID.'], 400);
            return;
        }

        $db = Database::getInstance();

        if ($realId == 1) {
            $this->json(['success' => false, 'message' => 'Cannot delete the Super Admin role.'], 403);
            return;
        }

        $stmt = $db->prepare("SELECT COUNT(*) FROM users WHERE role_id = :id");
        $stmt->execute(['id' => $realId]);
        if ($stmt->fetchColumn() > 0) {
            $this->json(['success' => false, 'message' => 'Cannot delete this role because there are users assigned to it.'], 403);
            return;
        }

        try {
            $db->beginTransaction();

            $stmt = $db->prepare("SELECT * FROM roles WHERE id = :id");
            $stmt->execute(['id' => $realId]);
            $oldRole = $stmt->fetch();

            if ($oldRole) {
                $db->prepare("DELETE FROM role_permissions WHERE role_id = :id")->execute(['id' => $realId]);
                $db->prepare("DELETE FROM roles WHERE id = :id")->execute(['id' => $realId]);

                $db->commit();

                Logger::activity("Deleted role ID: {$realId}");
                Logger::audit('roles', $realId, 'delete', $oldRole, null);
                $this->json(['success' => true, 'message' => 'Role deleted successfully.']);
            } else {
                $db->rollBack();
                $this->json(['success' => false, 'message' => 'Role not found.'], 404);
            }
        } catch (\Exception $e) {
            $db->rollBack();
            $this->json(['success' => false, 'message' => 'Failed to delete role.'], 500);
        }
    }
}
