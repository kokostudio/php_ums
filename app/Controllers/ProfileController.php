<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Security;
use App\Core\Session;
use App\Core\Logger;
use PragmaRX\Google2FA\Google2FA;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class ProfileController extends Controller
{
    public function index()
    {
        $db = Database::getInstance();
        $userId = Session::get('user_id');
        
        $stmt = $db->prepare("SELECT username, email, role_id, created_at, last_login, profile_image, two_factor_enabled FROM users WHERE id = :id");
        $stmt->execute(['id' => $userId]);
        $user = $stmt->fetch();

        $stmtRole = $db->prepare("SELECT name FROM roles WHERE id = :id");
        $stmtRole->execute(['id' => $user['role_id']]);
        $roleName = $stmtRole->fetchColumn() ?: 'No Role';

        // 2FA Setup
        $qrCodeSvg = '';
        $secret = '';
        if (!$user['two_factor_enabled']) {
            $google2fa = new Google2FA();
            $secret = Session::get('2fa_secret');
            if (!$secret) {
                $secret = $google2fa->generateSecretKey();
                Session::set('2fa_secret', $secret);
            }

            $siteName = \App\Core\Settings::get('site_name') ?: 'PHP UMS';
            $qrCodeUrl = $google2fa->getQRCodeUrl($siteName, $user['email'], $secret);

            $renderer = new ImageRenderer(
                new RendererStyle(200),
                new SvgImageBackEnd()
            );
            $writer = new Writer($renderer);
            $qrCodeSvg = $writer->writeString($qrCodeUrl);
        }

        $this->view('profile/index', [
            'pageTitle' => 'My Profile',
            'user' => $user,
            'roleName' => $roleName,
            'qrCodeSvg' => $qrCodeSvg,
            'twoFactorSecret' => $secret
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

            // Handle Cropped Profile Image Upload (Base64)
            if (!empty($_POST['cropped_image'])) {
                $uploadDir = __DIR__ . '/../../public/uploads/profiles/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                $base64Image = $_POST['cropped_image'];
                list($type, $data) = explode(';', $base64Image);
                list(, $data)      = explode(',', $data);
                $data = base64_decode($data);
                
                // Determine extension
                $ext = 'png';
                if (str_contains($type, 'jpeg') || str_contains($type, 'jpg')) $ext = 'jpg';
                elseif (str_contains($type, 'webp')) $ext = 'webp';
                
                $filename = 'profile_' . $userId . '_' . time() . '.' . $ext;
                $targetPath = $uploadDir . $filename;

                if (file_put_contents($targetPath, $data)) {
                    $updateSql = "UPDATE users SET email = :email, profile_image = :profile_image WHERE id = :id";
                    $params['profile_image'] = '/uploads/profiles/' . $filename;
                    Session::set('profile_image', '/uploads/profiles/' . $filename);
                }
            }
            // Handle regular Profile Image Upload (Fallback)
            elseif (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
                $file = $_FILES['profile_image'];
                $uploadDir = __DIR__ . '/../../public/uploads/profiles/';
                
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                // Simple validation
                $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                $fileMimeType = mime_content_type($file['tmp_name']);

                if (in_array($fileMimeType, $allowedMimeTypes)) {
                    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                    $filename = 'profile_' . $userId . '_' . time() . '.' . $ext;
                    $targetPath = $uploadDir . $filename;

                    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                        $updateSql = "UPDATE users SET email = :email, profile_image = :profile_image WHERE id = :id";
                        $params['profile_image'] = '/uploads/profiles/' . $filename;
                        
                        // Update session with new image
                        Session::set('profile_image', '/uploads/profiles/' . $filename);
                    }
                }
            }

            $stmt = $db->prepare($updateSql);
            $stmt->execute($params);

            Logger::activity("Updated own profile information.");
            
            Session::setFlash('success', __('messages.profile_updated'));
            $this->redirect('/profile');
        } catch (\Exception $e) {
            Session::setFlash('error', __('messages.failed_update_profile'));
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
            Session::setFlash('error', __('messages.current_password_incorrect'));
            $this->redirect('/profile');
        }

        try {
            $newHash = Security::hashPassword($newPassword);
            $update = $db->prepare("UPDATE users SET password = :password WHERE id = :id");
            $update->execute(['password' => $newHash, 'id' => $userId]);

            Logger::activity("Changed own password.");
            
            Session::setFlash('success', __('messages.password_updated'));
            $this->redirect('/profile');
        } catch (\Exception $e) {
            Session::setFlash('error', 'Failed to update password.');
            $this->redirect('/profile');
        }
    }

    public function enable2fa()
    {
        Security::checkCsrf();
        $code = $_POST['totp_code'] ?? '';
        $secret = Session::get('2fa_secret');

        if (!$secret || empty($code)) {
            Session::setFlash('error', 'Invalid request.');
            $this->redirect('/profile');
        }

        $google2fa = new Google2FA();
        $valid = $google2fa->verifyKey($secret, $code);

        if ($valid) {
            $db = Database::getInstance();
            $stmt = $db->prepare("UPDATE users SET two_factor_secret = :secret, two_factor_enabled = 1 WHERE id = :id");
            $stmt->execute(['secret' => $secret, 'id' => Session::get('user_id')]);
            
            Session::remove('2fa_secret');
            Logger::activity("Enabled Two-Factor Authentication.");
            Session::setFlash('success', __('messages.2fa_enabled'));
        } else {
            Session::setFlash('error', 'Invalid verification code. Please try again.');
        }

        $this->redirect('/profile');
    }

    public function disable2fa()
    {
        Security::checkCsrf();
        $password = $_POST['password'] ?? '';

        $db = Database::getInstance();
        $userId = Session::get('user_id');

        $stmt = $db->prepare("SELECT password FROM users WHERE id = :id");
        $stmt->execute(['id' => $userId]);
        $hash = $stmt->fetchColumn();

        if (!Security::verifyPassword($password, $hash)) {
            Session::setFlash('error', 'Incorrect password. Cannot disable 2FA.');
            $this->redirect('/profile');
        }

        $stmt = $db->prepare("UPDATE users SET two_factor_secret = NULL, two_factor_enabled = 0 WHERE id = :id");
        $stmt->execute(['id' => $userId]);

        Logger::activity("Disabled Two-Factor Authentication.");
        Session::setFlash('success', __('messages.2fa_disabled'));
        $this->redirect('/profile');
    }

    public function sessions()
    {
        $db = Database::getInstance();
        $userId = Session::get('user_id');

        $stmt = $db->prepare("SELECT id, ip_address, user_agent, last_activity FROM user_sessions WHERE user_id = :id ORDER BY last_activity DESC");
        $stmt->execute(['id' => $userId]);
        $sessions = $stmt->fetchAll();

        $this->view('profile/sessions', [
            'pageTitle' => 'Active Sessions',
            'sessions' => $sessions,
            'currentSessionId' => session_id()
        ]);
    }

    public function revokeSession($sessionId)
    {
        Security::checkCsrf();
        $db = Database::getInstance();
        $userId = Session::get('user_id');

        // Prevent revoking the current session from here (use logout instead)
        if ($sessionId === session_id()) {
            Session::setFlash('error', 'Cannot revoke current session. Please use logout instead.');
            $this->redirect('/profile/sessions');
        }

        $stmt = $db->prepare("DELETE FROM user_sessions WHERE id = :id AND user_id = :user_id");
        $stmt->execute(['id' => $sessionId, 'user_id' => $userId]);

        Logger::activity("Revoked active session: {$sessionId}");
        Session::setFlash('success', __('messages.session_revoked'));
        $this->redirect('/profile/sessions');
    }
}
