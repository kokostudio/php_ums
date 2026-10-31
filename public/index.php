<?php

/**
 * Standard Core System (PHP 8.4+ Pure MVC)
 * Entry Point
 *
 * โครงสร้าง Route ถูกแยกออกเป็นไฟล์ย่อยตามโดเมนใน app/Routes/
 * และรองรับการ Auto-load Route ของ Module ส่วนขยายใน app/Modules/<Module>/routes.php
 */

// Production Diagnostic & Debug Trigger (?debug=1)
// เปิด display_errors ได้เฉพาะเมื่อ APP_DEBUG=true ใน .env เท่านั้น (กัน stack trace รั่วบน production)
if (isset($_GET['debug']) && $_GET['debug'] === '1' && getenv('APP_DEBUG') === 'true') {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
}

// 1. Auto-detect Root Directory & Register Autoloader
$rootDir = null;
if (is_dir(__DIR__ . '/vendor')) {
    $rootDir = __DIR__;
} elseif (is_dir(__DIR__ . '/../php_ums/vendor')) {
    $rootDir = realpath(__DIR__ . '/../php_ums');
} elseif (is_dir(dirname(__DIR__) . '/php_ums/vendor')) {
    $rootDir = realpath(dirname(__DIR__) . '/php_ums');
} elseif (is_dir(dirname(__DIR__) . '/vendor')) {
    $rootDir = dirname(__DIR__);
} else {
    $rootDir = dirname(__DIR__);
}

if (function_exists('opcache_invalidate')) {
    @opcache_invalidate($rootDir . '/app/Core/Router.php', true);
    @opcache_invalidate($rootDir . '/app/Core/RouteRegistrar.php', true);
}
if (isset($_GET['clear_cache']) && function_exists('opcache_reset')) {
    @opcache_reset();
}

require_once $rootDir . '/vendor/autoload.php';

use App\Core\Config;
use App\Core\ErrorHandler;
use App\Core\Router;
use App\Core\RouteRegistrar;

// 2. Register Error Handler
ErrorHandler::register();

// 3. Load Environment Variables
Config::load($rootDir . '/.env');

// 4. Initialize Multi-Language System
\App\Core\Lang::init();

// 5. Initialize Session (Globally)
\App\Core\Session::init();

// 5.1 ส่ง Security Headers กลาง (ครอบทุก response รวม 404/error)
\App\Core\SecurityHeaders::send();

// 6. Initialize Router & Load Modular Routes
$router = new Router();
if (class_exists(RouteRegistrar::class)) {
    (new RouteRegistrar($rootDir))->load($router, $rootDir);
} elseif (method_exists($router, 'loadRoutes')) {
    $router->loadRoutes($rootDir);
}

// 7. Dispatch Request
$router->resolve();
