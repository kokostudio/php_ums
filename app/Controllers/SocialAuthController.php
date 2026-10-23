<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Session;
use App\Core\Logger;
use App\Core\Settings;

class SocialAuthController extends Controller
{
    // ==========================================
    // GOOGLE OAUTH
    // ==========================================
    public function redirectToGoogle()
    {
        if (Settings::get('enable_google_login') !== '1') {
            Session::setFlash('error', __('messages.google_login_disabled'));
            $this->redirect('/login');
        }

        $clientId = Settings::get('google_client_id') ?: getenv('GOOGLE_CLIENT_ID');
        $redirectUri = Settings::get('google_redirect_uri') ?: getenv('GOOGLE_REDIRECT_URI');
        
        $state = bin2hex(random_bytes(16));
        Session::set('oauth_state', $state);

        $params = [
            'client_id'     => $clientId,
            'redirect_uri'  => $redirectUri,
            'response_type' => 'code',
            'scope'         => 'email profile',
            'state'         => $state,
            'access_type'   => 'online',
            'prompt'        => 'select_account consent'
        ];

        $authUrl = 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($params);
        header('Location: ' . $authUrl);
        exit;
    }

    public function handleGoogleCallback()
    {
        if (Settings::get('enable_google_login') !== '1') {
            Session::setFlash('error', __('messages.google_login_disabled'));
            $this->redirect('/login');
        }

        $code = $_GET['code'] ?? null;
        $state = $_GET['state'] ?? null;
        $savedState = Session::get('oauth_state');

        if (!$code || !$state || $state !== $savedState) {
            Session::setFlash('error', __('messages.google_login_failed'));
            $this->redirect('/login');
        }

        Session::remove('oauth_state');

        $clientId = Settings::get('google_client_id') ?: getenv('GOOGLE_CLIENT_ID');
        $clientSecret = Settings::get('google_client_secret') ?: getenv('GOOGLE_CLIENT_SECRET');
        $redirectUri = Settings::get('google_redirect_uri') ?: getenv('GOOGLE_REDIRECT_URI');

        // Exchange code for token
        $tokenUrl = 'https://oauth2.googleapis.com/token';
        $postData = [
            'code'          => $code,
            'client_id'     => $clientId,
            'client_secret' => $clientSecret,
            'redirect_uri'  => $redirectUri,
            'grant_type'    => 'authorization_code'
        ];

        $ch = curl_init($tokenUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
        $response = curl_exec($ch);
        curl_close($ch);

        $tokenData = json_decode($response, true);

        if (isset($tokenData['error'])) {
            Session::setFlash('error', __('messages.google_token_failed'));
            $this->redirect('/login');
        }

        $accessToken = $tokenData['access_token'];

        // Get user info
        $userInfoUrl = 'https://www.googleapis.com/oauth2/v2/userinfo';
        $ch = curl_init($userInfoUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $accessToken]);
        $userInfoResponse = curl_exec($ch);
        curl_close($ch);

        $userData = json_decode($userInfoResponse, true);

        if (!isset($userData['id'])) {
            Session::setFlash('error', __('messages.google_data_failed'));
            $this->redirect('/login');
        }

        $googleId = $userData['id'];
        $email = $userData['email'] ?? null;
        $firstName = $userData['given_name'] ?? null;
        $lastName = $userData['family_name'] ?? null;
        $profileImage = $userData['picture'] ?? null;

        if (!$email) {
            Session::setFlash('error', __('messages.google_email_required'));
            $this->redirect('/login');
        }

        $this->loginOrCreateUser('google', $googleId, $email, $firstName, $lastName, $profileImage);
    }

    // ==========================================
    // LINE OAUTH
    // ==========================================
    public function redirectToLine()
    {
        if (Settings::get('enable_line_login') !== '1') {
            Session::setFlash('error', __('messages.line_login_disabled'));
            $this->redirect('/login');
        }

        $clientId = Settings::get('line_client_id') ?: getenv('LINE_CLIENT_ID');
        $redirectUri = Settings::get('line_redirect_uri') ?: getenv('LINE_REDIRECT_URI');
        
        $state = bin2hex(random_bytes(16));
        $nonce = bin2hex(random_bytes(16)); // Requires nonce for OIDC
        Session::set('oauth_state', $state);

        $params = [
            'response_type' => 'code',
            'client_id'     => $clientId,
            'redirect_uri'  => $redirectUri,
            'state'         => $state,
            'scope'         => 'profile openid email',
            'nonce'         => $nonce,
            'prompt'        => 'consent'
        ];

        $authUrl = 'https://access.line.me/oauth2/v2.1/authorize?' . http_build_query($params);
        header('Location: ' . $authUrl);
        exit;
    }

