<?php
/**
 * ตรวจสอบ Scrollbar ใช้สีตามธีม Sidebar (แบบอ่านอย่างเดียว ไม่แก้ไข Setting)
 */
$base = 'https://php_ums.localhost';
$jar  = __DIR__ . '/cookies_ro.txt';
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

// Login
$login = req("{$base}/login", null, $jar);
req("{$base}/login", ['username' => 'superadmin', 'password' => 'password', 'csrf_token' => csrf($login['body'])], $jar);

echo "=== ตรวจ CSS variables ของ Scrollbar บนแต่ละหน้า ===\n";

$pages = [
    '/dashboard'        => 'app layout',
    '/settings/general' => 'app layout',
    '/users'            => 'app layout',
];

$expected = [];
foreach ($pages as $path => $label) {
    $r = req("{$base}{$path}", null, $jar);

    preg_match('/--sidebar-bg:\s*([^;]+);/', $r['body'], $mBg);
    preg_match('/--scrollbar-thumb:\s*([^;]+);/', $r['body'], $mThumb);
    preg_match('/--scrollbar-thumb-dark:\s*([^;]+);/', $r['body'], $mDark);
    preg_match('/--scrollbar-sidebar-thumb:\s*([^;]+);/', $r['body'], $mSide);

    // ต้องมี CSS rule ที่อ้าง var(--scrollbar-...)
    $usesVars = substr_count($r['body'], 'var(--scrollbar-');

    $ok = $r['code'] === 200
        && !empty($mThumb[1]) && !empty($mDark[1]) && !empty($mSide[1])
        && $usesVars >= 4;

    if (!$ok) { $fail++; }
    $expected['sidebar-bg'] = trim($mBg[1] ?? '-');

    printf("[%s] %-18s HTTP %d  bg=%-9s thumb=%-9s dark=%-9s sidebar=%-9s (var refs: %d)\n",
        $ok ? 'PASS' : 'FAIL',
        $path,
        $r['code'],
        trim($mBg[1] ?? '-'),
        trim($mThumb[1] ?? '-'),
        trim($mDark[1] ?? '-'),
        trim($mSide[1] ?? '-'),
        $usesVars
    );
}

// ตรวจว่าทุกหน้าใช้สีเดียวกัน (ผูกกับ setting เดียวกัน)
echo "\n=== ตรวจว่าทุกหน้าใช้สีเดียวกัน ===\n";
$colors = [];
foreach (array_keys($pages) as $path) {
    $r = req("{$base}{$path}", null, $jar);
    preg_match('/--scrollbar-thumb:\s*([^;]+);/', $r['body'], $m);
    $colors[$path] = trim($m[1] ?? '-');
}
$same = count(array_unique($colors)) === 1;
if (!$same) { $fail++; }
printf("[%s] สี Scrollbar ทุกหน้า: %s\n", $same ? 'PASS' : 'FAIL', json_encode($colors));

// ตรวจ auth layout (ใช้ cookie jar ใหม่เพื่อไม่ให้ session ค้าง)
$authJar = __DIR__ . '/cookies_auth.txt';
@unlink($authJar);
$auth = req("{$base}/login", null, $authJar);
preg_match('/--scrollbar-thumb:\s*([^;]+);/', $auth['body'], $m);
preg_match('/--scrollbar-sidebar-thumb:\s*([^;]+);/', $auth['body'], $m2);
$authOk = $auth['code'] === 200 && !empty($m[1]) && !empty($m2[1]);
if (!$authOk) { $fail++; }
printf("[%s] /login (auth layout) HTTP %d  thumb=%-9s sidebar=%-9s\n",
    $authOk ? 'PASS' : 'FAIL',
    $auth['code'],
    trim($m[1] ?? '-'),
    trim($m2[1] ?? '-')
);

@unlink($jar);
@unlink($authJar);
echo $fail === 0 ? "\nAll checks passed.\n" : "\n{$fail} check(s) FAILED.\n";
exit($fail === 0 ? 0 : 1);
