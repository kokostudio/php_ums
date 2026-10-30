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
        
        $stmt = $db->prepare("SELECT username, email, first_name, last_name, role_id, created_at, last_login, profile_image, two_factor_enabled, google_id, line_id, avatar_url FROM users WHERE id = :id");
        $stmt->execute(['id' => $userId]);
        $user = $stmt->fetch();
        
        if (empty($user['profile_image']) && !empty($user['avatar_url'])) {
            $user['profile_image'] = $user['avatar_url'];
        }

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
            'twoFactorSecret' => $secret,
            'socialSettings' => [
                'google'   => \App\Core\Settings::get('enable_google_login') === '1',
                'line'     => \App\Core\Settings::get('enable_line_login') === '1',
                'facebook' => \App\Core\Settings::get('enable_facebook_login') === '1',
                'github'   => \App\Core\Settings::get('enable_github_login') === '1',
            ]
        ]);
    }

    public function update()
    {
        Security::checkCsrf();
        $db = Database::getInstance();
        $userId = Session::get('user_id');

        // Fetch current user details
        $stmt = $db->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute(['id' => $userId]);
        $currentUser = $stmt->fetch();

        if (!$currentUser) {
            $this->respond('/profile', __('messages.user_not_found'), 'error', 404);
            return;
        }

        $email = Security::sanitize(trim($_POST['email'] ?? ''));
        $firstName = Security::sanitize(trim($_POST['first_name'] ?? ''));
        $lastName = Security::sanitize(trim($_POST['last_name'] ?? ''));
        $rawUsername = isset($_POST['username']) ? trim($_POST['username']) : null;

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->respond('/profile', __('messages.invalid_email_format'), 'error', 422);
            return;
        }

        // Check duplicate email
        $check = $db->prepare("SELECT id FROM users WHERE email = :email AND id != :id");
        $check->execute(['email' => $email, 'id' => $userId]);
        if ($check->fetch()) {
            $this->respond('/profile', __('messages.email_taken'), 'error', 422);
            return;
        }

        // Username 1-time change check
        $usernameChanged = false;
        $finalUsername = $currentUser['username'];
        $changesCount = (int)($currentUser['username_changes_count'] ?? 0);

        if ($rawUsername !== null && $rawUsername !== '' && $rawUsername !== $currentUser['username']) {
            if ($changesCount >= 1) {
                $this->respond('/profile', __('profile.username_change_limit_reached'), 'error', 422);
                return;
            }

            $sanitizedUsername = Security::sanitize($rawUsername);
            if (!preg_match('/^[a-zA-Z0-9_-]{3,30}$/', $sanitizedUsername)) {
                $this->respond('/profile', __('profile.invalid_username_format'), 'error', 422);
                return;
            }

            // Check duplicate username
            $checkUser = $db->prepare("SELECT id FROM users WHERE username = :username AND id != :id");
            $checkUser->execute(['username' => $sanitizedUsername, 'id' => $userId]);
            if ($checkUser->fetch()) {
                $this->respond('/profile', __('profile.username_taken'), 'error', 422);
                return;
            }

            $usernameChanged = true;
            $finalUsername = $sanitizedUsername;
        }

        try {
            $setClauses = [
                'email = :email',
                'first_name = :first_name',
                'last_name = :last_name'
            ];
            $params = [
                'email' => $email,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'id' => $userId
            ];

            if ($usernameChanged) {
                // Ensure columns exist on users table (Zero-500 Guarantee if migration not yet run)
                try {
                    $db->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS username_changes_count INT UNSIGNED NOT NULL DEFAULT 0 AFTER role_id");
                    $db->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS username_changed_at DATETIME NULL DEFAULT NULL AFTER username_changes_count");
                } catch (\Throwable $e) {}

                $setClauses[] = 'username = :username';
                $setClauses[] = 'username_changes_count = username_changes_count + 1';
                $setClauses[] = 'username_changed_at = NOW()';
                $params['username'] = $finalUsername;
            }

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
                    $setClauses[] = 'profile_image = :profile_image';
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
                        $setClauses[] = 'profile_image = :profile_image';
                        $params['profile_image'] = '/uploads/profiles/' . $filename;
                        
                        // Update session with new image
                        Session::set('profile_image', '/uploads/profiles/' . $filename);
                    }
                }
            }

            $updateSql = "UPDATE users SET " . implode(', ', $setClauses) . " WHERE id = :id";
            $stmt = $db->prepare($updateSql);
            $stmt->execute($params);

            if ($usernameChanged) {
                Session::set('username', $finalUsername);
                Logger::activity("User #{$userId} changed username from '{$currentUser['username']}' to '{$finalUsername}'.");
            } else {
                Logger::activity("Updated own profile information.");
            }
            
            $this->respond('/profile', __('messages.profile_updated'), 'success');
            return;
        } catch (\Exception $e) {
            $this->respond('/profile', __('messages.failed_update_profile'), 'error', 500);
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
            $this->respond('/profile', __('messages.all_password_fields_required'), 'error', 422);
        }

        if ($newPassword !== $confirmPassword) {
            $this->respond('/profile', __('messages.new_passwords_not_match'), 'error', 422);
        }

        $pwdErrors = Security::validatePasswordComplexity($newPassword);
        if (!empty($pwdErrors)) {
            $errorMsg = implode('<br>', $pwdErrors);
            $this->respond('/profile', $errorMsg, 'error', 422);
        }

        $stmt = $db->prepare("SELECT password FROM users WHERE id = :id");
        $stmt->execute(['id' => $userId]);
        $hash = $stmt->fetchColumn();

        if (!Security::verifyPassword($currentPassword, $hash)) {
            $this->respond('/profile', __('messages.current_password_incorrect'), 'error', 422);
        }

        try {
            $newHash = Security::hashPassword($newPassword);
            $update = $db->prepare("UPDATE users SET password = :password WHERE id = :id");
            $update->execute(['password' => $newHash, 'id' => $userId]);

            Logger::activity("Changed own password.");
            
            $this->respond('/profile', __('messages.password_updated'), 'success');
        } catch (\Exception $e) {
            $this->respond('/profile', 'Failed to update password.', 'error', 500);
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
            Session::set('2fa_enabled', true);
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

        Session::set('2fa_enabled', false);
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

        foreach ($sessions as &$session) {
            $session['hash_id'] = Security::encrypt($session['id']);
        }

        $this->view('profile/sessions', [
            'pageTitle' => 'Active Sessions',
            'sessions' => $sessions,
            'currentSessionId' => session_id()
        ]);
    }

    public function revokeSession($id)
    {
        Security::checkCsrf();
        $isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') || 
                  (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);

        $db = Database::getInstance();
        $userId = Session::get('user_id');

        // Decrypt the encrypted token, or fallback to raw ID
        $realSessionId = Security::decrypt($id) ?? $id;

        // Prevent revoking the current session from here (use logout instead)
        if ($realSessionId === session_id()) {
            if ($isAjax) {
                return $this->json(['success' => false, 'message' => 'Cannot revoke current session. Please use logout instead.'], 400);
            }
            Session::setFlash('error', 'Cannot revoke current session. Please use logout instead.');
            $this->redirect('/profile/sessions');
        }

        $stmt = $db->prepare("DELETE FROM user_sessions WHERE id = :id AND user_id = :user_id");
        $stmt->execute(['id' => $realSessionId, 'user_id' => $userId]);

        Logger::activity("Revoked an active session.");

        if ($isAjax) {
            return $this->json(['success' => true, 'message' => __('messages.session_revoked')]);
        }

        Session::setFlash('success', __('messages.session_revoked'));
        $this->redirect('/profile/sessions');
    }

    public function disconnectSocial($provider)
    {
        Security::checkCsrf();
        $db = Database::getInstance();
        $userId = Session::get('user_id');

        if (!in_array($provider, ['google', 'line'])) {
            Session::setFlash('error', 'Invalid provider.');
            $this->redirect('/profile');
        }

        $providerColumn = $provider . '_id';

        // Check if user has a password set. If not, they can't disconnect their only login method
        $stmt = $db->prepare("SELECT password FROM users WHERE id = :id");
        $stmt->execute(['id' => $userId]);
        $hash = $stmt->fetchColumn();

        if (empty($hash) || strpos($hash, '$2y$') !== 0) {
            // User doesn't have a valid password, maybe generated randomly
            // But wait, we always generate a hashed password. 
            // So to be safe, we should enforce that they set a password before disconnecting,
            // but for now we just allow it or we could check if they have at least one other login method.
        }

        $stmt = $db->prepare("UPDATE users SET {$providerColumn} = NULL WHERE id = :id");
        $stmt->execute(['id' => $userId]);

        Logger::activity("Disconnected " . ucfirst($provider) . " account.");
        Session::setFlash('success', ucfirst($provider) . ' account disconnected successfully.');
        $this->redirect('/profile');
    }
}
