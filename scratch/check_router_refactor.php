<?php
/**
 * ทดสอบระบบ Route ใหม่ (Route Group / Prefix / Middleware Group / Auto-load Module)
 *
 * ตรวจสอบ:
 *   1. Route ทั้งหมดถูกโหลดครบ และ Path ถูกต้อง (เทียบกับระบบเดิม)
 *   2. Middleware ทำงาน (Auth redirect, Permission, Rate Limit)
 *   3. GET Fallback Route ที่มี dynamic param ไม่ 500 อีกต่อไป
 *   4. Auto-load จาก app/Modules/<Module>/routes.php
 */
$base = 'https://php_ums.localhost';
$jar  = __DIR__ . '/cookies_router_refactor.txt';
@unlink($jar);

$fail = 0;
function check(string $label, bool $ok, string $extra = ''): void
{
    global $fail;
    echo ($ok ? '[PASS] ' : '[FAIL] ') . $label . ($extra ? "  ({$extra})" : '') . "\n";
    if (!$ok) { $fail++; }
}

function req(string $url, ?array $post = null, string $jar = '', bool $follow = false): array
{
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => $follow,
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
    $redirectUrl = curl_getinfo($ch, CURLINFO_REDIRECT_URL);
    curl_close($ch);
    return ['code' => $info['http_code'], 'body' => (string) $body, 'url' => ($redirectUrl ?: $info['url'])];
}

function csrf(string $html): ?string
{
    return preg_match('/name="csrf_token"\s+value="([^"]+)"/', $html, $m) ? $m[1] : null;
}

// ═══════════════════════════════════════════════════════════════════
// PART 1: Route Registry (Static Analysis)
// ═══════════════════════════════════════════════════════════════════
echo "=== PART 1: Route Registry ===\n";

require __DIR__ . '/../vendor/autoload.php';
\App\Core\Config::load(__DIR__ . '/../.env');

$router = new \App\Core\Router();
(new \App\Core\RouteRegistrar())->load($router);
$routes = $router->routes();

echo "Total routes: " . count($routes) . "\n";

// แปลงเป็น lookup table
$map = [];
foreach ($routes as $r) {
    $map[$r['method'] . ' ' . $r['path']] = $r;
}

// ── Route ที่ต้องมี (เทียบกับ public/index.php เดิม) ────────────
$expected = [
    'GET /lang/{locale}',
    'GET /login', 'POST /login',
    'GET /login/2fa', 'POST /login/2fa',
    'POST /logout',
    'GET /register', 'POST /register',
    'GET /verify-email', 'POST /resend-verification',
    'GET /forgot-password', 'POST /forgot-password',
    'GET /reset-password', 'POST /reset-password',
    'GET /auth/google', 'GET /auth/google/callback',
    'GET /auth/line', 'GET /auth/line/callback',
    'GET /auth/facebook', 'GET /auth/facebook/callback',
    'GET /auth/github', 'GET /auth/github/callback',
    'GET /dashboard',
    'GET /users', 'GET /users/data',
    'GET /users/create', 'POST /users/create',
    'GET /users/{id}/edit', 'POST /users/{id}/edit',
    'POST /users/{id}/delete', 'GET /users/{id}/delete',
    'GET /roles',
    'GET /roles/create', 'POST /roles/create',
    'GET /roles/{id}/edit', 'POST /roles/{id}/edit',
    'POST /roles/{id}/delete', 'GET /roles/{id}/delete',
    'GET /logs/activity', 'GET /logs/activity/data',
    'GET /logs/audit', 'GET /logs/audit/data',
    'GET /settings', 'GET /settings/{tab}',
    'POST /settings/translations/update', 'POST /settings/{tab}',
    'GET /profile', 'POST /profile',
    'POST /profile/password',
    'POST /profile/2fa/enable', 'POST /profile/2fa/disable',
    'GET /profile/sessions', 'POST /profile/sessions/{id}/revoke',
    'GET /profile/sessions/{id}/revoke',
    'POST /profile/social/{provider}/disconnect',
    'POST /api/v1/login', 'GET /api/v1/me',
    'GET /api/notifications', 'POST /api/notifications/read',
    'POST /api/notifications/read-all', 'GET /api/search',
    'GET /',
];

