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
                throw new \Exception("CSRF Token Verification Failed.", 403);
            }
        }
    }

    /**
     * Get Hashids instance with a consistent salt
     */
    private static function getHashids(): \Hashids\Hashids
    {
        if (empty($_ENV['APP_KEY'])) {
            throw new \Exception("APP_KEY is missing in .env file. Security salt is required.", 500);
        }
        $salt = $_ENV['APP_KEY'];
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
    /**
     * Validate password against complexity rules defined in Settings.
     * Returns an array of error messages, or empty array if valid.
     */
    public static function validatePasswordComplexity(string $password): array
    {
        $errors = [];
        $minLength = (int)Settings::get('password_min_length', 6);
        if (strlen($password) < $minLength) {
            $errors[] = __("auth.pwd_min_length", ['length' => $minLength]);
        }
        
        if (Settings::get('pwd_require_upper') == '1' && !preg_match('/[A-Z]/', $password)) {
            $errors[] = __("auth.pwd_req_upper");
        }
        
        if (Settings::get('pwd_require_lower') == '1' && !preg_match('/[a-z]/', $password)) {
            $errors[] = __("auth.pwd_req_lower");
        }
        
        if (Settings::get('pwd_require_number') == '1' && !preg_match('/[0-9]/', $password)) {
            $errors[] = __("auth.pwd_req_number");
        }
        
        if (Settings::get('pwd_require_special') == '1' && !preg_match('/[^a-zA-Z0-9]/', $password)) {
            $errors[] = __("auth.pwd_req_special");
        }

        return $errors;
    }
}
