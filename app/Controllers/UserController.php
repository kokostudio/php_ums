<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Security;
use App\Core\Session;
use App\Core\Logger;

class UserController extends Controller
{
    public function index()
    {
        $db = Database::getInstance();
        $roles = $db->query("SELECT id, name FROM roles ORDER BY id ASC")->fetchAll();
        $this->view('users/index', [
            'pageTitle' => 'User Management',
            'roles' => $roles
        ]);
    }

    /**
     * DataTables Server-Side Processing API
     */
    public function data()
    {
        $db = Database::getInstance();
        
        $draw = $_GET['draw'] ?? 1;
        $start = $_GET['start'] ?? 0;
        $length = $_GET['length'] ?? 10;
        $searchValue = $_GET['search']['value'] ?? '';

        $params = [];
        $whereConditions = [];

        if (!empty($searchValue)) {
            $whereConditions[] = "(u.username LIKE :search1 OR u.email LIKE :search2 OR r.name LIKE :search3)";
            $params['search1'] = "%{$searchValue}%";
            $params['search2'] = "%{$searchValue}%";
            $params['search3'] = "%{$searchValue}%";
        }

        // Custom Filters
        if (!empty($_GET['role_filter'])) {
            $whereConditions[] = "u.role_id = :role_filter";
            $params['role_filter'] = $_GET['role_filter'];
        }
        if (!empty($_GET['status_filter'])) {
            $whereConditions[] = "u.status = :status_filter";
            $params['status_filter'] = $_GET['status_filter'];
        }

        $whereClause = "";
        if (!empty($whereConditions)) {
            $whereClause = "WHERE " . implode(" AND ", $whereConditions);
        }

        // Sorting
        $columns = [
            0 => 'u.id',
            1 => 'u.username',
            2 => 'u.email',
            3 => 'r.name',
            4 => 'u.status',
            5 => 'u.created_at'
        ];
        
        $orderColumnIndex = $_GET['order'][0]['column'] ?? 0;
        $orderDir = $_GET['order'][0]['dir'] ?? 'desc';
        $orderColumn = $columns[$orderColumnIndex] ?? 'u.id';
        $orderDir = strtolower($orderDir) === 'asc' ? 'ASC' : 'DESC';

        // Total count without filtering
        $totalRecords = $db->query("SELECT COUNT(id) FROM users")->fetchColumn();

        // Total count with filtering
        $totalFilteredQuery = "SELECT COUNT(u.id) FROM users u LEFT JOIN roles r ON u.role_id = r.id {$whereClause}";
        $stmtFiltered = $db->prepare($totalFilteredQuery);
        $stmtFiltered->execute($params);
        $totalFiltered = $stmtFiltered->fetchColumn();

        // Fetch paginated data
        $query = "
            SELECT u.id, u.username, u.email, u.status, u.created_at, r.name as role_name
            FROM users u
            LEFT JOIN roles r ON u.role_id = r.id
            {$whereClause}
            ORDER BY {$orderColumn} {$orderDir}
            LIMIT :start, :length
        ";
        
        $stmt = $db->prepare($query);
        foreach ($params as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }
        $stmt->bindValue(':start', (int)$start, \PDO::PARAM_INT);
        $stmt->bindValue(':length', (int)$length, \PDO::PARAM_INT);
        $stmt->execute();
        
        $data = $stmt->fetchAll();

        // Encode IDs for the frontend
        foreach ($data as &$row) {
            $row['hash_id'] = Security::encodeId((int)$row['id']);
        }

        $this->json([
            "draw" => intval($draw),
            "recordsTotal" => intval($totalRecords),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $data
        ]);
    }

    public function create()
    {
        $db = Database::getInstance();
        $roles = $db->query("SELECT id, name FROM roles ORDER BY name")->fetchAll();
        $this->view('users/create', ['pageTitle' => 'Add New User', 'roles' => $roles]);
    }

