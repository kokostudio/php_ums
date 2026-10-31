<?php

/**
 * 05. System Settings Routes
 * ตั้งค่าระบบ (ทุกแท็บใช้โครงสร้าง Clean URL)
 * การเข้าถึง: ดูหน้า Settings ต้องมีสิทธิ์ settings/view · บันทึกค่าต้องมี settings/edit
 * (ปกป้องค่าความปลอดภัย เช่น OAuth Client Secret จากผู้ใช้ทั่วไป)
 */

use App\Controllers\SettingController;
use App\Middlewares\Groups;
use App\Core\Router;

/** @var Router $router */

$router->group('/settings', Groups::authPermission('settings', 'view'), function (Router $r) {
    // ── หน้า Settings ทั่วไป (แท็บ default) ──
    $r->get('/', [SettingController::class, 'index']);

    // ── อัปเดตคำแปลภาษา (ต้องอยู่ก่อน /{tab} เพื่อความแน่นอน) ──
    $r->post('/translations/update', [SettingController::class, 'updateTranslation'], Groups::permission('settings', 'edit'));

    // ── แท็บเฉพาะ (general / login / security / social / translations / design_spec) ──
    $r->get('/{tab}', [SettingController::class, 'index']);

    // ── บันทึกการตั้งค่าของแท็บ ──
    $r->post('/{tab}', [SettingController::class, 'update'], Groups::permission('settings', 'edit'));
});
