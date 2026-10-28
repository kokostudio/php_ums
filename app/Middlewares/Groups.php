<?php

namespace App\Middlewares;

use App\Core\MiddlewareInterface;

/**
 * Groups — ชุด Middleware ที่ใช้ซ้ำได้ (Middleware Groups)
 *
 * ใช้ในไฟล์ Route เพื่อไม่ต้องเขียน Middleware ซ้ำ ๆ ทุกบรรทัด
 *
 * ตัวอย่าง:
 *   $router->group('/users', Groups::auth('users', 'view'), function ($r) {
 *       $r->get('/', [UserController::class, 'index']);
 *   });
 *
 * กลุ่มที่มีให้ใช้:
 *   - auth()           : ต้อง Login (Session)
 *   - guest()          : ต้องยังไม่ Login (หน้า Login/Register)
 *   - throttle()       : จำกัดความถี่ (Rate Limit)
 *   - permission()     : ตรวจสอบสิทธิ์ตาม Module + Action
 *   - jwt()            : ตรวจสอบ JWT Token (สำหรับ API)
 *   - admin()          : ต้อง Login และเป็นผู้ดูแลระบบ (role_id = 1)
 */
final class Groups
{
    /**
     * ต้องเข้าสู่ระบบ (Authentication)
     *
     * @param  array<int, MiddlewareInterface|array>  $extra  Middleware เพิ่มเติม
     */
    public static function auth(array $extra = []): array
    {
        return array_merge([AuthMiddleware::class], $extra);
    }

    /**
     * ต้องยังไม่เข้าสู่ระบบ (Guest only) — ใช้กับหน้า Login / Register
     */
    public static function guest(): array
    {
        return [GuestMiddleware::class];
    }

    /**
     * จำกัดความถี่การเรียก (Brute Force Protection)
     */
    public static function throttle(): array
    {
        return [RateLimitMiddleware::class];
    }

    /**
     * ตรวจสอบสิทธิ์ตาม Module + Action (RBAC)
     */
    public static function permission(string $module, string $action): array
    {
        return [[PermissionMiddleware::class, $module, $action]];
    }

    /**
     * ต้องเข้าสู่ระบบ + ตรวจสอบสิทธิ์ (รวมกัน)
     */
    public static function authPermission(string $module, string $action): array
    {
        return [
            AuthMiddleware::class,
            [PermissionMiddleware::class, $module, $action],
        ];
    }

    /**
     * ตรวจสอบ JWT Token (สำหรับ Stateless API)
     */
    public static function jwt(): array
    {
        return [JwtMiddleware::class];
    }

    /**
     * ต้องเป็นผู้ดูแลระบบ (role_id = 1)
     */
    public static function admin(array $extra = []): array
    {
        return array_merge([AuthMiddleware::class, AdminMiddleware::class], $extra);
    }
}
