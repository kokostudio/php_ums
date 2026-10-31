<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Security;
use App\Core\Session;
use App\Core\Logger;
use App\Core\Mailer;
use PragmaRX\Google2FA\Google2FA;
use App\Core\Settings;

class AuthController extends Controller
{
    public function showLogin()
    {
        Session::init();
        
        if (Session::has('user_id')) {
            $this->redirect('/dashboard');
        }

        $this->view('auth/login', ['pageTitle' => 'Login'], 'auth');
    }

    public function login()
    {
        Session::init();
        Security::checkCsrf();

        if (\App\Core\Settings::get('enable_username_login', '1') === '0') {
            $this->respond('/login', __('messages.username_login_disabled'), 'error', 403);
            return;
        }

        $username = Security::sanitize($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($username) || empty($password)) {
            $errors = [];
            if (empty($username)) {
                $errors['username'] = [__('auth.username_required')];
            }
            if (empty($password)) {
                $errors['password'] = [__('auth.password_required')];
            }
            Session::setFlash('old_username', $username);
            $this->respond('/login', __('messages.enter_user_pass'), 'error', 422, ['errors' => $errors]);
        }

        $db = Database::getInstance();
        try {
            $stmt = $db->prepare("SELECT * FROM users WHERE username = :username LIMIT 1");
            $stmt->execute(['username' => $username]);
            $user = $stmt->fetch();
        } catch (\Throwable $e) {
            Logger::error('Login query failed: ' . $e->getMessage());
            $this->respond('/login', __('common.unexpected_error'), 'error', 500);
        }

        // กัน timing-based username enumeration: ถ้าไม่เจอ user ให้ verify กับ hash หลอก
        // เพื่อให้เวลาตอบสนองใกล้เคียงกรณีเจอ user จริง
        if (!$user) {
            Security::verifyPassword($password, '$2y$12$K0t3STUdI0.Dummy.Hash.For.Timing.Xxxxxxxxxxxxxxxxxxxxxxxx');
        }

        if ($user && Security::verifyPassword($password, $user['password'])) {
            if ($user['status'] !== 'active') {
                Logger::activity("Failed login attempt (Account {$user['status']}) for user: {$username}");
                Session::setFlash('old_username', $username);
                $this->respond('/login', __('messages.account_disabled'), 'error', 403);
            }

            // Check email verification
            $requireVerification = Settings::get('require_email_verification', '0') === '1';
            if ($requireVerification && empty($user['email_verified_at'])) {
                Logger::activity("Failed login attempt (Unverified email) for user: {$username}");
                Session::setFlash('unverified_email', $user['email']);
                Session::setFlash('old_username', $username);
                $this->respond('/login', __('messages.email_not_verified'), 'error', 403, ['unverified_email' => $user['email']]);
            }

            // Check if 2FA is enabled
            if ($user['two_factor_enabled']) {
                Session::set('2fa_pending_user_id', $user['id']);
                Session::set('2fa_pending_expires', time() + 300); // หมดอายุใน 5 นาที
                $this->respond('/login/2fa', __('common.please_wait'), 'info');
            }

            // Regenerate session ID for security against fixation
            session_regenerate_id(true);

            // Login success
            Session::set('user_id', $user['id']);
            Session::set('username', $user['username']);
            Session::set('role_id', $user['role_id']);
            if (!empty($user['profile_image'])) {
                Session::set('profile_image', $user['profile_image']);
            }

            // Update last login
            $updateStmt = $db->prepare("UPDATE users SET last_login = NOW() WHERE id = :id");
            $updateStmt->execute(['id' => $user['id']]);

            // Reset rate limiter attempts on success
            \App\Core\RateLimiter::reset('/login');

            Logger::activity("User logged in successfully.");

            $this->respond('/dashboard', __('common.welcome'), 'success');
        } else {
            Logger::activity("Failed login attempt (Invalid credentials) for user: {$username}");
            Session::setFlash('old_username', $username);
            $this->respond('/login', __('messages.invalid_credentials'), 'error', 401, ['errors' => ['password' => [__('messages.invalid_credentials')]]]);
        }
    }

