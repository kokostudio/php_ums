<?php

namespace App\Middlewares;

use App\Core\MiddlewareInterface;
use App\Core\RBAC;
use App\Core\Session;

class PermissionMiddleware implements MiddlewareInterface
{
    private string $module;
    private string $action;

    public function __construct(string $module, string $action)
    {
        $this->module = $module;
        $this->action = $action;
    }

    public function handle(): bool
    {
        if (RBAC::hasPermission($this->module, $this->action)) {
            return true;
        }

        $message = __('common.no_permission');

        // AJAX: ตอบ JSON ตาม contract ของระบบ (Dual-Response Pattern)
        $isAjax = \App\Core\Request::wantsJson();

        if ($isAjax) {
            http_response_code(403);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'success' => false,
                'type'    => 'error',
                'message' => $message,
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        Session::setFlash('error', $message);
        \App\Core\Redirect::to('/dashboard');
    }
}
