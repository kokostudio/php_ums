<?php
/**
 * ตรวจสอบว่า Layout ทั้งหมด (app + auth) ใช้ธีมเดียวกัน
 */
$base = 'https://php_ums.localhost';
$jar  = __DIR__ . '/cookies2.txt';
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

// ตั้งค่าเป็นสีม่วงเฉพาะกิ๊ก
$set = req("{$base}/settings/general", null, $jar);
req("{$base}/settings/general", [
    'csrf_token'          => csrf($set['body']),
    'site_name'           => 'UMS',
    'site_footer'         => 'footer',
    'base_font_size'      => 'text-[16px]',
    'sidebar_theme'       => 'custom',
    'sidebar_custom_color' => '#7C3AED',
], $jar);

echo "=== Login page (auth layout, no session) ===\n";
$logout = req("{$base}/logout", null, $jar);
$authPage = req("{$base}/login", null, $jar);
preg_match('/sidebar:\s*\'([^\']+)\'/', $authPage['body'], $m);
echo "auth layout sidebar color: " . ($m[1] ?? 'NOT FOUND') . "\n";
preg_match('/--primary-500:\s*([^;]+);/', $authPage['body'], $m);
echo "auth layout --primary-500: " . trim($m[1] ?? 'NOT FOUND') . "\n";
preg_match('/--primary-hex:\s*([^;]+);/', $authPage['body'], $m);
echo "auth layout --primary-hex: " . trim($m[1] ?? 'NOT FOUND') . "\n";

echo "\n=== App layout pages ===\n";
foreach (['/dashboard', '/users', '/roles', '/settings/general', '/profile'] as $path) {
    $r = req("{$base}{$path}", null, $jar);
    preg_match('/--sidebar-bg:\s*([^;]+);/', $r['body'], $m);
    preg_match('/sidebar:\s*\'([^\']+)\'/', $r['body'], $m2);
    printf("%-22s HTTP %d  sidebar-bg=%-10s tailwind.sidebar=%-10s\n",
        $path, $r['code'], trim($m[1] ?? '-'), $m2[1] ?? '-');
}

// ตรวจสอบว่า bg-sidebar class ถูกใช้ (สีปุ่มหลักควรตรงกับ sidebar)
$r = req("{$base}/settings/general", null, $jar);
echo "\nbg-sidebar usage on settings page: " . (str_contains($r['body'], 'bg-sidebar') ? 'yes' : 'no') . "\n";
preg_match('/sidebar:\s*\'([^\']+)\'/', $r['body'], $m2);
echo "tailwind sidebar value: " . ($m2[1] ?? 'NOT FOUND') . "\n";

// คืนค่าเดิม
req("{$base}/settings/general", [
    'csrf_token'          => csrf($r['body']),
    'site_name'           => 'UMS',
    'site_footer'         => 'footer',
    'base_font_size'      => 'text-[16px]',
    'sidebar_theme'       => 'indigo',
    'sidebar_custom_color' => '#7C3AED',
], $jar);

@unlink($jar);
echo "\nDone.\n";
