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
        $clientId = getenv('GOOGLE_CLIENT_ID');
        $redirectUri = getenv('GOOGLE_REDIRECT_URI');
        
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
        $code = $_GET['code'] ?? null;
        $state = $_GET['state'] ?? null;
        $savedState = Session::get('oauth_state');

        if (!$code || !$state || $state !== $savedState) {
            Session::setFlash('error', 'Google Login failed or was cancelled.');
            $this->redirect('/login');
        }

        Session::remove('oauth_state');

        $clientId = getenv('GOOGLE_CLIENT_ID');
        $clientSecret = getenv('GOOGLE_CLIENT_SECRET');
        $redirectUri = getenv('GOOGLE_REDIRECT_URI');

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
            Session::setFlash('error', 'Failed to obtain Google access token.');
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
            Session::setFlash('error', 'Failed to fetch user data from Google.');
            $this->redirect('/login');
        }

        $googleId = $userData['id'];
        $email = $userData['email'] ?? null;
        $firstName = $userData['given_name'] ?? null;
        $lastName = $userData['family_name'] ?? null;
        $profileImage = $userData['picture'] ?? null;

        if (!$email) {
            Session::setFlash('error', 'Email is required from Google account.');
            $this->redirect('/login');
        }

        $this->loginOrCreateUser('google', $googleId, $email, $firstName, $lastName, $profileImage);
    }

    // ==========================================
    // LINE OAUTH
    // ==========================================
    public function redirectToLine()
    {
        $clientId = getenv('LINE_CLIENT_ID');
        $redirectUri = getenv('LINE_REDIRECT_URI');
        
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
        $code = $_GET['code'] ?? null;
        $state = $_GET['state'] ?? null;
        $error = $_GET['error'] ?? null;
        $savedState = Session::get('oauth_state');

        if ($error || !$code || !$state || $state !== $savedState) {
            Session::setFlash('error', 'LINE Login failed or was cancelled.');
            $this->redirect('/login');
        }

        Session::remove('oauth_state');

        $clientId = getenv('LINE_CLIENT_ID');
        $clientSecret = getenv('LINE_CLIENT_SECRET');
        $redirectUri = getenv('LINE_REDIRECT_URI');

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
            Session::setFlash('error', 'Failed to obtain LINE access token.');
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
            Session::setFlash('error', 'Failed to fetch user profile from LINE.');
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
    // CORE LOGIN/REGISTRATION LOGIC
    // ==========================================
    private function loginOrCreateUser($provider, $providerId, $email, $firstName, $lastName, $profileImage)
    {
        $db = Database::getInstance();

        // Check if user exists by provider_id
        $stmt = $db->prepare("SELECT * FROM users WHERE provider = :provider AND provider_id = :provider_id LIMIT 1");
        $stmt->execute(['provider' => $provider, 'provider_id' => $providerId]);
        $user = $stmt->fetch();

        // If not found by provider_id, check by email
        if (!$user && $email) {
            $stmt = $db->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
            $stmt->execute(['email' => $email]);
            $user = $stmt->fetch();

            if ($user) {
                // Link account to existing email
                $update = $db->prepare("UPDATE users SET provider = :provider, provider_id = :provider_id WHERE id = :id");
                $update->execute([
                    'provider' => $provider,
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
            
            $insert = $db->prepare("INSERT INTO users (username, email, first_name, last_name, profile_image, provider, provider_id, role_id, status) VALUES (:username, :email, :first_name, :last_name, :profile_image, :provider, :provider_id, :role_id, 'active')");
            $insert->execute([
                'username' => $username,
                'email' => $email,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'profile_image' => $profileImage,
                'provider' => $provider,
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
            Logger::activity("Failed {$provider} login attempt (Account {$user['status']}) for user: {$user['username']}");
            Session::setFlash('error', __('messages.account_disabled'));
            $this->redirect('/login');
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
        Session::set('user_role', $user['role_id']);
        Session::set('profile_image', $user['profile_image']);

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
