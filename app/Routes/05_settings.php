<?php

/**
 * 05. System Settings Routes
 * ตั้งค่าระบบ (ทุกแท็บใช้โครงสร้าง Clean URL)
 */

use App\Controllers\SettingController;
use App\Middlewares\Groups;
use App\Core\Router;

/** @var Router $router */

$router->group('/settings', Groups::auth(), function (Router $r) {
    // ── หน้า Settings ทั่วไป (แท็บ default) ──
    $r->get('/', [SettingController::class, 'index']);

    // ── อัปเดตคำแปลภาษา (ต้องอยู่ก่อน /{tab} เพื่อความแน่นอน) ──
    $r->post('/translations/update', [SettingController::class, 'updateTranslation']);

    // ── แท็บเฉพาะ (general / login / security / social / translations / design_spec) ──
    $r->get('/{tab}', [SettingController::class, 'index']);

    // ── บันทึกการตั้งค่าของแท็บ ──
    $r->post('/{tab}', [SettingController::class, 'update']);
});
