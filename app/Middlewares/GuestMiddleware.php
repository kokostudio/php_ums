<?php

namespace App\Middlewares;

use App\Core\MiddlewareInterface;
use App\Core\Session;

/**
 * GuestMiddleware — ป้องกันไม่ให้ผู้ที่เข้าสู่ระบบแล้วเข้าถึงหน้า Guest (Login / Register)
 *
 * เมื่อผู้ใช้ Login อยู่แล้ว แล้วพยายามเปิดหน้า Login อีกครั้ง
 * ระบบจะ Redirect ไปยัง Dashboard แทน
 */
class GuestMiddleware implements MiddlewareInterface
{
    public function handle(): bool
    {
        Session::init();

        if (Session::has('user_id')) {
            header('Location: /dashboard');
            exit;
        }

        return true;
    }
}