    public function handleLineCallback()
    {
        if (Settings::get('enable_line_login') !== '1') {
            Session::setFlash('error', __('messages.line_login_disabled'));
            $this->redirect('/login');
        }

        $code = $_GET['code'] ?? null;
        $state = $_GET['state'] ?? null;
        $error = $_GET['error'] ?? null;
        $savedState = Session::get('oauth_state');

        if ($error || !$code || !$state || $state !== $savedState) {
            Session::setFlash('error', __('messages.line_login_failed'));
            $this->redirect('/login');
        }

        Session::remove('oauth_state');

        $clientId = Settings::get('line_client_id') ?: getenv('LINE_CLIENT_ID');
        $clientSecret = Settings::get('line_client_secret') ?: getenv('LINE_CLIENT_SECRET');
        $redirectUri = Settings::get('line_redirect_uri') ?: getenv('LINE_REDIRECT_URI');

        // Exchange code for token
        $tokenUrl = 'https://api.line.me/oauth2/v2.1/token';
        $postData = [
            'grant_type'    => 'authorization_code',
            'code'          => $code,
            'redirect_uri'  => $redirectUri,
            'client_id'     => $clientId,
            'client_secret' => $clientSecret
        ];

        $ch = curl_init($tokenUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
        $response = curl_exec($ch);
        curl_close($ch);

        $tokenData = json_decode($response, true);

        if (isset($tokenData['error'])) {
            Session::setFlash('error', __('messages.line_token_failed'));
            $this->redirect('/login');
        }

        $accessToken = $tokenData['access_token'];
        $idToken = $tokenData['id_token'] ?? null;

        // Get User Profile (for display name, picture, userId)
        $profileUrl = 'https://api.line.me/v2/profile';
        $ch = curl_init($profileUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $accessToken]);
        $profileResponse = curl_exec($ch);
        curl_close($ch);

        $profileData = json_decode($profileResponse, true);

        if (!isset($profileData['userId'])) {
            Session::setFlash('error', __('messages.line_data_failed'));
            $this->redirect('/login');
        }

        $lineId = $profileData['userId'];
        $displayName = $profileData['displayName'] ?? '';
        $profileImage = $profileData['pictureUrl'] ?? null;

        // Parse ID token to get email if available
        $email = null;
        if ($idToken) {
            $parts = explode('.', $idToken);
            if (count($parts) === 3) {
                $payload = json_decode(base64_decode(strtr($parts[1], '-_', '+/')), true);
                $email = $payload['email'] ?? null;
            }
        }

        if (!$email) {
            $email = $lineId . '@line.login'; // fallback email for LINE if email permission denied
        }

        // Split display name into first and last name (simple split)
        $nameParts = explode(' ', $displayName, 2);
        $firstName = $nameParts[0];
        $lastName = $nameParts[1] ?? '';

        $this->loginOrCreateUser('line', $lineId, $email, $firstName, $lastName, $profileImage);
    }

    // ==========================================
    // FACEBOOK OAUTH
    // ==========================================
    public function redirectToFacebook()
    {
        if (Settings::get('enable_facebook_login') !== '1') {
            Session::setFlash('error', __('messages.facebook_login_disabled'));
            $this->redirect('/login');
        }

        $clientId = Settings::get('facebook_client_id') ?: getenv('FACEBOOK_CLIENT_ID');
        $redirectUri = Settings::get('facebook_redirect_uri') ?: getenv('FACEBOOK_REDIRECT_URI');

        $state = bin2hex(random_bytes(16));
        Session::set('oauth_state', $state);

        $params = [
            'client_id'     => $clientId,
            'redirect_uri'  => $redirectUri,
            'state'         => $state,
            'scope'         => 'email,public_profile',
            'response_type' => 'code'
        ];

        $authUrl = 'https://www.facebook.com/v19.0/dialog/oauth?' . http_build_query($params);
        header('Location: ' . $authUrl);
        exit;
    }

