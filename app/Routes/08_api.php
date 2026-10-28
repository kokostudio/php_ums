<?php

/**
 * 08. Public API Routes (Stateless / JWT)
 * API สำหรับ Mobile App หรือระบบภายนอก — ไม่ใช้ Session
 */

use App\Controllers\Api\AuthController;
use App\Middlewares\Groups;
use App\Core\Router;

/** @var Router $router */

$router->group('/api/v1', [], function (Router $r) {
    // ── Public (ไม่ต้อง Authenticate) ──
    $r->post('/login', [AuthController::class, 'login'], Groups::throttle());

    // ── Protected (ต้องมี JWT Token) ──
    $r->get('/me', [AuthController::class, 'me'], Groups::jwt());
});
