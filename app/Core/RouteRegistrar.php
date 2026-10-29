<?php

namespace App\Core;

/**
 * RouteRegistrar — โหลดไฟล์ Route ทั้งหมดของระบบ
 *
 * แหล่งที่มาของ Route:
 *   1. app/Routes/*.php          — ไฟล์ Route หลักของ Core System (โหลดตามลำดับตัวอักษร)
 *   2. app/Modules/<Module>/routes.php หรือ Routes.php — ไฟล์ Route ของ Module ส่วนขยาย (Auto-load)
 *
 * ไฟล์ Route แต่ละไฟล์จะได้รับตัวแปร $router (Router) เป็น Scope ให้ใช้
 */
class RouteRegistrar
{
    private string $routesPath;
    private string $modulesPath;

    public function __construct(?string $rootDir = null, ?string $routesPath = null, ?string $modulesPath = null)
    {
        $base = $rootDir ?: dirname(__DIR__, 2);
        $this->routesPath  = $routesPath  ?? ($base . '/app/Routes');
        $this->modulesPath = $modulesPath ?? ($base . '/app/Modules');
    }

    /**
     * โหลดไฟล์ Route ทั้งหมดเข้าสู่ Router
     */
    public function load(Router $router, ?string $rootDir = null): void
    {
        if ($rootDir !== null) {
            $this->routesPath  = $rootDir . '/app/Routes';
            $this->modulesPath = $rootDir . '/app/Modules';
        }

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
        sort($files); // เรียงตามชื่อไฟล์ (00_xxx.php, 01_xxx.php)

        return $files;
    }

    /**
     * ไฟล์ Route ของ Module ส่วนขยาย (app/Modules/<Module>/routes.php หรือ Routes.php)
     *
     * @return array<int, string>
     */
    private function moduleRouteFiles(): array
    {
        if (!is_dir($this->modulesPath)) {
            return [];
        }

        $files = [];

        // ใช้ scandir เพื่อความปลอดภัยแม้บน hosting ที่ปิด glob
        $entries = @scandir($this->modulesPath);
        if (is_array($entries)) {
            sort($entries);
            foreach ($entries as $entry) {
                if ($entry === '.' || $entry === '..') {
                    continue;
                }
                $moduleDir = $this->modulesPath . '/' . $entry;
                if (!is_dir($moduleDir)) {
                    continue;
                }

                // ตรวจสอบสถานะการเปิดใช้งานโมดูล (หากมีเมธอดตรวจสอบใน RBAC)
                $checkMethod = 'is' . $entry . 'ModuleEnabled';
                if (class_exists(\App\Core\RBAC::class) && method_exists(\App\Core\RBAC::class, $checkMethod)) {
                    if (!\App\Core\RBAC::$checkMethod()) {
                        continue;
                    }
                }

                if (is_file($moduleDir . '/routes.php')) {
                    $files[] = $moduleDir . '/routes.php';
                } elseif (is_file($moduleDir . '/Routes.php')) {
                    $files[] = $moduleDir . '/Routes.php';
                }
            }
        }

        return $files;
    }

    /**
     * require ไฟล์ Route โดยส่ง $router เข้าไป
     */
    private function requireRouteFile(string $file, Router $router): void
    {
        require $file;
    }
}
