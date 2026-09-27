<?php

namespace App\Middlewares;

use App\Core\MiddlewareInterface;
use App\Core\Session;

class AuthMiddleware implements MiddlewareInterface
{
    public function handle(): bool
    {
        Session::init();
        
        if (!Session::has('user_id')) {
            // Not logged in, redirect to login page
            header("Location: /login");
            exit;
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
                    header("Location: /profile");
                    exit;
                }
            }
        }

        return true;
    }
}
