<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Security;
use App\Core\Session;
use App\Core\Logger;

class ProfileController extends Controller
{
    public function index()
    {
        $db = Database::getInstance();
        $userId = Session::get('user_id');
        
        $stmt = $db->prepare("SELECT username, email, role_id, created_at, last_login, profile_image FROM users WHERE id = :id");
        $stmt->execute(['id' => $userId]);
        $user = $stmt->fetch();

        $stmtRole = $db->prepare("SELECT name FROM roles WHERE id = :id");
        $stmtRole->execute(['id' => $user['role_id']]);
        $roleName = $stmtRole->fetchColumn() ?: 'No Role';

        $this->view('profile/index', [
            'pageTitle' => 'My Profile',
            'user' => $user,
            'roleName' => $roleName
        ]);
    }

    public function update()
    {
        Security::checkCsrf();
        $db = Database::getInstance();
        $userId = Session::get('user_id');

        $email = Security::sanitize($_POST['email'] ?? '');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Session::setFlash('error', 'Invalid email format.');
            $this->redirect('/profile');
        }

        // Check duplicate email
        $check = $db->prepare("SELECT id FROM users WHERE email = :email AND id != :id");
        $check->execute(['email' => $email, 'id' => $userId]);
        if ($check->fetch()) {
            Session::setFlash('error', 'Email is already taken by another user.');
            $this->redirect('/profile');
        }

        try {
            $updateSql = "UPDATE users SET email = :email WHERE id = :id";
            $params = ['email' => $email, 'id' => $userId];

            // Handle Profile Image Upload
            if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
                $file = $_FILES['profile_image'];
                $uploadDir = __DIR__ . '/../../public/uploads/profiles/';
                
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                // Simple validation
                $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                $fileMimeType = mime_content_type($file['tmp_name']);

                if (in_array($fileMimeType, $allowedMimeTypes)) {
                    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                    $fileName = 'profile_' . $userId . '_' . time() . '.' . $extension;
                    $targetFilePath = $uploadDir . $fileName;

                    if (move_uploaded_file($file['tmp_name'], $targetFilePath)) {
                        $profileImageUrl = '/uploads/profiles/' . $fileName;
                        
                        $updateSql = "UPDATE users SET email = :email, profile_image = :profile_image WHERE id = :id";
                        $params['profile_image'] = $profileImageUrl;
                        
                        // Update session
                        Session::set('profile_image', $profileImageUrl);
                    }
                }
            }

            $stmt = $db->prepare($updateSql);
            $stmt->execute($params);

            Logger::activity("Updated own profile information.");
            
            Session::setFlash('success', 'Profile updated successfully.');
            $this->redirect('/profile');
        } catch (\Exception $e) {
            Session::setFlash('error', 'Failed to update profile.');
            $this->redirect('/profile');
        }
    }

    public function updatePassword()
    {
        Security::checkCsrf();
        $db = Database::getInstance();
        $userId = Session::get('user_id');

        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
            Session::setFlash('error', 'All password fields are required.');
            $this->redirect('/profile');
        }

        if ($newPassword !== $confirmPassword) {
            Session::setFlash('error', 'New passwords do not match.');
            $this->redirect('/profile');
        }

        $stmt = $db->prepare("SELECT password FROM users WHERE id = :id");
        $stmt->execute(['id' => $userId]);
        $hash = $stmt->fetchColumn();

        if (!Security::verifyPassword($currentPassword, $hash)) {
            Session::setFlash('error', 'Current password is incorrect.');
            $this->redirect('/profile');
        }

        try {
            $newHash = Security::hashPassword($newPassword);
            $update = $db->prepare("UPDATE users SET password = :password WHERE id = :id");
            $update->execute(['password' => $newHash, 'id' => $userId]);

            Logger::activity("Changed own password.");
            
            Session::setFlash('success', 'Password updated successfully. Next time you login, please use your new password.');
            $this->redirect('/profile');
        } catch (\Exception $e) {
            Session::setFlash('error', 'Failed to update password.');
            $this->redirect('/profile');
        }
    }
}
