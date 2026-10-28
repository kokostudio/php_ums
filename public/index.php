<?php

/**
 * Standard Core System (PHP 8.4+ Pure MVC)
 * Entry Point
 *
 * โครงสร้าง Route ถูกแยกออกเป็นไฟล์ย่อยตามโดเมนใน app/Routes/
 * และรองรับการ Auto-load Route ของ Module ส่วนขยายใน app/Modules/<Module>/routes.php
 */

// 1. Register Autoloader
require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Config;
use App\Core\ErrorHandler;
use App\Core\Router;
use App\Core\RouteRegistrar;

// 2. Register Error Handler
ErrorHandler::register();

// 3. Load Environment Variables
Config::load(__DIR__ . '/../.env');

// 4. Initialize Multi-Language System
\App\Core\Lang::init();

// 5. Initialize Session (Globally)
\App\Core\Session::init();

// 6. Initialize Router
$router = new Router();

// 7. Load Route Files
//    - app/Routes/*.php         (Core System)
//    - app/Modules/*/routes.php (Module ส่วนขยาย — Auto-load)
(new RouteRegistrar())->load($router);

// 8. Dispatch Request
$router->resolve();