    public function logout()
    {
        Session::init();
        if (Session::has('user_id')) {
            Logger::activity("User logged out.");
        }
        Session::destroy();
        Session::init(); // Start a new session to hold flash message
        Session::setFlash('success', __('messages.logged_out'));
        $this->redirect('/login');
    }

    public function showRegister()
    {
        if (\App\Core\Settings::get('enable_registration') === '0') {
            Session::init();
            Session::setFlash('error', __('messages.registration_disabled') ?? __('messages.registration_disabled_text'));
            $this->redirect('/login');
        }

        if (Session::get('user_id')) {
            $this->redirect('/dashboard');
        }
        $this->view('auth/register', ['title' => __('auth.register') ?? 'Register']);
    }

    public function register()
    {
        if (\App\Core\Settings::get('enable_registration') === '0') {
            Session::init();
            $this->respond('/login', __('messages.registration_disabled') ?? __('messages.registration_disabled_text'), 'error', 403);
        }

        Security::checkCsrf();

        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
            $this->respond('/register', __('messages.all_fields_required'), 'error', 422);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->respond('/register', __('messages.invalid_email'), 'error', 422, ['errors' => ['email' => [__('messages.invalid_email')]]]);
        }

        if ($password !== $confirm_password) {
            $this->respond('/register', __('messages.password_mismatch'), 'error', 422, ['errors' => ['confirm_password' => [__('messages.password_mismatch')]]]);
        }

        // บังคับนโยบายความซับซ้อนของรหัสผ่านเหมือน flow อื่นของระบบ
        $complexityErrors = Security::validatePasswordComplexity($password);
        if ($complexityErrors !== []) {
            $this->respond('/register', __('messages.password_requirements_not_met'), 'error', 422, ['errors' => ['password' => $complexityErrors]]);
        }

        $db = Database::getInstance();

        // Check existing
        $stmt = $db->prepare("SELECT id FROM users WHERE username = :username OR email = :email");
        $stmt->execute(['username' => $username, 'email' => $email]);
        if ($stmt->fetch()) {
            $this->respond('/register', __('messages.user_exists'), 'error', 422, ['errors' => ['username' => [__('messages.user_exists')]]]);
        }

        // Default role for new users (usually 2 = User)
        $defaultRole = Settings::get('default_role_id', 2);
        
        $requireVerification = Settings::get('require_email_verification', '0') === '1';

        // Insert User
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        if ($requireVerification) {
            $token = bin2hex(random_bytes(32));
            // เก็บเฉพาะ hash ของ token (DB leak ไม่สามารถนำไปยืนยันอีเมลแทนได้)
            $insert = $db->prepare("INSERT INTO users (username, email, password, role_id, status, email_verify_token) VALUES (:username, :email, :password, :role, 'active', :token)");
            $insert->execute([
                'username' => $username,
                'email' => $email,
                'password' => $hashedPassword,
                'role' => $defaultRole,
                'token' => hash('sha256', $token)
            ]);
            $verifyLink = \App\Core\Config::get('APP_URL') . "/verify-email?token=" . $token;
            $subject = "Verify your email address";
            $body = \App\Core\View::renderToString('emails/verify', [
                'username' => $username,
                'verifyLink' => $verifyLink
            ], 'email');
            Mailer::send($email, $subject, $body);
            
            $message = __('messages.register_success_verify');
        } else {
            $insert = $db->prepare("INSERT INTO users (username, email, password, role_id, status, email_verified_at) VALUES (:username, :email, :password, :role, 'active', NOW())");
            $insert->execute([
                'username' => $username,
                'email' => $email,
                'password' => $hashedPassword,
                'role' => $defaultRole
            ]);
            $message = __('messages.register_success');
        }

