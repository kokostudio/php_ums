<?php

/**
 * 02. User Management Routes
 * จัดการผู้ใช้ (ผู้ดูแลระบบเท่านั้น)
 */

use App\Controllers\UserController;
use App\Middlewares\Groups;
use App\Core\Router;

/** @var Router $router */

$router->group('/users', Groups::auth(), function (Router $r) {
    // ── รายการผู้ใช้ + DataTables ──
    $r->get('/', [UserController::class, 'index'], Groups::permission('users', 'view'));
    $r->get('/data', [UserController::class, 'data'], Groups::permission('users', 'view'));

    // ── สร้างผู้ใช้ใหม่ ──
    $r->get('/create', [UserController::class, 'create'], Groups::permission('users', 'create'));
    $r->post('/create', [UserController::class, 'store'], Groups::permission('users', 'create'));

    // ── แก้ไขผู้ใช้ ──
    $r->get('/{id}/edit', [UserController::class, 'edit'], Groups::permission('users', 'edit'));
    $r->post('/{id}/edit', [UserController::class, 'update'], Groups::permission('users', 'edit'));

    // ── ลบผู้ใช้ (POST หลัก) ──
    $r->post('/{id}/delete', [UserController::class, 'delete'], Groups::permission('users', 'delete'));

    // ── GET Fallback: กันไม่ให้เปิด URL ตรง ๆ แล้วได้ 404 (ตามกฎ Rule 8) ──
    $r->get('/{id}/delete', function (): void {
        \App\Core\Redirect::to('/users');
    });
});