$missing = [];
foreach ($expected as $e) {
    if (!isset($map[$e])) { $missing[] = $e; }
}
check('ทุก Route เดิมถูกย้ายครบ (' . count($expected) . ' routes)', empty($missing), implode(', ', $missing));

// ── Prefix ของ Group ถูกต้อง ────────────────────────────────────
echo "\n--- Prefix verification ---\n";
check('GET /users (prefix /users + /)', isset($map['GET /users']));
check('GET /profile (prefix /profile + /)', isset($map['GET /profile']));
check('GET /settings (prefix /settings + /)', isset($map['GET /settings']));
check('GET /roles (prefix /roles + /)', isset($map['GET /roles']));

// ── Middleware Group ถูกติดตั้ง ──────────────────────────────────
echo "\n--- Middleware Group verification ---\n";
$usersMw = $map['GET /users']['middlewares'] ?? [];
check('GET /users มี AuthMiddleware', in_array(\App\Middlewares\AuthMiddleware::class, $usersMw, true), implode(', ', array_map(fn($m) => is_array($m) ? $m[0] : $m, $usersMw)));
check('GET /users มี PermissionMiddleware(users,view)',
    in_array(\App\Middlewares\PermissionMiddleware::class . ',users,view', array_map(
        fn($m) => is_array($m) ? $m[0] . ',' . implode(',', array_slice($m, 1)) : $m, $usersMw
    ), true));

$loginMw = $map['GET /login']['middlewares'] ?? [];
check('GET /login มี GuestMiddleware (กลุ่ม Guest)', in_array(\App\Middlewares\GuestMiddleware::class, $loginMw, true));

$loginPostMw = $map['POST /login']['middlewares'] ?? [];
check('POST /login มี Guest + RateLimit', in_array(\App\Middlewares\GuestMiddleware::class, $loginPostMw, true)
    && in_array(\App\Middlewares\RateLimitMiddleware::class, $loginPostMw, true));

$apiMeMw = $map['GET /api/v1/me']['middlewares'] ?? [];
check('GET /api/v1/me มี JwtMiddleware', in_array(\App\Middlewares\JwtMiddleware::class, $apiMeMw, true));

$apiLoginMw = $map['POST /api/v1/login']['middlewares'] ?? [];
check('POST /api/v1/login มี RateLimit', in_array(\App\Middlewares\RateLimitMiddleware::class, $apiLoginMw, true));

// ═══════════════════════════════════════════════════════════════════
// PART 2: Auto-load Module
// ═══════════════════════════════════════════════════════════════════
echo "\n=== PART 2: Auto-load Module ===\n";

$moduleDir = dirname(__DIR__) . '/app/Modules/Demo';
if (!is_dir($moduleDir)) {
    @mkdir($moduleDir, 0755, true);
    file_put_contents($moduleDir . '/routes.php', <<<'PHP'
<?php
/**
 * Demo Module Routes — ตัวอย่างการ Auto-load Route ของ Module ส่วนขยาย
 */

use App\Controllers\DashboardController;
use App\Middlewares\Groups;

use App\Core\Router;

/** @var Router $router */

$router->group('/demo', Groups::auth(), function (Router $r) {
    $r->get('/', [DashboardController::class, 'index'], [], 'demo.index');
    $r->get('/ping', function (): void {
        echo json_encode(['ok' => true, 'module' => 'Demo']);
    });
});
PHP);
    echo "(สร้าง Demo Module สำหรับทดสอบ)\n";
}

$router2 = new \App\Core\Router();
(new \App\Core\RouteRegistrar())->load($router2);
$map2 = [];
foreach ($router2->routes() as $r) {
    $map2[$r['method'] . ' ' . $r['path']] = $r;
}
check('Module route ถูก Auto-load (GET /demo)', isset($map2['GET /demo']));
check('Module route ถูก Auto-load (GET /demo/ping)', isset($map2['GET /demo/ping']));
check('Module route มีชื่อ (name = demo.index)', ($map2['GET /demo']['name'] ?? null) === 'demo.index');

// ═══════════════════════════════════════════════════════════════════
// PART 3: HTTP Behaviour
// ═══════════════════════════════════════════════════════════════════
echo "\n=== PART 3: HTTP Behaviour ===\n";

