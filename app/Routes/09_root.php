<?php

/**
 * 09. Root & Fallback Routes
 * หน้าแรกของระบบ และตัวจัดการกรณีไม่พบ Route (404 Not Found)
 */

use App\Core\Router;

/** @var Router $router */

// ── หน้าแรก → Redirect ไปหน้า Login ──
$router->get('/', function (): void {
    \App\Core\Redirect::to('/login');
});

// ── 404 Handler ──
$router->fallback(function (): void {
    http_response_code(404);

    $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH'])
        && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

    if ($isAjax) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'success' => false,
            'message' => 'Endpoint not found.',
        ]);
        exit;
    }

    echo '<!DOCTYPE html><html lang="th"><head><meta charset="UTF-8">';
    echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
    echo '<title>404 Not Found</title>';
    echo '<style>body{font-family:Prompt,sans-serif;display:flex;align-items:center;justify-content:center;min-height:100vh;margin:0;background:#f8fafc;color:#1e293b}h1{font-size:4rem;margin:0;color:#6366f1}p{color:#64748b}a{color:#6366f1;text-decoration:none;font-weight:600}@media(prefers-color-scheme:dark){body{background:#0f172a;color:#e2e8f0}p{color:#94a3b8}a{color:#818cf8}}</style>';
    echo '</head><body><div style="text-align:center">';
    echo '<h1>404</h1><p>ไม่พบหน้าที่คุณกำลังค้นหา</p>';
    echo '<p style="margin-top:1rem"><a href="/dashboard">กลับไปหน้าแดชบอร์ด</a></p>';
    echo '</div></body></html>';
});
