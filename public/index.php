<?php

/**
 * Standard Core System (PHP 8.4+ Pure MVC)
 * Entry Point
 */

// 1. Register Autoloader
require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Config;
use App\Core\ErrorHandler;
use App\Core\Router;

// 2. Register Error Handler
ErrorHandler::register();

// 3. Load Environment Variables
Config::load(__DIR__ . '/../.env');

// 4. Initialize Multi-Language System
\App\Core\Lang::init();

// 5. Initialize Router
$router = new Router();

// Language Switch Route
$router->get('/lang/{locale}', [\App\Controllers\LangController::class, 'switch']);


// --- Define Routes Here ---
// Auth Routes
$router->get('/login', [\App\Controllers\AuthController::class, 'showLogin']);
$router->post('/login', [\App\Controllers\AuthController::class, 'login']);
$router->post('/logout', [\App\Controllers\AuthController::class, 'logout']);

// Forgot/Reset Password
$router->get('/forgot-password', [\App\Controllers\AuthController::class, 'forgotPassword']);
$router->post('/forgot-password', [\App\Controllers\AuthController::class, 'sendResetLink']);
$router->get('/reset-password', [\App\Controllers\AuthController::class, 'resetPassword']);
$router->post('/reset-password', [\App\Controllers\AuthController::class, 'updatePassword']);

// Protected Dashboard Route
$router->get('/dashboard', [\App\Controllers\DashboardController::class, 'index'], [\App\Middlewares\AuthMiddleware::class]);

// User Management Routes (Protected by Auth and Permission if fully strictly applied, but for now just Auth is fine as base)
$router->get('/users', [\App\Controllers\UserController::class, 'index'], [\App\Middlewares\AuthMiddleware::class]);
$router->get('/users/data', [\App\Controllers\UserController::class, 'data'], [\App\Middlewares\AuthMiddleware::class]);
$router->get('/users/create', [\App\Controllers\UserController::class, 'create'], [\App\Middlewares\AuthMiddleware::class]);
$router->post('/users/create', [\App\Controllers\UserController::class, 'store'], [\App\Middlewares\AuthMiddleware::class]);
$router->get('/users/{id}/edit', [\App\Controllers\UserController::class, 'edit'], [\App\Middlewares\AuthMiddleware::class]);
$router->post('/users/{id}/edit', [\App\Controllers\UserController::class, 'update'], [\App\Middlewares\AuthMiddleware::class]);
$router->post('/users/{id}/delete', [\App\Controllers\UserController::class, 'delete'], [\App\Middlewares\AuthMiddleware::class]);

// Roles & Permissions Routes
$router->get('/roles', [\App\Controllers\RoleController::class, 'index'], [\App\Middlewares\AuthMiddleware::class]);
$router->get('/roles/create', [\App\Controllers\RoleController::class, 'create'], [\App\Middlewares\AuthMiddleware::class]);
$router->post('/roles/create', [\App\Controllers\RoleController::class, 'store'], [\App\Middlewares\AuthMiddleware::class]);
$router->get('/roles/{id}/edit', [\App\Controllers\RoleController::class, 'edit'], [\App\Middlewares\AuthMiddleware::class]);
$router->post('/roles/{id}/edit', [\App\Controllers\RoleController::class, 'update'], [\App\Middlewares\AuthMiddleware::class]);
$router->post('/roles/{id}/delete', [\App\Controllers\RoleController::class, 'delete'], [\App\Middlewares\AuthMiddleware::class]);

// Activity & Audit Logs Routes
$router->get('/logs/activity', [\App\Controllers\LogController::class, 'activity'], [\App\Middlewares\AuthMiddleware::class]);
$router->get('/logs/activity/data', [\App\Controllers\LogController::class, 'activityData'], [\App\Middlewares\AuthMiddleware::class]);
$router->get('/logs/audit', [\App\Controllers\LogController::class, 'audit'], [\App\Middlewares\AuthMiddleware::class]);
$router->get('/logs/audit/data', [\App\Controllers\LogController::class, 'auditData'], [\App\Middlewares\AuthMiddleware::class]);

// System Settings Routes
$router->get('/settings', [\App\Controllers\SettingController::class, 'index'], [\App\Middlewares\AuthMiddleware::class]);
$router->get('/settings/{tab}', [\App\Controllers\SettingController::class, 'index'], [\App\Middlewares\AuthMiddleware::class]);
$router->post('/settings/{tab}', [\App\Controllers\SettingController::class, 'update'], [\App\Middlewares\AuthMiddleware::class]);

// Profile Routes
$router->get('/profile', [\App\Controllers\ProfileController::class, 'index'], [\App\Middlewares\AuthMiddleware::class]);
$router->post('/profile', [\App\Controllers\ProfileController::class, 'update'], [\App\Middlewares\AuthMiddleware::class]);
$router->post('/profile/password', [\App\Controllers\ProfileController::class, 'updatePassword'], [\App\Middlewares\AuthMiddleware::class]);

$router->get('/', function() {
    header('Location: /login');
    exit;
});

// 5. Dispatch Request
$router->resolve();
