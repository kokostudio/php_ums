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

// 5. Initialize Session (Globally)
\App\Core\Session::init();

// 6. Initialize Router
$router = new Router();
// Language Switch Route
$router->get('/lang/{locale}', [\App\Controllers\LangController::class, 'switch']);


// --- Define Routes Here ---
// Auth Routes
$router->get('/login', [\App\Controllers\AuthController::class, 'showLogin']);
$router->post('/login', [\App\Controllers\AuthController::class, 'login'], [\App\Middlewares\RateLimitMiddleware::class]);
$router->get('/login/2fa', [\App\Controllers\AuthController::class, 'show2faForm']);
$router->post('/login/2fa', [\App\Controllers\AuthController::class, 'verify2faLogin'], [\App\Middlewares\RateLimitMiddleware::class]);
$router->post('/logout', [\App\Controllers\AuthController::class, 'logout']);

// Registration & Email Verification
$router->get('/register', [\App\Controllers\AuthController::class, 'showRegister']);
$router->post('/register', [\App\Controllers\AuthController::class, 'register'], [\App\Middlewares\RateLimitMiddleware::class]);
$router->get('/verify-email', [\App\Controllers\AuthController::class, 'verifyEmail']);
$router->post('/resend-verification', [\App\Controllers\AuthController::class, 'resendVerification'], [\App\Middlewares\RateLimitMiddleware::class]);

// Social Auth Routes
$router->get('/auth/google', [\App\Controllers\SocialAuthController::class, 'redirectToGoogle']);
$router->get('/auth/google/callback', [\App\Controllers\SocialAuthController::class, 'handleGoogleCallback']);
$router->get('/auth/line', [\App\Controllers\SocialAuthController::class, 'redirectToLine']);
$router->get('/auth/line/callback', [\App\Controllers\SocialAuthController::class, 'handleLineCallback']);

// Forgot/Reset Password
$router->get('/forgot-password', [\App\Controllers\AuthController::class, 'forgotPassword']);
$router->post('/forgot-password', [\App\Controllers\AuthController::class, 'sendResetLink'], [\App\Middlewares\RateLimitMiddleware::class]);
$router->get('/reset-password', [\App\Controllers\AuthController::class, 'resetPassword']);
$router->post('/reset-password', [\App\Controllers\AuthController::class, 'updatePassword']);

// Protected Dashboard Route
$router->get('/dashboard', [\App\Controllers\DashboardController::class, 'index'], [\App\Middlewares\AuthMiddleware::class]);

// User Management Routes (Protected by Auth and Permission if fully strictly applied, but for now just Auth is fine as base)
$router->get('/users', [\App\Controllers\UserController::class, 'index'], [\App\Middlewares\AuthMiddleware::class, [\App\Middlewares\PermissionMiddleware::class, 'users', 'view']]);
$router->get('/users/data', [\App\Controllers\UserController::class, 'data'], [\App\Middlewares\AuthMiddleware::class, [\App\Middlewares\PermissionMiddleware::class, 'users', 'view']]);
$router->get('/users/create', [\App\Controllers\UserController::class, 'create'], [\App\Middlewares\AuthMiddleware::class, [\App\Middlewares\PermissionMiddleware::class, 'users', 'create']]);
$router->post('/users/create', [\App\Controllers\UserController::class, 'store'], [\App\Middlewares\AuthMiddleware::class, [\App\Middlewares\PermissionMiddleware::class, 'users', 'create']]);
$router->get('/users/{id}/edit', [\App\Controllers\UserController::class, 'edit'], [\App\Middlewares\AuthMiddleware::class, [\App\Middlewares\PermissionMiddleware::class, 'users', 'edit']]);
$router->post('/users/{id}/edit', [\App\Controllers\UserController::class, 'update'], [\App\Middlewares\AuthMiddleware::class, [\App\Middlewares\PermissionMiddleware::class, 'users', 'edit']]);
$router->post('/users/{id}/delete', [\App\Controllers\UserController::class, 'delete'], [\App\Middlewares\AuthMiddleware::class, [\App\Middlewares\PermissionMiddleware::class, 'users', 'delete']]);
$router->get('/users/{id}/delete', function() { header('Location: /users'); exit; }, [\App\Middlewares\AuthMiddleware::class]);

