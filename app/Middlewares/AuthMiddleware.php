<?php

namespace App\Middlewares;

use App\Core\MiddlewareInterface;
use App\Core\Redirect;
use App\Core\Session;

class AuthMiddleware implements MiddlewareInterface
{
    public function handle(): bool
    {
        Session::init();

        if (!Session::has('user_id')) {
            // AJAX (axios/fetch): ตอบ 401 JSON ตาม contract ของระบบ — หน้า client จะแจ้ง
            // "เซสชันหมดอายุ" แล้วพาไปหน้าเข้าสู่ระบบ (กัน toast สำเร็จปลอมจากการตาม redirect ไปเจอ HTML)
            if ($this->wantsJson()) {
                http_response_code(401);
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode([
                    'success'         => false,
                    'type'            => 'error',
                    'session_expired' => true,
                    'message'         => __('messages.session_expired'),
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            // Not logged in, redirect to login page
            Redirect::to('/login');
        }

        // Check maintenance mode
        if (\App\Core\Settings::get('maintenance_mode') === '1' && Session::get('role_id') != 1) {
            // Include maintenance view directly
            require dirname(__DIR__) . '/Views/errors/maintenance.php';
            exit;
        }

        // Force 2FA for Admins
        if (Session::get('role_id') == 1 && \App\Core\Settings::get('force_2fa_admin', 0) == 1) {
            if (!Session::get('2fa_enabled')) {
                $uri = $_SERVER['REQUEST_URI'];
                if ($uri !== '/profile' && !str_starts_with($uri, '/profile/2fa') && $uri !== '/logout') {
                    Session::setFlash('error', __('auth.force_2fa_warning') ?? 'Administrator policy requires you to enable Two-Factor Authentication.');
                    Redirect::to('/profile');
                }
            }
        }

        return true;
    }

    /**
     * ตรวจว่า client ขอ JSON (ใช้ logic เดียวกับ Controller::isAjax)
     */
    private function wantsJson(): bool
    {
        return \App\Core\Request::wantsJson();
    }
}
