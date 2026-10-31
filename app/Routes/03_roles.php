<?php

/**
 * 03. Roles & Permissions Routes
 * จัดการบทบาทและสิทธิ์การเข้าถึง
 */

use App\Controllers\RoleController;
use App\Middlewares\Groups;
use App\Core\Router;

/** @var Router $router */

$router->group('/roles', Groups::auth(), function (Router $r) {
    // ── รายการบทบาท ──
    $r->get('/', [RoleController::class, 'index'], Groups::permission('roles', 'view'));

    // ── สร้างบทบาทใหม่ ──
    $r->get('/create', [RoleController::class, 'create'], Groups::permission('roles', 'create'));
    $r->post('/create', [RoleController::class, 'store'], Groups::permission('roles', 'create'));

    // ── แก้ไขบทบาท ──
    $r->get('/{id}/edit', [RoleController::class, 'edit'], Groups::permission('roles', 'edit'));
    $r->post('/{id}/edit', [RoleController::class, 'update'], Groups::permission('roles', 'edit'));

    // ── ลบบทบาท ──
    $r->post('/{id}/delete', [RoleController::class, 'delete'], Groups::permission('roles', 'delete'));

    // ── GET Fallback (ตามกฎ Rule 8) ──
    $r->get('/{id}/delete', function (): void {
        \App\Core\Redirect::to('/roles');
    });
});
