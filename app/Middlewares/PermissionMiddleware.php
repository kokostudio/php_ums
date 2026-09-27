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
            throw new \Exception("You do not have permission to access this resource.", 403);
        }

        return true;
    }
}
