<?php

namespace App\Middlewares;

use App\Core\MiddlewareInterface;
use App\Core\Session;

/**
 * AdminMiddleware — ตรวจสอบว่าผู้ใช้เป็นผู้ดูแลระบบ (role_id = 1)
 *
 * ต้องใช้ร่วมกับ AuthMiddleware เสมอ (ดูกลุ่ม Groups::admin())
 */
class AdminMiddleware implements MiddlewareInterface
{
    public function handle(): bool
    {
        Session::init();

        if ((int) Session::get('role_id') !== 1) {
            // ถ้าเป็น AJAX Request ให้ตอบกลับเป็น JSON
            if (\App\Core\Request::wantsJson()) {
                http_response_code(403);
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode([
                    'success' => false,
                    'type'    => 'error',
                    'message' => __('messages.access_denied') ?? 'Access denied.',
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            Session::setFlash('error', __('messages.access_denied') ?? 'Access denied.');
            \App\Core\Redirect::to('/dashboard');
        }

        return true;
    }
}
