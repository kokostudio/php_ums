<?php

namespace App\Core;

class Security
{
    /**
     * Sanitize input strings to prevent XSS
     */
    public static function sanitize(string $input): string
    {
        $input = trim($input);
        $input = stripslashes($input);
        return htmlspecialchars($input, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    /**
     * Hash password securely using Bcrypt (or Argon2 if specified)
     */
    public static function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    }

    /**
     * Verify password against hash
     */
    public static function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    /**
     * Validate CSRF Token from POST request
     */
    public static function checkCsrf(): void
    {
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        
        if (in_array($requestMethod, ['POST', 'PUT', 'DELETE'])) {
            $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
            
            if (!Session::verifyCsrfToken($token)) {
                http_response_code(403);
                die("CSRF Token Verification Failed.");
            }
        }
    }

    /**
     * Get Hashids instance with a consistent salt
     */
    private static function getHashids(): \Hashids\Hashids
    {
        $salt = $_ENV['APP_KEY'] ?? 'CoreSystemDefaultSalt123!@#';
        return new \Hashids\Hashids($salt, 8);
    }

    /**
     * Encode an integer ID to an 8-character hash
     */
    public static function encodeId(int $id): string
    {
        return self::getHashids()->encode($id);
    }

    /**
     * Decode an 8-character hash back to integer ID
     * Returns null if invalid
     */
    public static function decodeId(string $hash): ?int
    {
        $decoded = self::getHashids()->decode($hash);
        return !empty($decoded) ? $decoded[0] : null;
    }
}
