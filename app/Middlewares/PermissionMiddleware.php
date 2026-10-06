<?php

namespace App\Middlewares;

use App\Core\MiddlewareInterface;
use App\Core\RBAC;

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
        if (!RBAC::hasPermission($this->module, $this->action)) {
            // Check if it's an AJAX request
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                http_response_code(403);
                echo json_encode(['success' => false, 'message' => 'You do not have permission to access this resource.']);
                exit;
            }

            \App\Core\Session::setFlash('error', 'You do not have permission to access this resource.');
            header("Location: /dashboard");
            exit;
        }

        return true;
    }
}
