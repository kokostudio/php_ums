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
                $setClauses[] = 'username = :username';
                $setClauses[] = 'username_changes_count = username_changes_count + 1';
                $setClauses[] = 'username_changed_at = NOW()';
                $params['username'] = $finalUsername;
            }

            // Handle Cropped Profile Image Upload (Base64) — ผ่าน UploadManager
            // (ตรวจ magic bytes ของข้อมูลจริง + จำกัดขนาด + สร้างชื่อไฟล์สุ่ม)
            try {
                if (!empty($_POST['cropped_image'])) {
                    $uploadDir = __DIR__ . '/../../public/uploads/profiles/';
                    $filename = \App\Core\UploadManager::handleBase64Image(
                        (string) $_POST['cropped_image'],
                        $uploadDir,
                        'profile_' . $userId . '_'
                    );
                    $setClauses[] = 'profile_image = :profile_image';
                    $params['profile_image'] = '/uploads/profiles/' . $filename;
                    Session::set('profile_image', '/uploads/profiles/' . $filename);
                }
                // Handle regular Profile Image Upload (Fallback) — ผ่าน UploadManager
                elseif (isset($_FILES['profile_image'])) {
                    $uploadDir = __DIR__ . '/../../public/uploads/profiles/';
                    $filename = \App\Core\UploadManager::handle(
                        $_FILES['profile_image'],
                        $uploadDir,
                        ['jpg', 'jpeg', 'png', 'gif', 'webp']
                    );

                    if ($filename !== null) {
                        $setClauses[] = 'profile_image = :profile_image';
                        $params['profile_image'] = '/uploads/profiles/' . $filename;
                        // Update session with new image
                        Session::set('profile_image', '/uploads/profiles/' . $filename);
                    }
                }
            } catch (\Exception $e) {
                // ข้อผิดพลาดของไฟล์อัปโหลด — แสดงสาเหตุจริงให้ผู้ใช้เห็น (ข้อความ i18n จาก UploadManager)
                $this->respond('/profile', $e->getMessage(), 'error', 422);
                return;
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
            \App\Core\Logger::error('Failed to update profile: ' . $e->getMessage());
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
            $this->respond('/profile', __('messages.invalid_2fa_code'), 'error', 422, ['errors' => ['totp_code' => [__('messages.invalid_2fa_code')]]]);
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
            // redirect กลับหน้าโปรไฟล์เพื่อซ่อน QR/แสดงสถานะเปิดใช้งาน (AJAX ได้ toast ต่อเนื่องที่ปลายทาง)
            $this->respond('/profile', __('messages.2fa_enabled'), 'success');
        }

        $this->respond('/profile', __('messages.invalid_2fa_code'), 'error', 422, ['errors' => ['totp_code' => [__('messages.invalid_2fa_code')]]]);
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
            $this->respond('/profile', __('messages.incorrect_password_2fa'), 'error', 403, ['errors' => ['password' => [__('messages.incorrect_password_2fa')]]]);
        }

        $stmt = $db->prepare("UPDATE users SET two_factor_secret = NULL, two_factor_enabled = 0 WHERE id = :id");
        $stmt->execute(['id' => $userId]);

        Session::set('2fa_enabled', false);
        Logger::activity("Disabled Two-Factor Authentication.");
        // redirect กลับมาหน้าโปรไฟล์เพื่อ render สถานะ 2FA ใหม่ (AJAX ได้ toast ต่อเนื่องที่ปลายทาง)
        $this->respond('/profile', __('messages.2fa_disabled'), 'success');
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
        $isAjax = \App\Core\Request::wantsJson();

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

        // รองรับครบทั้ง 4 ผู้ให้บริการ (เดิมมีเพียง google/line ทำให้ปุ่ม Facebook/GitHub กดแล้วไม่ตัดการเชื่อมต่อ)
        if (!in_array($provider, ['google', 'line', 'facebook', 'github'], true)) {
            $this->respond('/profile', __('messages.invalid_provider'), 'error', 422);
        }

        $providerColumn = $provider . '_id';

        // ตรวจว่ายังมีวิธีเข้าสู่ระบบเหลืออยู่: ต้องมีรหัสผ่าน หรือบัญชีโซเชียลอื่นที่ยังเชื่อมต่อ
        $stmt = $db->prepare("SELECT password, google_id, line_id, facebook_id, github_id FROM users WHERE id = :id");
        $stmt->execute(['id' => $userId]);
        $user = $stmt->fetch();

        $hasPassword = !empty($user['password']) && str_starts_with((string) $user['password'], '$2y$');
        $otherSocials = ['google_id', 'line_id', 'facebook_id', 'github_id'];
        $connectedOthers = 0;
        foreach ($otherSocials as $col) {
            if ($col !== $providerColumn && !empty($user[$col])) {
                $connectedOthers++;
            }
        }

        if (!$hasPassword && $connectedOthers === 0) {
            $this->respond('/profile', __('messages.cannot_disconnect_last_method'), 'error', 422);
        }

        $stmt = $db->prepare("UPDATE users SET {$providerColumn} = NULL WHERE id = :id");
        $stmt->execute(['id' => $userId]);

        Logger::activity("Disconnected " . ucfirst($provider) . " account.");
        $this->respond('/profile', __('messages.provider_disconnected', ['provider' => ucfirst($provider)]), 'success');
    }
}
