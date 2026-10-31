<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Security;
use App\Core\Session;
use App\Core\Logger;
use App\Core\UploadManager;
use App\Core\Validator;
use App\Core\DataTableHelper;

class UserController extends Controller
{
    public function index()
    {
        $db = Database::getInstance();
        $roleWhere = \App\Core\Session::get('role_id') != 1 ? "WHERE id != 1" : "";
        $roles = $db->query("SELECT id, name FROM roles $roleWhere ORDER BY id ASC")->fetchAll();
        
        $userWhere = \App\Core\Session::get('role_id') != 1 ? "WHERE u.role_id != 1" : "";
        $baseQuery = "SELECT u.id, u.username, u.email, u.status, u.created_at, u.profile_image, r.name as role_name FROM users u LEFT JOIN roles r ON u.role_id = r.id $userWhere ORDER BY u.id DESC";
        $users = $db->query($baseQuery)->fetchAll();
        foreach ($users as &$row) {
            $row['hash_id'] = Security::encodeId((int)$row['id']);
        }

        $this->view('users/index', [
            'pageTitle' => 'User Management',
            'roles' => $roles,
            'users' => $users
        ]);
    }

    /**
     * DataTables Server-Side Processing API
     */
    public function data()
    {
        $db = Database::getInstance();
        
        $baseQuery = "SELECT u.id, u.username, u.email, u.status, u.created_at, u.profile_image, r.name as role_name FROM users u LEFT JOIN roles r ON u.role_id = r.id";
        
        $searchableColumns = ['u.username', 'u.email', 'r.name'];
        $orderableColumns = [
            0 => 'u.id',
            1 => 'u.username',
            2 => 'u.email',
            3 => 'r.name',
            4 => 'u.status',
            5 => 'u.created_at'
        ];

        $extraWhere = [];
        $extraParams = [];

        if (\App\Core\Session::get('role_id') != 1) {
            $extraWhere[] = "u.role_id != 1";
        }

        if (!empty($_GET['role_filter'])) {
            $extraWhere[] = "u.role_id = :role_filter";
            $extraParams['role_filter'] = $_GET['role_filter'];
        }
        if (!empty($_GET['status_filter'])) {
            $extraWhere[] = "u.status = :status_filter";
            $extraParams['status_filter'] = $_GET['status_filter'];
        }

        $baseCountQuery = \App\Core\Session::get('role_id') != 1 ? "SELECT COUNT(id) FROM users WHERE role_id != 1" : "SELECT COUNT(id) FROM users";

        $result = DataTableHelper::process(
            $db,
            $_GET,
            $baseQuery,
            $searchableColumns,
            $orderableColumns,
            'u.id',
            'DESC',
            $extraWhere,
            $extraParams,
            $baseCountQuery
        );

        // Encode IDs for the frontend
        foreach ($result['data'] as &$row) {
            $row['hash_id'] = Security::encodeId((int)$row['id']);
        }

        $this->json($result);
    }

    public function create()
    {
        $db = Database::getInstance();
        $roleWhere = \App\Core\Session::get('role_id') != 1 ? "WHERE id != 1" : "";
        $roles = $db->query("SELECT id, name FROM roles $roleWhere ORDER BY name")->fetchAll();
        $this->view('users/create', ['pageTitle' => 'Add New User', 'roles' => $roles]);
    }