        $this->respond('/login', $message, 'success');
    }

    public function verifyEmail()
    {
        Session::init();
        $token = $_GET['token'] ?? '';
        if (empty($token)) {
            $this->redirect('/login');
        }
        
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT id FROM users WHERE email_verify_token = :token AND email_verified_at IS NULL");
        $stmt->execute(['token' => hash('sha256', $token)]);
        $userId = $stmt->fetchColumn();
        
        if (!$userId) {
            Session::setFlash('error', 'Invalid or expired verification token.');
            $this->redirect('/login');
        }
        
        $db->prepare("UPDATE users SET email_verified_at = NOW(), email_verify_token = NULL WHERE id = :id")->execute(['id' => $userId]);
        
        Session::setFlash('success', 'Email verified successfully. You can now login.');
        $this->redirect('/login');
    }

    public function resendVerification()
    {
        Session::init();
        Security::checkCsrf();
        $email = Security::sanitize($_POST['email'] ?? '');
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->respond('/login', __('messages.invalid_email_format'), 'error', 422);
        }

        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT id, username, email_verified_at FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();
        
        if ($user && empty($user['email_verified_at'])) {
            $token = bin2hex(random_bytes(32));
            // เก็บเฉพาะ hash ของ token
            $db->prepare("UPDATE users SET email_verify_token = :token WHERE id = :id")->execute(['token' => hash('sha256', $token), 'id' => $user['id']]);
            $verifyLink = \App\Core\Config::get('APP_URL') . "/verify-email?token=" . $token;
            $subject = "Verify your email address";
            $body = \App\Core\View::renderToString('emails/verify', [
                'username' => $user['username'],
                'verifyLink' => $verifyLink
            ], 'email');
            Mailer::send($email, $subject, $body);
        }
        
        $this->respond('/login', __('messages.verification_link_sent'), 'success');
    }

    public function show2faForm()
    {
        Session::init();
        if (!Session::has('2fa_pending_user_id') || $this->twofaPendingExpired()) {
            Session::remove('2fa_pending_user_id');
            Session::remove('2fa_pending_expires');
            $this->redirect('/login');
        }

        $this->view('auth/2fa', ['pageTitle' => 'Two-Factor Authentication'], 'auth');
    }

    /**
     * สถานะ 2FA pending หมดอายุหรือยัง (5 นาทีหลังกรอกรหัสผ่านสำเร็จ)
     */
    private function twofaPendingExpired(): bool
    {
        $expires = Session::get('2fa_pending_expires');
        return !is_int($expires) || time() > $expires;
    }

    public function verify2faLogin()
    {
        Session::init();
        Security::checkCsrf();

        if (!Session::has('2fa_pending_user_id') || $this->twofaPendingExpired()) {
            Session::remove('2fa_pending_user_id');
            Session::remove('2fa_pending_expires');
            $this->respond('/login', __('messages.session_expired'), 'error', 401);
        }

        $code = $_POST['totp_code'] ?? '';
        $userId = Session::get('2fa_pending_user_id');

        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM users WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $userId]);
        $user = $stmt->fetch();

        if (!$user) {
            Session::destroy();
            Session::init();
            $this->respond('/login', __('messages.session_expired'), 'error', 401);
        }

        $google2fa = new Google2FA();
        $valid = $google2fa->verifyKey($user['two_factor_secret'], $code);

        if ($valid) {
            // Login success
            session_regenerate_id(true);
            Session::remove('2fa_pending_user_id');
            Session::remove('2fa_pending_expires');
            
            Session::set('user_id', $user['id']);
            Session::set('username', $user['username']);
            Session::set('role_id', $user['role_id']);
            if (!empty($user['profile_image'])) {
                Session::set('profile_image', $user['profile_image']);
            }

            $updateStmt = $db->prepare("UPDATE users SET last_login = NOW() WHERE id = :id");
            $updateStmt->execute(['id' => $user['id']]);

            // Reset rate limiter attempts on success
            \App\Core\RateLimiter::reset('/login');
            \App\Core\RateLimiter::reset('/login/2fa');

            Logger::activity("User logged in successfully with 2FA.");
            $this->respond('/dashboard', __('common.welcome'), 'success');
        } else {
            Logger::activity("Failed 2FA attempt for user: {$user['username']}");
            $this->respond('/login/2fa', __('messages.invalid_2fa_code'), 'error', 422, ['errors' => ['totp_code' => [__('messages.invalid_2fa_code')]]]);
        }
    }

    public function forgotPassword()
    {
        Session::init();
        $this->view('auth/forgot-password', ['pageTitle' => 'Forgot Password'], 'auth');
    }

    public function sendResetLink()
    {
        Session::init();
        Security::checkCsrf();
        $email = Security::sanitize($_POST['email'] ?? '');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->respond('/forgot-password', __('messages.invalid_email_format'), 'error', 422);
        }

        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT id, username FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        if ($user) {
            $token = bin2hex(random_bytes(32));
            $expiresAt = date('Y-m-d H:i:s', strtotime('+1 hour'));

            // Delete old tokens for this email
            $db->prepare("DELETE FROM password_resets WHERE email = :email")->execute(['email' => $email]);

            // Insert new token — เก็บเฉพาะ hash (DB leak ≠ account takeover)
            $db->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (:email, :token, :expires_at)")
               ->execute(['email' => $email, 'token' => hash('sha256', $token), 'expires_at' => $expiresAt]);

            // Send email
            // Note: Update APP_URL in .env in the future, using localhost for now
            $resetLink = \App\Core\Config::get('APP_URL') . '/reset-password?token=' . $token;
            
            $subject = "Password Reset Request";
            $body = \App\Core\View::renderToString('emails/reset_password', [
                'username' => $user['username'],
                'resetLink' => $resetLink
            ], 'email');
            
            Mailer::send($email, $subject, $body);
            Logger::activity("Password reset link requested for email: {$email}");
        }

        // Always show success message to prevent email enumeration
        $this->respond('/forgot-password', __('messages.reset_link_sent'), 'success');
    }

    public function resetPassword()
    {
        Session::init();
        $token = $_GET['token'] ?? '';
        if (empty($token)) {
            $this->redirect('/login');
        }

        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT email FROM password_resets WHERE token = :token AND expires_at > NOW()");
        $stmt->execute(['token' => hash('sha256', $token)]);

        if (!$stmt->fetchColumn()) {
            Session::setFlash('error', __('messages.invalid_reset_token'));
            $this->redirect('/forgot-password');
        }

        $this->view('auth/reset-password', ['pageTitle' => 'Reset Password', 'token' => $token], 'auth');
    }

    public function updatePassword()
    {
        Session::init();
        Security::checkCsrf();
        $token = $_POST['token'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (empty($token) || empty($newPassword) || empty($confirmPassword)) {
            $this->respond('/reset-password?token=' . $token, __('messages.all_fields_required'), 'error', 422);
        }

        if ($newPassword !== $confirmPassword) {
            $this->respond('/reset-password?token=' . $token, __('messages.passwords_not_match'), 'error', 422, ['errors' => ['confirm_password' => [__('messages.passwords_not_match')]]]);
        }

        // บังคับนโยบายความซับซ้อนของรหัสผ่านเหมือน flow อื่นของระบบ
        $complexityErrors = Security::validatePasswordComplexity($newPassword);
        if ($complexityErrors !== []) {
            $this->respond('/reset-password?token=' . $token, __('messages.password_requirements_not_met'), 'error', 422, ['errors' => ['new_password' => $complexityErrors]]);
        }

        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT email FROM password_resets WHERE token = :token AND expires_at > NOW()");
        $stmt->execute(['token' => hash('sha256', $token)]);
        $email = $stmt->fetchColumn();

        if (!$email) {
            $this->respond('/forgot-password', __('messages.invalid_reset_token'), 'error', 422);
        }

        $newHash = Security::hashPassword($newPassword);
        
        $db->beginTransaction();
        try {
            $db->prepare("UPDATE users SET password = :password WHERE email = :email")
               ->execute(['password' => $newHash, 'email' => $email]);
               
            $db->prepare("DELETE FROM password_resets WHERE email = :email")
               ->execute(['email' => $email]);
               
            $db->commit();
            Logger::activity("Password reset successfully using token for email: {$email}");
            
            $this->respond('/login', __('messages.password_reset_success'), 'success');
        } catch (\Exception $e) {
            $db->rollBack();
            $this->respond('/reset-password?token=' . $token, __('messages.failed_reset_password'), 'error', 500);
        }
    }
}
