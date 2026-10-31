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
     * Get Hashids instance with a consistent salt and optional context
     */
    private static function getHashids(string $context = ''): \Hashids\Hashids
    {
        if (empty($_ENV['APP_KEY'])) {
            throw new \Exception("APP_KEY is missing in .env file. Security salt is required.", 500);
        }
        $salt = $_ENV['APP_KEY'] . ($context !== '' ? '_' . $context : '');
        return new \Hashids\Hashids($salt, 8);
    }

    /**
     * Encode an integer ID to an 8-character hash
     */
    public static function encodeId(int $id, string $context = ''): string
    {
        return self::getHashids($context)->encode($id);
    }

    /**
     * Decode an 8-character hash back to integer ID
     * Returns null if invalid
     */
    public static function decodeId(string $hash, string $context = ''): ?int
    {
        $decoded = self::getHashids($context)->decode($hash);
        return !empty($decoded) ? $decoded[0] : null;
    }

    /**
     * Encrypt an arbitrary string (e.g. Session ID, tokens) into a URL-safe token.
     *
     * ใช้ AES-256-GCM (Authenticated Encryption) — ตรวจจับการแก้ไข ciphertext ได้ (tamper-proof)
     * รูปแบบผลลัพธ์: "v2." + base64url(iv(12) + tag(16) + ciphertext)
     */
    public static function encrypt(string $data): string
    {
        $key = self::encryptionKey();
        $iv = random_bytes(12); // GCM แนะนำ IV 12 ไบต์
        $tag = '';
        $ciphertext = openssl_encrypt($data, 'aes-256-gcm', $key, OPENSSL_RAW_DATA, $iv, $tag);
        if ($ciphertext === false) {
            throw new \Exception('Encryption failed: unable to encrypt data.', 500);
        }
        return 'v2.' . rtrim(strtr(base64_encode($iv . $tag . $ciphertext), '+/', '-_'), '=');
    }

    /**
     * Decrypt an encrypted URL-safe token back to the original string
     * Returns null if decryption fails or token is tampered
     */
    public static function decrypt(string $token): ?string
    {
        $key = self::encryptionKey();

        if (!str_starts_with($token, 'v2.')) {
            return null; // รองรับเฉพาะรูปแบบ v2 (GCM)
        }

        $decoded = base64_decode(strtr(substr($token, 3), '-_', '+/'));
        if ($decoded === false || strlen($decoded) <= 12 + 16) {
            return null;
        }
        $iv = substr($decoded, 0, 12);
        $tag = substr($decoded, 12, 16);
        $ciphertext = substr($decoded, 28);
        $decrypted = openssl_decrypt($ciphertext, 'aes-256-gcm', $key, OPENSSL_RAW_DATA, $iv, $tag);
        return $decrypted !== false ? $decrypted : null;
    }

    /**
     * คีย์สำหรับเข้ารหัส — บังคับให้มี APP_KEY เสมอ (ห้าม fallback ค่า default)
     */
    private static function encryptionKey(): string
    {
        if (empty($_ENV['APP_KEY'])) {
            throw new \Exception('APP_KEY is missing in .env file. Encryption key is required.', 500);
        }
        return hash('sha256', $_ENV['APP_KEY'], true);
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