// ── 3.1 Route ที่ไม่มีอยู่ → 404 ─────────────────────────────────
$r = req("{$base}/this-route-does-not-exist-xyz", null, $jar);
check('Route ที่ไม่มีอยู่ ตอบ 404', $r['code'] === 404, "HTTP {$r['code']}");
check('404 มีหน้าแสดงผลสวยงาม', str_contains($r['body'], '404') && str_contains($r['body'], 'dashboard'));

// ── 3.2 Protected route ยังไม่ Login → redirect /login ──────────
$r = req("{$base}/dashboard", null, $jar);
check('GET /dashboard (ยังไม่ Login) → redirect', in_array($r['code'], [302, 301], true), "HTTP {$r['code']} → {$r['url']}");
check('redirect ไปที่ /login', str_contains($r['url'], '/login'), $r['url']);

// ── 3.3 GET Fallback Route ที่มี dynamic param ───────────────────
echo "\n--- GET Fallback with dynamic param (เดิมพัง 500) ---\n";
$login = req("{$base}/login", null, $jar);
req("{$base}/login", ['username' => 'superadmin', 'password' => 'password', 'csrf_token' => csrf($login['body'])], $jar);

$r = req("{$base}/users/abc/delete", null, $jar);
check('GET /users/{id}/delete → ไม่ 500 (เดิมพัง Unknown named parameter)', $r['code'] !== 500, "HTTP {$r['code']}");
check('GET /users/{id}/delete → redirect ไป /users', str_contains($r['url'], '/users'), $r['url']);

$r = req("{$base}/roles/abc/delete", null, $jar);
check('GET /roles/{id}/delete → ไม่ 500', $r['code'] !== 500, "HTTP {$r['code']}");

$r = req("{$base}/profile/sessions/5/revoke", null, $jar);
check('GET /profile/sessions/{id}/revoke → ไม่ 500', $r['code'] !== 500, "HTTP {$r['code']}");
check('GET /profile/sessions/{id}/revoke → redirect ไป /profile/sessions', str_contains($r['url'], '/profile/sessions'), $r['url']);

// ── 3.4 หน้าหลักที่ Login แล้ว ต้องเข้าถึงได้ ────────────────
echo "\n--- Authenticated pages ---\n";
foreach ([
    '/dashboard'         => 'Dashboard',
    '/users'             => 'Users',
    '/roles'             => 'Roles',
    '/logs/activity'     => 'Activity Log',
    '/logs/audit'        => 'Audit Log',
    '/settings/general'  => 'Settings',
    '/settings/social'   => 'Settings Social',
    '/profile'           => 'Profile',
    '/profile/sessions'  => 'Sessions',
] as $path => $label) {
    $resp = req("{$base}{$path}", null, $jar);
    check("GET {$path} ({$label}) → 200", $resp['code'] === 200, "HTTP {$resp['code']}");
}

// ── 3.5 Demo Module ─────────────────────────────────────────────
$r = req("{$base}/demo/ping", null, $jar);
check('GET /demo/ping (Module) → 200', $r['code'] === 200, "HTTP {$r['code']}");
check('GET /demo/ping คืน JSON ถูกต้อง', str_contains($r['body'], '"ok":true'));

// ── 3.6 Guest Middleware: Login แล้วเข้า /login ต้องถูกเด้ง ────
$r = req("{$base}/login", null, $jar);
check('GET /login (Login แล้ว) → redirect /dashboard', str_contains($r['url'], '/dashboard'), $r['url']);

// ── 3.7 API v1 ต้องมี JWT ───────────────────────────────────────
$r = req("{$base}/api/v1/me", null, $jar);
check('GET /api/v1/me (ไม่มี JWT) → 401', $r['code'] === 401, "HTTP {$r['code']}");

// ── 3.8 API ภายใน (Session-based) ───────────────────────────────
$r = req("{$base}/api/notifications", null, $jar);
check('GET /api/notifications (Login แล้ว) → 200', $r['code'] === 200, "HTTP {$r['code']}");

@unlink($jar);

echo "\n" . ($fail === 0 ? "All checks passed.\n" : "{$fail} check(s) FAILED.\n");
exit($fail === 0 ? 0 : 1);