    public function store()
    {
        Security::checkCsrf();
        $db = Database::getInstance();

        $errors = Validator::validate($_POST, [
            'username' => 'required|alpha_dash',
            'email' => 'required|email',
        ]);

        $password = $_POST['password'] ?? '';
        if (empty($password)) {
            $errors['password'][] = "The password field is required.";
        } else {
            $pwdErrors = Security::validatePasswordComplexity($password);
            if (!empty($pwdErrors)) {
                $errors['password'] = $pwdErrors;
            }
        }

        if (!empty($errors)) {
            $errorMsg = implode('<br>', array_map(fn($e) => implode('<br>', $e), $errors));
            $this->respond('/users/create', $errorMsg, 'error', 422, ['errors' => $errors]);
        }

        $username = Security::sanitize($_POST['username']);
        $email = Security::sanitize($_POST['email']);
        $password = $_POST['password'];
        $roleIdHash = $_POST['role_id'] ?? null;
        $status = Security::sanitize($_POST['status'] ?? 'active');

        $roleId = $roleIdHash ? Security::decodeId($roleIdHash) : null;

        $avatarName = null;
        try {
            $avatarName = UploadManager::handle($_FILES['avatar'] ?? [], __DIR__ . '/../../public/uploads/avatars/');
        } catch (\Exception $e) {
            $this->respond('/users/create', $e->getMessage(), 'error', 422);
        }

        // Check unique
        $stmt = $db->prepare("SELECT id FROM users WHERE username = :username OR email = :email");
        $stmt->execute(['username' => $username, 'email' => $email]);
        if ($stmt->fetch()) {
            $this->respond('/users/create', __('messages.user_exists'), 'error', 422);
        }

        $hashedPassword = Security::hashPassword($password);

        if ($roleId == 1 && Session::get('role_id') != 1) {
            $this->respond('/users/create', 'You cannot assign the Super Admin role.', 'error', 403);
        }

        try {
            $stmt = $db->prepare("INSERT INTO users (username, email, password, role_id, status, profile_image) VALUES (:username, :email, :password, :role_id, :status, :profile_image)");
            $stmt->execute([
                'username' => $username,
                'email' => $email,
                'password' => $hashedPassword,
                'role_id' => $roleId,
                'status' => $status,
                'profile_image' => $avatarName
            ]);
            
            $newId = $db->lastInsertId();
            Logger::activity("Created user: {$username}");
            Logger::audit('users', $newId, 'insert', null, ['username' => $username, 'email' => $email, 'role_id' => $roleId]);

            // AJAX: อยู่หน้าเดิม + Toast แจ้งบันทึก + เคลียร์ฟอร์มรอรับข้อมูลใหม่ | Fallback: กลับมาหน้าฟอร์มเปล่า
            $this->respond('/users/create', __('messages.user_created'), 'success', 200, ['stay' => true, 'reset_form' => true]);
        } catch (\Exception $e) {
            $this->respond('/users/create', __('messages.failed_create_user'), 'error', 500);
        }
    }

    public function edit($id)
    {
        $realId = Security::decodeId($id);
        if (!$realId) {
            Session::setFlash('error', __('messages.invalid_user_id'));
            $this->redirect('/users');
        }

        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute(['id' => $realId]);
        $user = $stmt->fetch();

        if (!$user) {
            Session::setFlash('error', __('messages.user_not_found'));
            $this->redirect('/users');
        }

        if (in_array(strtolower($user['username']), ['admin', 'superadmin']) || $user['role_id'] == 1) {
            if (Session::get('role_id') != 1 && Session::get('user_id') != $user['id']) {
                Session::setFlash('error', __('messages.cannot_edit_superadmin') ?? 'System administrators cannot be edited by lower roles.');
                $this->redirect('/users');
            }
        }

        $roleWhere = \App\Core\Session::get('role_id') != 1 ? "WHERE id != 1" : "";
        $roles = $db->query("SELECT id, name FROM roles $roleWhere ORDER BY name")->fetchAll();
        $this->view('users/edit', ['pageTitle' => 'Edit User', 'user' => $user, 'roles' => $roles]);
    }

