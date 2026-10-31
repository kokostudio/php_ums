<?php

namespace App\Core;

class Session
{
    public static function init(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            // Configure session security
            ini_set('session.cookie_httponly', '1');
            ini_set('session.use_only_cookies', '1');
            ini_set('session.cookie_samesite', 'Lax');

            // ส่ง cookie เฉพาะผ่าน HTTPS เท่านั้นเมื่อระบบรันบน HTTPS
            $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
                || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https')
                || ($_SERVER['SERVER_PORT'] ?? '') == '443';
            if ($isHttps) {
                ini_set('session.cookie_secure', '1');
            }

            $lifetime = (int)Settings::get('session_lifetime', 120) * 60;
            ini_set('session.gc_maxlifetime', (string)$lifetime);

            $cookieParams = session_get_cookie_params();
            session_set_cookie_params([
                'lifetime' => $lifetime,
                'path'     => $cookieParams['path'] ?? '/',
                'domain'   => $cookieParams['domain'] ?? '',
                'secure'   => $isHttps,
                'httponly' => true,
                'samesite' => 'Lax',
            ]);

            $handler = new DatabaseSessionHandler();
            session_set_save_handler($handler, true);

            session_start();
        }

        // Generate CSRF token if not exists
        if (!self::has('csrf_token')) {
            self::generateCsrfToken();
        }
    }

    public static function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    public static function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    public static function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public static function destroy(): void
    {
        // เคลียร์ session cookie ที่ฝั่ง browser ด้วย (หมดอายุทันที)
        if (session_status() === PHP_SESSION_ACTIVE) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', [
                'expires'  => time() - 42000,
                'path'     => $params['path'] ?? '/',
                'domain'   => $params['domain'] ?? '',
                'secure'   => (bool) ($params['secure'] ?? false),
                'httponly' => true,
                'samesite' => 'Lax',
            ]);
        }

        session_destroy();
        $_SESSION = [];
    }

    /**
     * Flash messages (stored for one request only)
     */
    public static function setFlash(string $key, string $message): void
    {
        $_SESSION['_flash'][$key] = $message;
    }

    public static function getFlash(string $key): ?string
    {
        if (isset($_SESSION['_flash'][$key])) {
            $message = $_SESSION['_flash'][$key];
            unset($_SESSION['_flash'][$key]);
            return $message;
        }
        return null;
    }

    /**
     * CSRF Protection
     */
    public static function generateCsrfToken(): string
    {
        $token = bin2hex(random_bytes(32));
        self::set('csrf_token', $token);
        return $token;
    }

    public static function getCsrfToken(): string
    {
        return self::get('csrf_token');
    }

    public static function verifyCsrfToken(?string $token): bool
    {
        if (empty($token) || !self::has('csrf_token')) {
            return false;
        }
        return hash_equals(self::get('csrf_token'), $token);
    }
}
