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

        $username = Security::sanitize($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($username) || empty($password)) {
            Session::setFlash('error', __('messages.enter_user_pass'));
            Session::setFlash('old_username', $username);
            $this->redirect('/login');
        }

        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM users WHERE username = :username LIMIT 1");
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch();

        if ($user && Security::verifyPassword($password, $user['password'])) {
            if ($user['status'] !== 'active') {
                Logger::activity("Failed login attempt (Account {$user['status']}) for user: {$username}");
                Session::setFlash('error', __('messages.account_disabled'));
                Session::setFlash('old_username', $username);
                $this->redirect('/login');
            }

            // Check email verification
            $requireVerification = Settings::get('require_email_verification', '0') === '1';
            if ($requireVerification && empty($user['email_verified_at'])) {
                Logger::activity("Failed login attempt (Unverified email) for user: {$username}");
                Session::setFlash('error', 'Please verify your email address before logging in.');
                Session::setFlash('unverified_email', $user['email']);
                Session::setFlash('old_username', $username);
                $this->redirect('/login');
            }

            // Check if 2FA is enabled
            if ($user['two_factor_enabled']) {
                Session::set('2fa_pending_user_id', $user['id']);
                $this->redirect('/login/2fa');
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

            Logger::activity("User logged in successfully.");

            $this->redirect('/dashboard');
        } else {
            Logger::activity("Failed login attempt (Invalid credentials) for user: {$username}");
            Session::setFlash('error', __('messages.invalid_credentials'));
            Session::setFlash('old_username', $username);
            $this->redirect('/login');
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
            Session::setFlash('error', __('messages.registration_disabled') ?? 'Registration is currently disabled.');
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
            Session::setFlash('error', __('messages.registration_disabled') ?? 'Registration is currently disabled.');
            $this->redirect('/login');
        }

        Security::checkCsrf();

        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
            Session::setFlash('error', __('messages.all_fields_required'));
            $this->redirect('/register');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Session::setFlash('error', __('messages.invalid_email'));
            $this->redirect('/register');
        }

        if ($password !== $confirm_password) {
            Session::setFlash('error', __('messages.password_mismatch'));
            $this->redirect('/register');
        }

        $db = Database::getInstance();

        // Check existing
        $stmt = $db->prepare("SELECT id FROM users WHERE username = :username OR email = :email");
        $stmt->execute(['username' => $username, 'email' => $email]);
        if ($stmt->fetch()) {
            Session::setFlash('error', __('messages.user_exists'));
            $this->redirect('/register');
        }

        // Default role for new users (usually 2 = User)
        $defaultRole = Settings::get('default_role_id', 2);
        
        $requireVerification = Settings::get('require_email_verification', '0') === '1';

        // Insert User
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        if ($requireVerification) {
            $token = bin2hex(random_bytes(32));
            $insert = $db->prepare("INSERT INTO users (username, email, password, role_id, status, email_verify_token) VALUES (:username, :email, :password, :role, 'active', :token)");
            $insert->execute([
                'username' => $username,
                'email' => $email,
                'password' => $hashedPassword,
                'role' => $defaultRole,
                'token' => $token
            ]);
            $verifyLink = \App\Core\Config::get('APP_URL') . "/verify-email?token=" . $token;
            $subject = "Verify your email address";
            $body = \App\Core\View::renderToString('emails/verify', [
                'username' => $username,
                'verifyLink' => $verifyLink
            ], 'email');
            Mailer::send($email, $subject, $body);
            
            Session::setFlash('success', 'Registration successful. Please check your email to verify your account.');
        } else {
            $insert = $db->prepare("INSERT INTO users (username, email, password, role_id, status, email_verified_at) VALUES (:username, :email, :password, :role, 'active', NOW())");
            $insert->execute([
                'username' => $username,
                'email' => $email,
                'password' => $hashedPassword,
                'role' => $defaultRole
            ]);
            Session::setFlash('success', __('messages.register_success'));
        }

        $this->redirect('/login');
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
        $stmt->execute(['token' => $token]);
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
            Session::setFlash('error', 'Invalid email format.');
            $this->redirect('/login');
        }
        
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT id, username, email_verified_at FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();
        
        if ($user && empty($user['email_verified_at'])) {
            $token = bin2hex(random_bytes(32));
            $db->prepare("UPDATE users SET email_verify_token = :token WHERE id = :id")->execute(['token' => $token, 'id' => $user['id']]);
            $verifyLink = \App\Core\Config::get('APP_URL') . "/verify-email?token=" . $token;
            $subject = "Verify your email address";
            $body = \App\Core\View::renderToString('emails/verify', [
                'username' => $user['username'],
                'verifyLink' => $verifyLink
            ], 'email');
            Mailer::send($email, $subject, $body);
        }
        
        Session::setFlash('success', 'If the email is registered and unverified, a new verification link has been sent.');
        $this->redirect('/login');
    }

    public function show2faForm()
    {
        Session::init();
        if (!Session::has('2fa_pending_user_id')) {
            $this->redirect('/login');
        }

        $this->view('auth/2fa', ['pageTitle' => 'Two-Factor Authentication'], 'auth');
    }

    public function verify2faLogin()
    {
        Session::init();
        Security::checkCsrf();

        if (!Session::has('2fa_pending_user_id')) {
            $this->redirect('/login');
        }

        $code = $_POST['totp_code'] ?? '';
        $userId = Session::get('2fa_pending_user_id');

        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM users WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $userId]);
        $user = $stmt->fetch();

        if (!$user) {
            Session::destroy();
            $this->redirect('/login');
        }

        $google2fa = new Google2FA();
        $valid = $google2fa->verifyKey($user['two_factor_secret'], $code);

        if ($valid) {
            // Login success
            session_regenerate_id(true);
            Session::remove('2fa_pending_user_id');
            
            Session::set('user_id', $user['id']);
            Session::set('username', $user['username']);
            Session::set('role_id', $user['role_id']);
            if (!empty($user['profile_image'])) {
                Session::set('profile_image', $user['profile_image']);
            }

            $updateStmt = $db->prepare("UPDATE users SET last_login = NOW() WHERE id = :id");
            $updateStmt->execute(['id' => $user['id']]);

            Logger::activity("User logged in successfully with 2FA.");
            $this->redirect('/dashboard');
        } else {
            Logger::activity("Failed 2FA attempt for user: {$user['username']}");
            Session::setFlash('error', 'Invalid authentication code.');
            $this->redirect('/login/2fa');
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
            Session::setFlash('error', 'Invalid email format.');
            $this->redirect('/forgot-password');
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

            // Insert new token
            $db->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (:email, :token, :expires_at)")
               ->execute(['email' => $email, 'token' => $token, 'expires_at' => $expiresAt]);

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
        Session::setFlash('success', 'If an account with that email exists, a password reset link has been sent.');
        $this->redirect('/forgot-password');
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
        $stmt->execute(['token' => $token]);
        
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
            Session::setFlash('error', __('messages.all_fields_required'));
            $this->redirect('/reset-password?token=' . $token);
        }

        if ($newPassword !== $confirmPassword) {
            Session::setFlash('error', __('messages.passwords_not_match'));
            $this->redirect('/reset-password?token=' . $token);
        }

        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT email FROM password_resets WHERE token = :token AND expires_at > NOW()");
        $stmt->execute(['token' => $token]);
        $email = $stmt->fetchColumn();

        if (!$email) {
            Session::setFlash('error', __('messages.invalid_reset_token'));
            $this->redirect('/forgot-password');
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
            
            Session::setFlash('success', 'Your password has been reset successfully. Please login.');
            $this->redirect('/login');
        } catch (\Exception $e) {
            $db->rollBack();
            Session::setFlash('error', 'Failed to reset password. Please try again.');
            $this->redirect('/reset-password?token=' . $token);
        }
    }
}
