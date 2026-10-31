<?php

/**
 * 01. Authentication Routes
 * ระบบล็อกอิน สมัครสมาชิก ยืนยันอีเมล รีเซ็ตรหัสผ่าน และ Social Login
 */

use App\Controllers\AuthController;
use App\Controllers\SocialAuthController;
use App\Controllers\LangController;
use App\Controllers\FaviconController;
use App\Middlewares\Groups;
use App\Core\Router;

/** @var Router $router */

// ── สลับภาษา & Dynamic Vector Favicon ──
$router->get('/lang/{locale}', [LangController::class, 'switch']);
$router->get('/favicon.svg', [FaviconController::class, 'render']);

// ── หน้า Login / Register (Guest only) ──
$router->group('', Groups::guest(), function (Router $r) {
    $r->get('/login', [AuthController::class, 'showLogin']);
    $r->post('/login', [AuthController::class, 'login'], Groups::throttle());

    $r->get('/register', [AuthController::class, 'showRegister']);
    $r->post('/register', [AuthController::class, 'register'], Groups::throttle());

    $r->get('/forgot-password', [AuthController::class, 'forgotPassword']);
    $r->post('/forgot-password', [AuthController::class, 'sendResetLink'], Groups::throttle());

    $r->get('/reset-password', [AuthController::class, 'resetPassword']);
    $r->post('/reset-password', [AuthController::class, 'updatePassword'], Groups::throttle());
});

// ── 2FA Login (ต้องยังไม่ Login แบบเต็มระบบ) ──
$router->group('/login', Groups::guest(), function (Router $r) {
    $r->get('/2fa', [AuthController::class, 'show2faForm']);
    $r->post('/2fa', [AuthController::class, 'verify2faLogin'], Groups::throttle());
});

// ── ยืนยันอีเมล ──
$router->get('/verify-email', [AuthController::class, 'verifyEmail']);
$router->post('/resend-verification', [AuthController::class, 'resendVerification'], Groups::throttle());

// ── Social Login (OAuth) ──
$router->group('/auth', [], function (Router $r) {
    $r->get('/google', [SocialAuthController::class, 'redirectToGoogle']);
    $r->get('/google/callback', [SocialAuthController::class, 'handleGoogleCallback']);

    $r->get('/line', [SocialAuthController::class, 'redirectToLine']);
    $r->get('/line/callback', [SocialAuthController::class, 'handleLineCallback']);

    $r->get('/facebook', [SocialAuthController::class, 'redirectToFacebook']);
    $r->get('/facebook/callback', [SocialAuthController::class, 'handleFacebookCallback']);

    $r->get('/github', [SocialAuthController::class, 'redirectToGithub']);
    $r->get('/github/callback', [SocialAuthController::class, 'handleGithubCallback']);
});

// ── Logout ──
$router->post('/logout', [AuthController::class, 'logout']);
