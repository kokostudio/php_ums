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

        return true;
    }
}
