<?php

namespace App\Controllers\Api;

use App\Core\Database;
use App\Core\Security;
use App\Core\Settings;
use Firebase\JWT\JWT;
use PragmaRX\Google2FA\Google2FA;

class AuthController
{
    private function json($data, $status = 200)
    {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code($status);
        echo json_encode($data);
        exit;
    }

    public function login()
    {
        $input = json_decode(file_get_contents('php://input'), true);
        $username = $input['username'] ?? '';
        $password = $input['password'] ?? '';
        $totpCode = $input['totp_code'] ?? null;

        if (empty($username) || empty($password)) {
            return $this->json(['error' => 'Username and password are required'], 400);
        }

        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM users WHERE username = :username LIMIT 1");
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch();

        if ($user && Security::verifyPassword($password, $user['password'])) {
            if ($user['status'] !== 'active') {
                return $this->json(['error' => 'Account is disabled'], 403);
            }

            if ($user['two_factor_enabled']) {
                if (empty($totpCode)) {
                    return $this->json(['error' => '2FA code required', 'requires_2fa' => true], 403);
                }
                
                $google2fa = new Google2FA();
                if (!$google2fa->verifyKey($user['two_factor_secret'], $totpCode)) {
                    return $this->json(['error' => 'Invalid 2FA code'], 401);
                }
            }

            // Generate JWT
            $secret = $_ENV['JWT_SECRET'] ?? Settings::get('jwt_secret') ?? 'default_secret_please_change';
            $issuedAt = time();
            $expire = $issuedAt + 3600; // 1 hour token

            $payload = [
                'iat'  => $issuedAt,
                'exp'  => $expire,
                'sub'  => $user['id'],
                'role' => $user['role_id'],
                'user' => $user['username']
            ];

            $jwt = JWT::encode($payload, $secret, 'HS256');

            return $this->json([
                'success' => true,
                'token' => $jwt,
                'expires_in' => 3600,
                'user' => [
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'role_id' => $user['role_id']
                ]
            ]);

        } else {
            return $this->json(['error' => 'Invalid credentials'], 401);
        }
    }

    public function me()
    {
        // Protected route (JwtMiddleware will populate $_REQUEST['jwt_user'])
        $jwtUser = $_REQUEST['jwt_user'] ?? null;
        if (!$jwtUser) {
            return $this->json(['error' => 'Not authenticated'], 401);
        }

        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT id, username, email, role_id, status FROM users WHERE id = :id");
        $stmt->execute(['id' => $jwtUser['sub']]);
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $this->json(['success' => true, 'user' => $user]);
    }
}
