<?php

namespace App\Core;

/**
 * SecurityHeaders — ส่ง HTTP security headers กลางของระบบ
 *
 * เรียกครั้งเดียวที่ public/index.php ก่อน dispatch เพื่อให้ header ครอบทุก response
 * (รวม 404 / error page / JSON) ไม่ใช่เฉพาะหน้าที่ match route
 *
 * หมายเหตุ CSP: ระบบมี inline JS/CSS และ CDN (Tailwind, SweetAlert2, Google Font) จำนวนมาก
 * จึงเริ่มด้วย Content-Security-Policy-Report-Only เพื่อเก็บรายงานก่อนพิจารณาบังคับใช้จริง
 * (เมื่อไร inline JS ถูกแยกเป็นไฟล์ + nonce แล้ว จึงเปลี่ยนเป็น CSP enforce ได้)
 */
final class SecurityHeaders
{
    public static function send(): void
    {
        // ป้องกัน clickjacking — ห้ามถูกฝังใน iframe
        header('X-Frame-Options: DENY');

        // ป้องกัน browser เดา MIME type (sniffing)
        header('X-Content-Type-Options: nosniff');

        // ส่งเฉพาะ origin เมื่อข้ามไป site อื่น
        header('Referrer-Policy: strict-origin-when-cross-origin');

        // ปิดฟีเจอร์เบราว์เซอร์ที่เว็บแอปนี้ไม่ใช้
        header('Permissions-Policy: camera=(), microphone=(), geolocation=(), payment=(), usb=()');

        $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https')
            || ($_SERVER['SERVER_PORT'] ?? '') == '443';

        // HSTS เฉพาะ HTTPS (ส่งทาง HTTP ไม่มีผลและอาจทำสับสน)
        if ($isHttps) {
            header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
        }

        // CSP โหมดรายงาน — เก็บ violation เพื่อประเมินก่อนบังคับใช้
        // อนุญาต: Tailwind CDN, SweetAlert2 CDN, jQuery CDN, Google Fonts, inline (ช่วงเปลี่ยนผ่าน),
        // self สำหรับรูป/font, data: สำหรับรูป inline (avatar จาก DB path /uploads)
        $csp = implode('; ', [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' https://cdn.tailwindcss.com https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://unpkg.com https://code.jquery.com",
            "style-src 'self' 'unsafe-inline' https://cdn.tailwindcss.com https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://fonts.googleapis.com",
            "font-src 'self' https://fonts.gstatic.com https://cdn.jsdelivr.net data:",
            "img-src 'self' data: blob: https:",
            "connect-src 'self'",
            "frame-ancestors 'none'",
            "base-uri 'self'",
            "form-action 'self'",
        ]);
        header('Content-Security-Policy-Report-Only: ' . $csp);
    }
}
