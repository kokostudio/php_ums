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

        return true;
    }
}
