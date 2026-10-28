<?php
/**
 * ตรวจสอบ GET Fallback Route ที่มี dynamic param
 * เช่น GET /users/{id}/delete -> closure ที่ไม่รับ argument
 */
$base = 'https://php_ums.localhost';
$jar  = __DIR__ . '/cookies_router_test.txt';
@unlink($jar);

function req(string $url, ?array $post = null, string $jar = ''): array
{
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => false,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_COOKIEJAR      => $jar,
        CURLOPT_COOKIEFILE     => $jar,
    ]);
    if ($post !== null) {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
    }
    $body = curl_exec($ch);
    $info = curl_getinfo($ch);
    curl_close($ch);
    return ['code' => $info['http_code'], 'body' => (string) $body];
}

function csrf(string $html): ?string
{
    return preg_match('/name="csrf_token"\s+value="([^"]+)"/', $html, $m) ? $m[1] : null;
}

// Login
$login = req("{$base}/login", null, $jar);
req("{$base}/login", ['username' => 'superadmin', 'password' => 'password', 'csrf_token' => csrf($login['body'])], $jar);

echo "=== GET /users/abc/delete (fallback closure, no param accepted) ===\n";
$r = req("{$base}/users/abc/delete", null, $jar);
echo "HTTP {$r['code']}\n";
$body = preg_replace('/\s+/', ' ', strip_tags($r['body']));
echo "Body (truncated): " . mb_substr($body, 0, 300) . "\n";

echo "\n=== GET /profile/sessions/5/revoke (fallback closure) ===\n";
$r2 = req("{$base}/profile/sessions/5/revoke", null, $jar);
echo "HTTP {$r2['code']}\n";
$body2 = preg_replace('/\s+/', ' ', strip_tags($r2['body']));
echo "Body (truncated): " . mb_substr($body2, 0, 300) . "\n";

@unlink($jar);
