<?php
/**
 * ตรวจสอบ auth layout (หน้า Login) ใช้ธีมเดียวกับ app layout
 */
$base = 'https://php_ums.localhost';
$jar  = __DIR__ . '/cookies3.txt';
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

$fail = 0;
function check(string $label, bool $ok, string $detail = ''): void
{
    global $fail;
    if (!$ok) { $fail++; }
    echo '[' . ($ok ? 'PASS' : 'FAIL') . "] {$label}" . ($detail !== '' ? " — {$detail}" : '') . "\n";
}

// ตั้งค่าเป็นสีม่วงก่อน (ผ่าน session ที่ล็อกอินแล้ว)
$login = req("{$base}/login", null, $jar);
req("{$base}/login", ['username' => 'superadmin', 'password' => 'password', 'csrf_token' => csrf($login['body'])], $jar);
$set = req("{$base}/settings/general", null, $jar);
req("{$base}/settings/general", [
    'csrf_token'          => csrf($set['body']),
    'site_name'           => 'UMS',
    'site_footer'         => 'footer',
    'base_font_size'      => 'text-[16px]',
    'sidebar_theme'       => 'custom',
    'sidebar_custom_color' => '#7C3AED',
], $jar);

// ── ตรวจสอบหน้า Login ด้วย session ใหม่ (ไม่ล็อกอิน) ──
$jar2 = __DIR__ . '/cookies4.txt';
@unlink($jar2);

$auth = req("{$base}/login", null, $jar2);
check('Login page loads', $auth['code'] === 200, "HTTP {$auth['code']}");

preg_match('/sidebar:\s*\'([^\']+)\'/', $auth['body'], $m);
check('auth layout tailwind.sidebar = picked color', strtolower($m[1] ?? '') === '#7c3aed', "value: " . ($m[1] ?? 'NOT FOUND'));

preg_match('/"primary":\s*\{(.*?)\}/s', $auth['body'], $m);
$primaryBlock = $m[1] ?? '';
check('auth layout primary palette injected', str_contains($primaryBlock, '"500"'), 'has 500 key');
check('auth layout primary uses alpha-value format', str_contains($primaryBlock, 'alpha-value'));

// ตรวจสอบว่าปุ่มในหน้า login ใช้ bg-sidebar (สีปุ่มจะตรงกับ sidebar)
check('auth page uses bg-sidebar button', str_contains($auth['body'], 'bg-sidebar'));
check('auth page no longer hardcodes blue submit button', !preg_match('/type="submit"[^>]*bg-blue-600/', $auth['body']));

// ── หน้า 2FA / forgot password ก็ใช้ auth layout ──
foreach (['/forgot-password', '/reset-password', '/login/2fa'] as $path) {
    $r = req("{$base}{$path}", null, $jar2);
    preg_match('/sidebar:\s*\'([^\']+)\'/', $r['body'], $m);
    check("{$path} uses same theme", strtolower($m[1] ?? '') === '#7c3aed', "HTTP {$r['code']}, sidebar: " . ($m[1] ?? 'NOT FOUND'));
}

// ── คืนค่าเดิม ──
$restore = req("{$base}/settings/general", null, $jar);
req("{$base}/settings/general", [
    'csrf_token'          => csrf($restore['body']),
    'site_name'           => 'UMS',
    'site_footer'         => 'footer',
    'base_font_size'      => 'text-[16px]',
    'sidebar_theme'       => 'indigo',
    'sidebar_custom_color' => '#7C3AED',
], $jar);

@unlink($jar);
@unlink($jar2);

echo $fail === 0 ? "\nAll auth-layout checks passed.\n" : "\n{$fail} check(s) FAILED.\n";
exit($fail === 0 ? 0 : 1);
