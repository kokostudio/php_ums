<?php

/**
 * 06. Profile Routes
 * โปรไฟล์ผู้ใช้ รหัสผ่าน 2FA เซสชัน และการจัดการบัญชีโซเชียล
 */

use App\Controllers\ProfileController;
use App\Middlewares\Groups;
use App\Core\Router;

/** @var Router $router */

$router->group('/profile', Groups::auth(), function (Router $r) {
    // ── ข้อมูลโปรไฟล์ ──
    $r->get('/', [ProfileController::class, 'index']);
    $r->post('/', [ProfileController::class, 'update']);

    // ── เปลี่ยนรหัสผ่าน ──
    $r->post('/password', [ProfileController::class, 'updatePassword']);

    // ── 2FA ──
    $r->post('/2fa/enable', [ProfileController::class, 'enable2fa']);
    $r->post('/2fa/disable', [ProfileController::class, 'disable2fa']);

    // ── เซสชันที่เปิดใช้งาน ──
    $r->get('/sessions', [ProfileController::class, 'sessions']);
    $r->post('/sessions/{id}/revoke', [ProfileController::class, 'revokeSession']);

    // ── GET Fallback (ตามกฎ Rule 8) ──
    $r->get('/sessions/{id}/revoke', function (): void {
        header('Location: /profile/sessions');
        exit;
    });

    // ── ยกเลิกการเชื่อมต่อบัญชีโซเชียล ──
    $r->post('/social/{provider}/disconnect', [ProfileController::class, 'disconnectSocial']);
});
