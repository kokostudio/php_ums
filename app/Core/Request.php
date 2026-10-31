<?php

namespace App\Core;

/**
 * Request — ตัวช่วยอ่านข้อมูล Request กลางของระบบ
 *
 * ใช้ logic เดียวกันทั้งระบบ (แทนการ copy กันหลายที่):
 *   Controller::isAjax() / Middlewares / ErrorHandler / Router ต่างเรียกผ่านคลาสนี้
 */
final class Request
{
    /**
     * request นี้ขอ JSON หรือไม่ (axios/fetch ที่แอปใช้งานส่งทั้งสอง header ให้เสมอ)
     */
    public static function wantsJson(): bool
    {
        $requestedWith = strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '');
        $accept        = strtolower($_SERVER['HTTP_ACCEPT'] ?? '');

        return $requestedWith === 'xmlhttprequest' || str_contains($accept, 'application/json');
    }
}
