<?php

namespace App\Core;

/**
 * Redirect — ตัวส่ง Location Header กลางของระบบ (จุดเดียว)
 *
 * ทุกการ redirect ภายในระบบต้องผ่านคลาสนี้เพื่อให้มีการตรวจปลายทางแบบรวมศูนย์:
 *   - Redirect::to('/path')                     → ใช้เส้นทางภายใน (relative path เท่านั้น)
 *   - Redirect::external($url, $allowedHosts)   → URL ภายนอก ต้องผ่าน host allowlist เสมอ
 *
 * ป้องกัน Open Redirect (ห้าม absolute/protocol-relative ใน to())
 * และ Header Injection (กรอง CRLF ออกจากปลายทางทุกกรณี)
 */
final class Redirect
{
    /**
     * Redirect ไปยังเส้นทางภายในระบบ — อนุญาตเฉพาะ relative path เท่านั้น
     */
    public static function to(string $url): never
    {
        $parts = parse_url($url);

        $isInternalPath = is_array($parts)
            && !isset($parts['scheme'])
            && !isset($parts['host'])
            && isset($parts['path']);

        if (!$isInternalPath) {
            throw new \Exception('Blocked redirect: only internal relative paths are allowed.', 500);
        }

        $safeUrl = (string) filter_var($url, FILTER_SANITIZE_URL);
        header("Location: {$safeUrl}");
        exit;
    }

    /**
     * Redirect ไปยัง URL ภายนอก (เช่น หน้า Authorize ของ OAuth Provider)
     * อนุญาตเฉพาะ https + host ที่อยู่ใน allowlist เท่านั้น
     */
    public static function external(string $url, array $allowedHosts): never
    {
        $parts = parse_url($url);

        $isAllowedExternal = is_array($parts)
            && ($parts['scheme'] ?? '') === 'https'
            && isset($parts['host'])
            && in_array(strtolower($parts['host']), array_map('strtolower', $allowedHosts), true);

        if (!$isAllowedExternal) {
            throw new \Exception('Blocked external redirect: host is not in the trusted allowlist.', 500);
        }

        $safeUrl = (string) filter_var($url, FILTER_SANITIZE_URL);
        header("Location: {$safeUrl}");
        exit;
    }
}