    public function update($id)
    {
        Security::checkCsrf();
        $realId = Security::decodeId($id);
        if (!$realId) {
            Session::setFlash('error', __('messages.invalid_user_id'));
            $this->redirect('/users');
        }

        $db = Database::getInstance();

        $stmt = $db->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute(['id' => $realId]);
        $oldUser = $stmt->fetch();

        if (!$oldUser) {
            $this->redirect('/users');
        }

        if (in_array(strtolower($oldUser['username']), ['admin', 'superadmin']) || $oldUser['role_id'] == 1) {
            if (Session::get('role_id') != 1 && Session::get('user_id') != $oldUser['id']) {
                $this->respond('/users', __('messages.cannot_edit_superadmin') ?? 'System administrators cannot be edited by lower roles.', 'error', 403);
            }
        }

        $errors = Validator::validate($_POST, [
            'username' => 'required|alpha_dash',
            'email' => 'required|email',
        ]);

        if (!empty($errors)) {
            $this->respond("/users/{$id}/edit", __('messages.validation_failed') ?? 'Please correct the errors and try again.', 'error', 422, ['errors' => $errors]);
        }

        $username = Security::sanitize($_POST['username']);
        $email = Security::sanitize($_POST['email']);
        $password = $_POST['password'] ?? '';
        if (!empty($password)) {
            $pwdErrors = Security::validatePasswordComplexity($password);
            if (!empty($pwdErrors)) {
                // ส่ง errors รายฟิลด์ตาม contract — ห้าม setFlash+redirect (ฟอร์มเป็น data-ajax จะได้ toast สำเร็จปลอม)
                $this->respond("/users/{$id}/edit", __('messages.password_requirements_not_met'), 'error', 422, ['errors' => ['password' => $pwdErrors]]);
            }
        }
        $roleIdHash = $_POST['role_id'] ?? null;
        $status = Security::sanitize($_POST['status'] ?? 'active');

        $roleId = $roleIdHash ? Security::decodeId($roleIdHash) : null;

        if ($roleId == 1 && Session::get('role_id') != 1) {
            $this->respond("/users/{$id}/edit", 'You cannot assign the Super Admin role.', 'error', 403);
        }

        $avatarName = $oldUser['profile_image'] ?? null;
        try {
            $newAvatarName = UploadManager::handle($_FILES['avatar'] ?? [], __DIR__ . '/../../public/uploads/avatars/');
            if ($newAvatarName) {
                // Delete old avatar if exists
                if (!empty($oldUser['profile_image']) && file_exists(__DIR__ . '/../../public/uploads/avatars/' . $oldUser['profile_image'])) {
                    unlink(__DIR__ . '/../../public/uploads/avatars/' . $oldUser['profile_image']);
                }
                $avatarName = $newAvatarName;
            }
        } catch (\Exception $e) {
            $this->respond("/users/{$id}/edit", $e->getMessage(), 'error', 422);
        }

        // Check unique (excluding current user)
        $checkStmt = $db->prepare("SELECT id FROM users WHERE (username = :username OR email = :email) AND id != :id");
        $checkStmt->execute(['username' => $username, 'email' => $email, 'id' => $realId]);
        if ($checkStmt->fetch()) {
            $this->respond("/users/{$id}/edit", __('messages.user_email_exists_other'), 'error', 422);
        }

        $query = "UPDATE users SET username = :username, email = :email, role_id = :role_id, status = :status, profile_image = :profile_image";
        $params = [
            'username' => $username,
            'email' => $email,
            'role_id' => $roleId,
            'status' => $status,
            'profile_image' => $avatarName,
            'id' => $realId
        ];

        // Only update password if provided
        if (!empty($_POST['password'])) {
            $query .= ", password = :password";
            $params['password'] = Security::hashPassword($_POST['password']);
        }

        $query .= " WHERE id = :id";

        $stmtUpdate = $db->prepare($query);
        $stmtUpdate->execute($params);

        // Fetch new values for audit log
        $stmtCheck = $db->prepare("SELECT * FROM users WHERE id = :id");
        $stmtCheck->execute(['id' => $realId]);
        $newUser = $stmtCheck->fetch();

        // Audit & Activity Logs
        // Unset password hashes from audit logs for security
        unset($oldUser['password'], $newUser['password']);
        Logger::audit('users', $realId, 'update', $oldUser, $newUser);
        Logger::activity("Updated user profile: {$username}");

        // AJAX: อยู่หน้าเดิม + Toast แจ้งบันทึกสำเร็จ | Fallback: PRG กลับมาหน้าเดิม
        $this->respond("/users/{$id}/edit", __('messages.user_updated'), 'success', 200, ['stay' => true]);
    }

    public function delete($id)
    {
        Security::checkCsrf();
        $realId = Security::decodeId($id);
        if (!$realId) {
            $this->respond('/users', __('messages.invalid_user_id'), 'error', 400);
        }

        $db = Database::getInstance();

        if ($realId == Session::get('user_id')) {
            $this->respond('/users', __('messages.cannot_delete_self'), 'error', 403);
        }

        // Get user for audit
        $stmt = $db->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute(['id' => $realId]);
        $oldUser = $stmt->fetch();

        if ($oldUser) {
            if (in_array(strtolower($oldUser['username']), ['admin', 'superadmin']) || $oldUser['role_id'] == 1) {
                $this->respond('/users', __('messages.cannot_delete_superadmin'), 'error', 403);
            }

            unset($oldUser['password']);
            $db->prepare("DELETE FROM users WHERE id = :id")->execute(['id' => $realId]);

            Logger::audit('users', $realId, 'delete', $oldUser, null);
            Logger::activity("Deleted user: {$oldUser['username']}");

            // respond(): AJAX ได้ JSON {success, message} + non-AJAX ได้ redirect + flash (ตาม Rule 8)
            $this->respond('/users', __('messages.user_deleted'), 'success', 200, ['reload_table' => true]);
        } else {
            $this->respond('/users', __('messages.user_not_found'), 'error', 404);
        }
    }
}
