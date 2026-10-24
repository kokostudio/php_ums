<?php
/**
 * End-to-end test: ตรวจสอบ Sidebar Color Picker ผ่าน HTTP จริง (login → save → reload)
 * ใช้ cookie jar เพื่อจำลอง session เดียวกับ browser
 */
$base = 'https://php_ums.localhost';
$jar  = __DIR__ . '/cookies.txt';
@unlink($jar);

function request(string $url, ?array $post = null, string $jar = ''): array
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
    $code = $info['http_code'];
    $loc  = $info['redirect_url'] ?? '';
    curl_close($ch);

    return ['code' => $code, 'body' => (string) $body, 'location' => $loc];
}

function csrfToken(string $html): ?string
{
    if (preg_match('/name="csrf_token"\s+value="([^"]+)"/', $html, $m)) {
        return $m[1];
    }
    if (preg_match('/name=\'csrf_token\'\s+value=\'([^\']+)\'/', $html, $m)) {
        return $m[1];
    }
    return null;
}

$fail = 0;
function check(string $label, bool $ok, string $detail = ''): void
{
    global $fail;
    if (!$ok) {
        $fail++;
    }
    echo '[' . ($ok ? 'PASS' : 'FAIL') . "] {$label}" . ($detail !== '' ? " — {$detail}" : '') . "\n";
}

// ── 1. Login ──
$login = request("{$base}/login", null, $jar);
$token = csrfToken($login['body']);
check('Login page loads', $login['code'] === 200, "HTTP {$login['code']}");
check('CSRF token found on login page', $token !== null);

if ($token === null) {
    echo "Cannot continue without CSRF token.\n";
    exit(1);
}

$res = request("{$base}/login", [
    'username'   => 'superadmin',
    'password'   => 'password',
    'csrf_token' => $token,
], $jar);

check('Login accepted (redirect to non-login)', $res['code'] === 302 && !str_contains($res['location'], '/login'),
    "HTTP {$res['code']} -> {$res['location']}");

// ── 2. Settings page renders ──
$settings = request("{$base}/settings/general", null, $jar);
check('Settings /general accessible', $settings['code'] === 200, "HTTP {$settings['code']}");
check('Not redirected to login', $settings['code'] !== 302);

$html = $settings['body'];

check('Color picker input present (sidebar_custom_color)', str_contains($html, 'name="sidebar_custom_color"'));
check('Color input id present', str_contains($html, 'id="sidebarColorInput"'));
check('HEX text input present', str_contains($html, 'id="sidebarColorHex"'));
check('Reset button present', str_contains($html, 'id="sidebarColorReset"'));
check('Preview swatch present', str_contains($html, 'id="sidebarPreviewSwatch"'));
check('Error line present', str_contains($html, 'id="sidebarColorError"'));
check('Custom option radio present', str_contains($html, 'value="custom"'));
check('All 5 preset radios present',
    str_contains($html, 'value="slate"') && str_contains($html, 'value="black"')
    && str_contains($html, 'value="indigo"') && str_contains($html, 'value="emerald"')
    && str_contains($html, 'value="rose"'));

// ตรวจสอบว่าไม่มี label เก่าที่ hardcode สี (ควรมาจาก Theme::PRESETS loop)
check('No hardcoded preset swatch classes left', !str_contains($html, 'bg-indigo-950 border border-indigo-900/50'));

// ตรวจสอบ CSS variables ถูกส่งออกมา
preg_match('/--sidebar-bg:\s*([^;]+);/', $html, $m);
$sidebarBg = $m[1] ?? '';
check('CSS var --sidebar-bg emitted', $sidebarBg !== '', "value: " . trim($sidebarBg));

preg_match('/--primary-500:\s*([^;]+);/', $html, $m);
check('CSS var --primary-500 emitted', isset($m[1]), "value: " . trim($m[1] ?? ''));

// ── 3. Save custom color ──
$saveToken = csrfToken($html);
check('CSRF token on settings page', $saveToken !== null);

$res = request("{$base}/settings/general", [
    'csrf_token'         => $saveToken,
    'site_name'          => 'UMS',
    'site_footer'        => 'footer',
    'base_font_size'     => 'text-[16px]',
    'sidebar_theme'      => 'custom',
    'sidebar_custom_color' => '#7C3AED',
], $jar);

check('Save redirected', $res['code'] === 302, "HTTP {$res['code']} -> {$res['location']}");

// ── 4. Reload and verify persistence ──
$after = request("{$base}/settings/general", null, $jar);
check('Reload after save works', $after['code'] === 200, "HTTP {$after['code']}");

$html2 = $after['body'];
check('Custom radio is checked after reload', (bool) preg_match('/value="custom"[^>]*checked/', $html2));
check('Saved color persisted in input', str_contains($html2, 'value="#7c3aed"') || str_contains($html2, 'value="#7C3AED"'),
    'looking for #7c3aed in color input');

preg_match('/--sidebar-bg:\s*([^;]+);/', $html2, $m);
$bgAfter = trim($m[1] ?? '');
check('Sidebar bg CSS var reflects saved color', strtolower($bgAfter) === '#7c3aed', "value: {$bgAfter}");

preg_match('/--primary-500:\s*([^;]+);/', $html2, $m);
check('primary-500 regenerated from picked color', isset($m[1]) && $m[1] !== '99 102 241', "value: " . trim($m[1] ?? ''));

// ── 5. Dashboard uses the same theme ──
$dash = request("{$base}/dashboard", null, $jar);
check('Dashboard loads', $dash['code'] === 200, "HTTP {$dash['code']}");
preg_match('/--sidebar-bg:\s*([^;]+);/', $dash['body'], $m);
check('Dashboard sidebar uses same color', strtolower(trim($m[1] ?? '')) === '#7c3aed', "value: " . trim($m[1] ?? ''));

// ── 6. Invalid color is rejected & prior value preserved ──
$token2 = csrfToken($html2);
$res = request("{$base}/settings/general", [
    'csrf_token'          => $token2,
    'site_name'           => 'UMS',
    'site_footer'         => 'footer',
    'base_font_size'      => 'text-[16px]',
    'sidebar_theme'       => 'custom',
    'sidebar_custom_color' => 'javascript:alert(1)',
], $jar);

$after2 = request("{$base}/settings/general", null, $jar);
preg_match('/--sidebar-bg:\s*([^;]+);/', $after2['body'], $m);
check('Invalid color rejected (previous value kept)', strtolower(trim($m[1] ?? '')) === '#7c3aed', "value: " . trim($m[1] ?? ''));

// ── 7. Restore original preset ──
$token3 = csrfToken($after2['body']);
request("{$base}/settings/general", [
    'csrf_token'          => $token3,
    'site_name'           => 'UMS',
    'site_footer'         => 'footer',
    'base_font_size'      => 'text-[16px]',
    'sidebar_theme'       => 'indigo',
    'sidebar_custom_color' => '#7C3AED',
], $jar);

$restored = request("{$base}/settings/general", null, $jar);
preg_match('/--sidebar-bg:\s*([^;]+);/', $restored['body'], $m);
check('Preset mode still works (indigo)', strtolower(trim($m[1] ?? '')) === '#1e1b4b', "value: " . trim($m[1] ?? ''));

@unlink($jar);

echo $fail === 0 ? "\nAll E2E checks passed.\n" : "\n{$fail} check(s) FAILED.\n";
exit($fail === 0 ? 0 : 1);