// Roles & Permissions Routes
$router->get('/roles', [\App\Controllers\RoleController::class, 'index'], [\App\Middlewares\AuthMiddleware::class, [\App\Middlewares\PermissionMiddleware::class, 'roles', 'view']]);
$router->get('/roles/create', [\App\Controllers\RoleController::class, 'create'], [\App\Middlewares\AuthMiddleware::class, [\App\Middlewares\PermissionMiddleware::class, 'roles', 'create']]);
$router->post('/roles/create', [\App\Controllers\RoleController::class, 'store'], [\App\Middlewares\AuthMiddleware::class, [\App\Middlewares\PermissionMiddleware::class, 'roles', 'create']]);
$router->get('/roles/{id}/edit', [\App\Controllers\RoleController::class, 'edit'], [\App\Middlewares\AuthMiddleware::class, [\App\Middlewares\PermissionMiddleware::class, 'roles', 'edit']]);
$router->post('/roles/{id}/edit', [\App\Controllers\RoleController::class, 'update'], [\App\Middlewares\AuthMiddleware::class, [\App\Middlewares\PermissionMiddleware::class, 'roles', 'edit']]);
$router->post('/roles/{id}/delete', [\App\Controllers\RoleController::class, 'delete'], [\App\Middlewares\AuthMiddleware::class, [\App\Middlewares\PermissionMiddleware::class, 'roles', 'delete']]);
$router->get('/roles/{id}/delete', function() { header('Location: /roles'); exit; }, [\App\Middlewares\AuthMiddleware::class]);

// Activity & Audit Logs Routes
$router->get('/logs/activity', [\App\Controllers\LogController::class, 'activity'], [\App\Middlewares\AuthMiddleware::class]);
$router->get('/logs/activity/data', [\App\Controllers\LogController::class, 'activityData'], [\App\Middlewares\AuthMiddleware::class]);
$router->get('/logs/audit', [\App\Controllers\LogController::class, 'audit'], [\App\Middlewares\AuthMiddleware::class]);
$router->get('/logs/audit/data', [\App\Controllers\LogController::class, 'auditData'], [\App\Middlewares\AuthMiddleware::class]);

// System Settings Routes
$router->get('/settings', [\App\Controllers\SettingController::class, 'index'], [\App\Middlewares\AuthMiddleware::class]);
$router->get('/settings/{tab}', [\App\Controllers\SettingController::class, 'index'], [\App\Middlewares\AuthMiddleware::class]);
$router->post('/settings/translations/update', [\App\Controllers\SettingController::class, 'updateTranslation'], [\App\Middlewares\AuthMiddleware::class]);
$router->post('/settings/{tab}', [\App\Controllers\SettingController::class, 'update'], [\App\Middlewares\AuthMiddleware::class]);

// Profile Routes
$router->get('/profile', [\App\Controllers\ProfileController::class, 'index'], [\App\Middlewares\AuthMiddleware::class]);
$router->post('/profile', [\App\Controllers\ProfileController::class, 'update'], [\App\Middlewares\AuthMiddleware::class]);
$router->post('/profile/password', [\App\Controllers\ProfileController::class, 'updatePassword'], [\App\Middlewares\AuthMiddleware::class]);
$router->post('/profile/2fa/enable', [\App\Controllers\ProfileController::class, 'enable2fa'], [\App\Middlewares\AuthMiddleware::class]);
$router->post('/profile/2fa/disable', [\App\Controllers\ProfileController::class, 'disable2fa'], [\App\Middlewares\AuthMiddleware::class]);
$router->get('/profile/sessions', [\App\Controllers\ProfileController::class, 'sessions'], [\App\Middlewares\AuthMiddleware::class]);
$router->post('/profile/sessions/{id}/revoke', [\App\Controllers\ProfileController::class, 'revokeSession'], [\App\Middlewares\AuthMiddleware::class]);
$router->post('/profile/social/{provider}/disconnect', [\App\Controllers\ProfileController::class, 'disconnectSocial'], [\App\Middlewares\AuthMiddleware::class]);

// --- API Routes (Stateless / JWT) ---
$router->post('/api/v1/login', [\App\Controllers\Api\AuthController::class, 'login']);
$router->get('/api/v1/me', [\App\Controllers\Api\AuthController::class, 'me'], [\App\Middlewares\JwtMiddleware::class]);

// --- Internal API Routes (Session-based) ---
$router->get('/api/notifications', [\App\Controllers\NotificationController::class, 'index'], [\App\Middlewares\AuthMiddleware::class]);
$router->post('/api/notifications/read', [\App\Controllers\NotificationController::class, 'markAsRead'], [\App\Middlewares\AuthMiddleware::class]);
$router->post('/api/notifications/read-all', [\App\Controllers\NotificationController::class, 'markAllAsRead'], [\App\Middlewares\AuthMiddleware::class]);
$router->get('/api/search', [\App\Controllers\SearchController::class, 'search'], [\App\Middlewares\AuthMiddleware::class]);

$router->get('/', function() {
    header('Location: /login');
    exit;
});

// 5. Dispatch Request
$router->resolve();
