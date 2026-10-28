<?php

/**
 * 07. Dashboard & Internal API Routes
 * หน้าแดชบอร์ดหลัก และ API ภายในระบบ (Session-based)
 */

use App\Controllers\DashboardController;
use App\Controllers\NotificationController;
use App\Controllers\SearchController;
use App\Middlewares\Groups;
use App\Core\Router;

/** @var Router $router */

// ── Dashboard หลัก ──
$router->get('/dashboard', [DashboardController::class, 'index'], Groups::auth());

// ── Internal API (Session-based) ──
$router->group('/api', Groups::auth(), function (Router $r) {
    $r->get('/notifications', [NotificationController::class, 'index']);
    $r->post('/notifications/read', [NotificationController::class, 'markAsRead']);
    $r->post('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);
    $r->get('/search', [SearchController::class, 'search']);
});
