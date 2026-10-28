<?php

namespace App\Core;

/**
 * RouteRegistrar — โหลดไฟล์ Route ทั้งหมดของระบบ
 *
 * แหล่งที่มาของ Route:
 *   1. app/Routes/*.php          — ไฟล์ Route หลักของ Core System
 *   2. app/Modules/<Module>/routes.php — ไฟล์ Route ของ Module ส่วนขยาย (Auto-load)
 *
 * ไฟล์ Route แต่ละไฟล์จะได้รับตัวแปร $router (Router) เป็น Scope ให้ใช้
 *
 * ตัวอย่างไฟล์ Route:
 *   use App\Controllers\UserController;
 *   use App\Middlewares\Groups;
 *
 *   $router->group('/users', Groups::authPermission('users', 'view'), function ($r) {
 *       $r->get('/', [UserController::class, 'index']);
 *   });
 */
class RouteRegistrar
{
    private string $routesPath;
    private string $modulesPath;

    public function __construct(?string $routesPath = null, ?string $modulesPath = null)
    {
        $this->routesPath  = $routesPath  ?? dirname(__DIR__) . '/Routes';
        $this->modulesPath = $modulesPath ?? dirname(__DIR__) . '/Modules';
    }

    /**
     * โหลดไฟล์ Route ทั้งหมดเข้าสู่ Router
     */
    public function load(Router $router): void
    {
        foreach ($this->coreRouteFiles() as $file) {
            $this->requireRouteFile($file, $router);
        }

        foreach ($this->moduleRouteFiles() as $file) {
            $this->requireRouteFile($file, $router);
        }
    }

    /**
     * ไฟล์ Route ของ Core System (เรียงตามลำดับความสำคัญ)
     *
     * @return array<int, string>
     */
    private function coreRouteFiles(): array
    {
        if (!is_dir($this->routesPath)) {
            return [];
        }

        $files = glob($this->routesPath . '/*.php') ?: [];
        sort($files); // เรียงตามชื่อไฟล์ (01_xxx.php, 02_xxx.php) เพื่อความชัดเจน

        return $files;
    }

    /**
     * ไฟล์ Route ของ Module ส่วนขยาย (app/Modules/<Module>/routes.php)
     *
     * @return array<int, string>
     */
    private function moduleRouteFiles(): array
    {
        if (!is_dir($this->modulesPath)) {
            return [];
        }

        $files = [];

        foreach (glob($this->modulesPath . '/*', GLOB_ONLYDIR) ?: [] as $moduleDir) {
            $routeFile = $moduleDir . '/routes.php';

            if (is_file($routeFile)) {
                $files[] = $routeFile;
            }
        }

        sort($files);

        return $files;
    }

    /**
     * require ไฟล์ Route โดยส่ง $router และ $groups เข้าไป
     */
    private function requireRouteFile(string $file, Router $router): void
    {
        require $file;
    }
}
