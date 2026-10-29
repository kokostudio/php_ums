<?php

/**
 * Web Installer Routes (Production Setup & Upgrade Wizard)
 *
 * @var \App\Core\Router $router
 */

use App\Controllers\InstallController;
use App\Core\Router;

$router->group(['prefix' => '/install'], function (Router $r): void {
    $r->get('/', [InstallController::class, 'index']);
    $r->post('/test-db', [InstallController::class, 'testDb']);
    $r->post('/process', [InstallController::class, 'process']);
    $r->get('/complete', [InstallController::class, 'complete']);
    $r->post('/upgrade', [InstallController::class, 'upgrade']);
});