    public function store()
    {
        Security::checkCsrf();
        $db = Database::getInstance();

        $username = Security::sanitize($_POST['username'] ?? '');
        $email = Security::sanitize($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $roleIdHash = $_POST['role_id'] ?? null;
        $status = Security::sanitize($_POST['status'] ?? 'active');

        $roleId = $roleIdHash ? Security::decodeId($roleIdHash) : null;

        // Basic Validation
        if (empty($username) || empty($email) || empty($password)) {
            Session::setFlash('error', 'All fields are required.');
            $this->redirect('/users/create');
        }

        // Check unique
        $stmt = $db->prepare("SELECT id FROM users WHERE username = :username OR email = :email");
        $stmt->execute(['username' => $username, 'email' => $email]);
        if ($stmt->fetch()) {
            Session::setFlash('error', 'Username or Email already exists.');
            $this->redirect('/users/create');
        }

        $hashedPassword = Security::hashPassword($password);

        try {
            $stmt = $db->prepare("INSERT INTO users (username, email, password, role_id, status) VALUES (:username, :email, :password, :role_id, :status)");
            $stmt->execute([
                'username' => $username,
                'email' => $email,
                'password' => $hashedPassword,
                'role_id' => $roleId,
                'status' => $status
            ]);
            
            $newId = $db->lastInsertId();
            Logger::activity("Created user: {$username}");
            Logger::audit('users', $newId, 'insert', null, ['username' => $username, 'email' => $email, 'role_id' => $roleId]);

            Session::setFlash('success', 'User created successfully.');
            $this->redirect('/users');
        } catch (\Exception $e) {
            Session::setFlash('error', 'Failed to create user. Please try again.');
            $this->redirect('/users/create');
        }
    }

    public function edit($id)
    {
        $realId = Security::decodeId($id);
        if (!$realId) {
            Session::setFlash('error', 'Invalid user ID.');
            $this->redirect('/users');
        }

        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute(['id' => $realId]);
        $user = $stmt->fetch();

        if (!$user) {
            Session::setFlash('error', 'User not found.');
            $this->redirect('/users');
        }

        $roles = $db->query("SELECT id, name FROM roles ORDER BY name")->fetchAll();
        $this->view('users/edit', ['pageTitle' => 'Edit User', 'user' => $user, 'roles' => $roles]);
    }

    public function update($id)
    {
        Security::checkCsrf();
        $realId = Security::decodeId($id);
        if (!$realId) {
            Session::setFlash('error', 'Invalid user ID.');
            $this->redirect('/users');
        }

        $db = Database::getInstance();

        $stmt = $db->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute(['id' => $realId]);
        $oldUser = $stmt->fetch();

        if (!$oldUser) {
            $this->redirect('/users');
        }

        $username = Security::sanitize($_POST['username'] ?? '');
        $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'] ?? '';
        $roleIdHash = $_POST['role_id'] ?? null;
        $status = Security::sanitize($_POST['status'] ?? 'active');

        $roleId = $roleIdHash ? Security::decodeId($roleIdHash) : null;

        // Check unique (excluding current user)
        $checkStmt = $db->prepare("SELECT id FROM users WHERE (username = :username OR email = :email) AND id != :id");
        $checkStmt->execute(['username' => $username, 'email' => $email, 'id' => $realId]);
        if ($checkStmt->fetch()) {
            Session::setFlash('error', 'Username or Email already exists for another user.');
            $this->redirect("/users/{$id}/edit");
        }

        $query = "UPDATE users SET username = :username, email = :email, role_id = :role_id, status = :status";
        $params = [
            'username' => $username,
            'email' => $email,
            'role_id' => $roleId,
            'status' => $status,
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

        Session::setFlash('success', 'User updated successfully.');
        $this->redirect('/users');
    }

    public function delete($id)
    {
        Security::checkCsrf();
        $realId = Security::decodeId($id);
        if (!$realId) {
            $this->json(['status' => 'error', 'message' => 'Invalid user ID.'], 400);
        }

        $db = Database::getInstance();

        if ($realId == Session::get('user_id')) {
            $this->json(['success' => false, 'message' => 'You cannot delete yourself.'], 403);
        }

        // Get user for audit
        $stmt = $db->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute(['id' => $realId]);
        $oldUser = $stmt->fetch();

        if ($oldUser) {
            unset($oldUser['password']);
            $db->prepare("DELETE FROM users WHERE id = :id")->execute(['id' => $realId]);
            
            Logger::audit('users', $realId, 'delete', $oldUser, null);
            Logger::activity("Deleted user: {$oldUser['username']}");
            
            $this->json(['success' => true, 'message' => 'User deleted successfully.']);
        } else {
            $this->json(['success' => false, 'message' => 'User not found.'], 404);
        }
    }
}
