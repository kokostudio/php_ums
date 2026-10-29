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