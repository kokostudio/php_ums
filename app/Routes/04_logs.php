<?php

/**
 * 04. Activity & Audit Logs Routes
 * บันทึกกิจกรรมและบันทึกการเปลี่ยนแปลงข้อมูล (Audit Logs)
 */

use App\Controllers\LogController;
use App\Middlewares\Groups;
use App\Core\Router;

/** @var Router $router */

$router->group('/logs', Groups::authPermission('logs', 'view'), function (Router $r) {
    // ── Activity Log ──
    $r->get('/activity', [LogController::class, 'activity']);
    $r->get('/activity/data', [LogController::class, 'activityData']);

    // ── Audit Log ──
    $r->get('/audit', [LogController::class, 'audit']);
    $r->get('/audit/data', [LogController::class, 'auditData']);
});
