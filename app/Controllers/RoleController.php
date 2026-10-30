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
        $whereClause = Session::get('role_id') != 1 ? "WHERE r.id != 1" : "";
        $roles = $db->query("
            SELECT r.*, COUNT(u.id) as users_count 
            FROM roles r 
            LEFT JOIN users u ON r.id = u.role_id 
            $whereClause
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
            $this->respond('/roles/create', __('messages.role_name_required'), 'error', 422);
        }

        $stmt = $db->prepare("SELECT id FROM roles WHERE name = :name");
        $stmt->execute(['name' => $name]);
        if ($stmt->fetch()) {
            $this->respond('/roles/create', __('messages.role_exists'), 'error', 422);
        }

        try {
            $db->beginTransaction();

            $stmt = $db->prepare("INSERT INTO roles (name, description) VALUES (:name, :desc)");
            $stmt->execute(['name' => $name, 'desc' => $description]);
            $roleId = $db->lastInsertId();

            if (!empty($perms) && is_array($perms)) {
                $insertPerms = $db->prepare("INSERT INTO role_permissions (role_id, permission_id) VALUES (:role, :perm)");
                foreach ($perms as $permIdHash) {
                    $permId = is_numeric($permIdHash) ? (int)$permIdHash : Security::decodeId($permIdHash);
                    if ($permId) {
                        $insertPerms->execute(['role' => $roleId, 'perm' => $permId]);
                    }
                }
            }

            $db->commit();
            Logger::activity("Created role: {$name}");
            Logger::audit('roles', $roleId, 'insert', null, ['name' => $name, 'description' => $description, 'permissions' => $perms]);

            // AJAX: อยู่หน้าเดิม + Toast แจ้งบันทึก + เคลียร์ฟอร์มรอรับข้อมูลใหม่ | Fallback: กลับมาหน้าฟอร์มเปล่า
            $this->respond('/roles/create', __('messages.role_created'), 'success', 200, ['stay' => true, 'reset_form' => true]);
        } catch (\Exception $e) {
            $db->rollBack();
            $this->respond('/roles/create', __('messages.failed_create_role') . ' ' . $e->getMessage(), 'error', 500);
        }
    }

    public function edit($id)
    {
        $realId = Security::decodeId($id);
        if (!$realId) {
            Session::setFlash('error', __('messages.invalid_role_id'));
            $this->redirect('/roles');
        }

        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM roles WHERE id = :id");
        $stmt->execute(['id' => $realId]);
        $role = $stmt->fetch();

        if (!$role) {
            Session::setFlash('error', __('messages.role_not_found'));
            $this->redirect('/roles');
        }

        if ($realId == 1 && Session::get('role_id') != 1) {
            Session::setFlash('error', __('messages.cannot_edit_superadmin') ?? 'Access denied.');
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
            Session::setFlash('error', __('messages.invalid_role_id'));
            $this->redirect('/roles');
        }

        $db = Database::getInstance();

        if ($realId == 1 && Session::get('role_id') != 1) {
            Session::setFlash('error', __('messages.cannot_edit_superadmin') ?? 'Access denied.');
            $this->redirect('/roles');
        }

        $name = Security::sanitize($_POST['name'] ?? '');
        $description = Security::sanitize($_POST['description'] ?? '');
        $perms = $_POST['permissions'] ?? [];

        if (empty($name)) {
            $this->respond("/roles/{$id}/edit", __('messages.role_name_required'), 'error', 422);
        }

        if ($realId == 1 && $name !== 'Super Admin') {
            $this->respond("/roles/{$id}/edit", __('messages.cannot_rename_superadmin'), 'error', 403);
        }

        $checkStmt = $db->prepare("SELECT id FROM roles WHERE name = :name AND id != :id");
        $checkStmt->execute(['name' => $name, 'id' => $realId]);
        if ($checkStmt->fetch()) {
            $this->respond("/roles/{$id}/edit", __('messages.role_exists'), 'error', 422);
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
                    foreach ($perms as $permIdHash) {
                        $permId = is_numeric($permIdHash) ? (int)$permIdHash : Security::decodeId($permIdHash);
                        if ($permId) {
                            $insertPerms->execute(['role' => $realId, 'perm' => $permId]);
                        }
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

            // AJAX: อยู่หน้าเดิม + Toast แจ้งบันทึกสำเร็จ | Fallback: PRG กลับมาหน้าเดิม
            $this->respond("/roles/{$id}/edit", __('messages.role_updated'), 'success', 200, ['stay' => true]);
        } catch (\Exception $e) {
            $db->rollBack();
            $this->respond("/roles/{$id}/edit", __('messages.failed_update_role'), 'error', 500);
        }
    }

    public function delete($id)
    {
        Security::checkCsrf();
        $realId = Security::decodeId($id);

        $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'
               || (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)
               || (isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false);

        if (!$realId) {
            if ($isAjax) {
                return $this->json(['success' => false, 'message' => __('messages.invalid_role_id')], 400);
            }
            Session::setFlash('error', __('messages.invalid_role_id'));
            return $this->redirect('/roles');
        }

        $db = Database::getInstance();

        if ($realId == 1) {
            $msg = 'Cannot delete the Super Admin role.';
            if ($isAjax) {
                return $this->json(['success' => false, 'message' => $msg], 403);
            }
            Session::setFlash('error', $msg);
            return $this->redirect('/roles');
        }

        $stmt = $db->prepare("SELECT COUNT(*) FROM users WHERE role_id = :id");
        $stmt->execute(['id' => $realId]);
        if ($stmt->fetchColumn() > 0) {
            $msg = 'Cannot delete this role because there are users assigned to it.';
            if ($isAjax) {
                return $this->json(['success' => false, 'message' => $msg], 403);
            }
            Session::setFlash('error', $msg);
            return $this->redirect('/roles');
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

                if ($isAjax) {
                    return $this->json(['success' => true, 'message' => 'Role deleted successfully.']);
                }
                Session::setFlash('success', 'Role deleted successfully.');
                return $this->redirect('/roles');
            } else {
                $db->rollBack();
                if ($isAjax) {
                    return $this->json(['success' => false, 'message' => __('messages.role_not_found')], 404);
                }
                Session::setFlash('error', __('messages.role_not_found'));
                return $this->redirect('/roles');
            }
        } catch (\Exception $e) {
            $db->rollBack();
            if ($isAjax) {
                return $this->json(['success' => false, 'message' => 'Failed to delete role.'], 500);
            }
            Session::setFlash('error', 'Failed to delete role.');
            return $this->redirect('/roles');
        }
    }
}
