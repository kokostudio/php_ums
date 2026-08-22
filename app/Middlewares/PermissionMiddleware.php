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
            http_response_code(403);
            echo "<h1>403 Forbidden</h1><p>You do not have permission to access this resource.</p>";
            exit;
        }

        return true;
    }
}