    public function handleFacebookCallback()
    {
        if (Settings::get('enable_facebook_login') !== '1') {
            Session::setFlash('error', __('messages.facebook_login_disabled'));
            $this->redirect('/login');
        }

        $code = $_GET['code'] ?? null;
        $state = $_GET['state'] ?? null;
        $error = $_GET['error'] ?? null;
        $savedState = Session::get('oauth_state');

        if ($error || !$code || !$state || $state !== $savedState) {
            Session::setFlash('error', __('messages.facebook_login_failed'));
            $this->redirect('/login');
        }

        Session::remove('oauth_state');

        $clientId = Settings::get('facebook_client_id') ?: getenv('FACEBOOK_CLIENT_ID');
        $clientSecret = Settings::get('facebook_client_secret') ?: getenv('FACEBOOK_CLIENT_SECRET');
        $redirectUri = Settings::get('facebook_redirect_uri') ?: getenv('FACEBOOK_REDIRECT_URI');

        $tokenUrl = 'https://graph.facebook.com/v19.0/oauth/access_token?' . http_build_query([
            'client_id'     => $clientId,
            'client_secret' => $clientSecret,
            'redirect_uri'  => $redirectUri,
            'code'          => $code
        ]);

        $ch = curl_init($tokenUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        $tokenData = json_decode($response, true);
        if (!isset($tokenData['access_token'])) {
            Session::setFlash('error', __('messages.facebook_token_failed'));
            $this->redirect('/login');
        }

        $accessToken = $tokenData['access_token'];

        // Get user profile
        $userUrl = 'https://graph.facebook.com/v19.0/me?fields=id,name,first_name,last_name,email,picture.type(large)&access_token=' . $accessToken;
        $ch = curl_init($userUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $userResponse = curl_exec($ch);
        curl_close($ch);

        $userData = json_decode($userResponse, true);
        if (!isset($userData['id'])) {
            Session::setFlash('error', __('messages.facebook_data_failed'));
            $this->redirect('/login');
        }

        $facebookId = $userData['id'];
        $email = $userData['email'] ?? null;
        $firstName = $userData['first_name'] ?? ($userData['name'] ?? 'Facebook');
        $lastName = $userData['last_name'] ?? 'User';
        $profileImage = $userData['picture']['data']['url'] ?? null;

        if (!$email) {
            $email = $facebookId . '@facebook.login';
        }

        $this->loginOrCreateUser('facebook', $facebookId, $email, $firstName, $lastName, $profileImage);
    }

    // ==========================================
    // GITHUB OAUTH
    // ==========================================
    public function redirectToGithub()
    {
        if (Settings::get('enable_github_login') !== '1') {
            Session::setFlash('error', __('messages.github_login_disabled'));
            $this->redirect('/login');
        }

        $clientId = Settings::get('github_client_id') ?: getenv('GITHUB_CLIENT_ID');
        $redirectUri = Settings::get('github_redirect_uri') ?: getenv('GITHUB_REDIRECT_URI');

        $state = bin2hex(random_bytes(16));
        Session::set('oauth_state', $state);

        $params = [
            'client_id'     => $clientId,
            'redirect_uri'  => $redirectUri,
            'state'         => $state,
            'scope'         => 'read:user user:email'
        ];

        $authUrl = 'https://github.com/login/oauth/authorize?' . http_build_query($params);
        header('Location: ' . $authUrl);
        exit;
    }

    public function handleGithubCallback()
    {
        if (Settings::get('enable_github_login') !== '1') {
            Session::setFlash('error', __('messages.github_login_disabled'));
            $this->redirect('/login');
        }

        $code = $_GET['code'] ?? null;
        $state = $_GET['state'] ?? null;
        $savedState = Session::get('oauth_state');

        if (!$code || !$state || $state !== $savedState) {
            Session::setFlash('error', __('messages.github_login_failed'));
            $this->redirect('/login');
        }

        Session::remove('oauth_state');

        $clientId = Settings::get('github_client_id') ?: getenv('GITHUB_CLIENT_ID');
        $clientSecret = Settings::get('github_client_secret') ?: getenv('GITHUB_CLIENT_SECRET');
        $redirectUri = Settings::get('github_redirect_uri') ?: getenv('GITHUB_REDIRECT_URI');

        // Exchange code for token
        $tokenUrl = 'https://github.com/login/oauth/access_token';
        $postData = [
            'client_id'     => $clientId,
            'client_secret' => $clientSecret,
            'code'          => $code,
            'redirect_uri'  => $redirectUri
        ];

        $ch = curl_init($tokenUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json', 'User-Agent: PHP-UMS-App']);
        $response = curl_exec($ch);
        curl_close($ch);

        $tokenData = json_decode($response, true);
        if (!isset($tokenData['access_token'])) {
            Session::setFlash('error', __('messages.github_token_failed'));
            $this->redirect('/login');
        }

        $accessToken = $tokenData['access_token'];

        // Get user profile
        $userUrl = 'https://api.github.com/user';
        $ch = curl_init($userUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $accessToken,
            'User-Agent: PHP-UMS-App',
            'Accept: application/json'
        ]);
        $userResponse = curl_exec($ch);
        curl_close($ch);

        $userData = json_decode($userResponse, true);
        if (!isset($userData['id'])) {
            Session::setFlash('error', __('messages.github_data_failed'));
            $this->redirect('/login');
        }

        $githubId = (string)$userData['id'];
        $email = $userData['email'] ?? null;
        $name = $userData['name'] ?? ($userData['login'] ?? 'GitHub User');
        $profileImage = $userData['avatar_url'] ?? null;

        // If email is private, fetch from /user/emails
        if (!$email) {
            $emailsUrl = 'https://api.github.com/user/emails';
            $ch = curl_init($emailsUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $accessToken,
                'User-Agent: PHP-UMS-App',
                'Accept: application/json'
            ]);
            $emailsResponse = curl_exec($ch);
            curl_close($ch);
            $emails = json_decode($emailsResponse, true);
            if (is_array($emails)) {
                foreach ($emails as $emailItem) {
                    if (!empty($emailItem['primary']) && !empty($emailItem['email'])) {
                        $email = $emailItem['email'];
                        break;
                    }
                }
            }
        }

        if (!$email) {
            $email = ($userData['login'] ?? $githubId) . '@github.login';
        }

        $nameParts = explode(' ', $name, 2);
        $firstName = $nameParts[0];
        $lastName = $nameParts[1] ?? '';

        $this->loginOrCreateUser('github', $githubId, $email, $firstName, $lastName, $profileImage);
    }

    // ==========================================
    // CORE LOGIN/REGISTRATION LOGIC
    // ==========================================
    private function loginOrCreateUser($provider, $providerId, $email, $firstName, $lastName, $profileImage)
    {
        $db = Database::getInstance();
        $providerColumn = $provider . '_id';
        $currentUserId = Session::get('user_id');

        // Check if provider_id is already linked to any account
        $stmt = $db->prepare("SELECT * FROM users WHERE {$providerColumn} = :provider_id LIMIT 1");
        $stmt->execute(['provider_id' => $providerId]);
        $existingUser = $stmt->fetch();

        if ($currentUserId) {
            // ==========================================
            // LINKING FLOW (User is already logged in)
            // ==========================================
            if ($existingUser) {
                if ($existingUser['id'] == $currentUserId) {
                    Session::setFlash('success', __('messages.social_already_linked', ['provider' => ucfirst($provider)]));
                } else {
                    Session::setFlash('error', __('messages.social_linked_other', ['provider' => ucfirst($provider)]));
                }
                $this->redirect('/profile');
            }

            // Link the account
            $update = $db->prepare("UPDATE users SET {$providerColumn} = :provider_id WHERE id = :id");
            $update->execute(['provider_id' => $providerId, 'id' => $currentUserId]);

            // Optionally update avatar if they don't have one
            $stmt = $db->prepare("SELECT avatar_url FROM users WHERE id = :id");
            $stmt->execute(['id' => $currentUserId]);
            $currentUser = $stmt->fetch();
            if (empty($currentUser['avatar_url']) && $profileImage) {
                $db->prepare("UPDATE users SET avatar_url = :avatar WHERE id = :id")->execute(['avatar' => $profileImage, 'id' => $currentUserId]);
                Session::set('profile_image', $profileImage);
            }

            Logger::activity("Linked " . ucfirst($provider) . " account to profile.");
            Session::setFlash('success', __('messages.social_link_success', ['provider' => ucfirst($provider)]));
            $this->redirect('/profile');
            return;
        }

        // ==========================================
        // LOGIN/REGISTRATION FLOW (User is NOT logged in)
        // ==========================================
        $user = $existingUser;

        // If not found by provider_id, check by email
        if (!$user && $email && strpos($email, '@line.login') === false) {
            $stmt = $db->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
            $stmt->execute(['email' => $email]);
            $user = $stmt->fetch();

            if ($user) {
                // Link account to existing email
                $update = $db->prepare("UPDATE users SET {$providerColumn} = :provider_id WHERE id = :id");
                $update->execute([
                    'provider_id' => $providerId,
                    'id' => $user['id']
                ]);
            }
        }

        // If still no user, create a new one
        if (!$user) {
            $username = $provider . '_' . substr(md5($providerId . time()), 0, 10);
            
            // Assume default role is user (role_id 2 usually)
            $defaultRole = Settings::get('default_role_id', 2);
            
            $insert = $db->prepare("INSERT INTO users (username, email, first_name, last_name, avatar_url, {$providerColumn}, role_id) VALUES (:username, :email, :first_name, :last_name, :avatar_url, :provider_id, :role_id)");
            $insert->execute([
                'username' => $username,
                'email' => $email,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'avatar_url' => $profileImage,
                'provider_id' => $providerId,
                'role_id' => $defaultRole
            ]);
            
            $userId = $db->lastInsertId();
            
            // Re-fetch the newly created user
            $stmt = $db->prepare("SELECT * FROM users WHERE id = :id LIMIT 1");
            $stmt->execute(['id' => $userId]);
            $user = $stmt->fetch();
        }

        if ($user['status'] !== 'active') {
            Logger::activity("Failed {$provider} login attempt (Account disabled) for user: {$user['username']}");
            Session::setFlash('error', __('messages.account_disabled') ?? 'Your account is disabled.');
            $this->redirect('/login');
        }

        // Update avatar if changed (optional)
        if ($profileImage && $profileImage !== $user['avatar_url']) {
            $update = $db->prepare("UPDATE users SET avatar_url = :avatar_url WHERE id = :id");
            $update->execute(['avatar_url' => $profileImage, 'id' => $user['id']]);
        }

        // Complete Login
        $this->finalizeLogin($user, $db);
    }

    private function finalizeLogin($user, $db)
    {
        // Check for 2FA
        if ($user['two_factor_enabled']) {
            Session::set('temp_user_id', $user['id']);
            $this->redirect('/login/2fa');
        }

        // Generate Session ID explicitly
        session_regenerate_id(true);

        Session::set('user_id', $user['id']);
        Session::set('username', $user['username']);
        Session::set('user_email', $user['email']);
        Session::set('role_id', $user['role_id']);
        Session::set('2fa_enabled', false);
        Session::set('profile_image', $user['avatar_url']);

        // Update Last Login
        $update = $db->prepare("UPDATE users SET last_login = NOW() WHERE id = :id");
        $update->execute(['id' => $user['id']]);

        // Record User Session
        $this->recordSession($user['id'], $db);

        Logger::activity("Successful social login for user: {$user['username']}");

        $this->redirect('/dashboard');
    }

    private function recordSession($userId, $db)
    {
        $sessionId = session_id();
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';

        // Check if session already recorded
        $stmt = $db->prepare("SELECT id FROM user_sessions WHERE id = :id");
        $stmt->execute(['id' => $sessionId]);

        if ($stmt->fetch()) {
            $update = $db->prepare("UPDATE user_sessions SET last_activity = NOW() WHERE id = :id");
            $update->execute(['id' => $sessionId]);
        } else {
            $insert = $db->prepare("INSERT INTO user_sessions (id, user_id, ip_address, user_agent, last_activity) VALUES (:id, :user_id, :ip, :ua, NOW())");
            $insert->execute([
                'id' => $sessionId,
                'user_id' => $userId,
                'ip' => $ip,
                'ua' => $userAgent
            ]);
        }
    }
}
