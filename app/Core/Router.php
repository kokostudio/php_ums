<?php

namespace App\Core;

/**
 * Router — ระบบ Routing (Route Group / Prefix / Middleware Group)
 *
 * ความสามารถหลัก:
 *   1. Route Group      : $router->group('/prefix', [Middleware::class], function ($r) { ... })
 *   2. Middleware Group : ใช้ชื่อกลุ่มจาก \App\Middlewares\Groups เพื่อไม่ต้องเขียนซ้ำ
 *   3. Named Parameter  : {id}, {id:\d+}, {id?} — ส่งเข้า Controller แบบ Named Argument
 *   4. Route Listing    : $router->routes() สำหรับแสดงตาราง Route ทั้งหมด
 *   5. Fallback Handler : จัดการ 404 แบบกำหนดเองได้
 *
 * หมายเหตุสำคัญ (PHP 8+):
 *   ถ้า Handler เป็น Closure ที่ไม่รับ Parameter แต่ URL มี Dynamic Segment
 *   ระบบจะ "กรอง Parameter ที่ไม่ได้ใช้" ออกให้อัตโนมัติ ป้องกัน error
 *   "Unknown named parameter $id"
 */
class Router
{
    /** @var array<string, array<int, Route>> */
    private array $routes = [];

    /** @var callable|null */
    private $notFoundHandler = null;

    // ─────────────────────────────────────────────────────────────
    // Registration
    // ─────────────────────────────────────────────────────────────

    public function get(string $path, array|callable $handler, array $middlewares = [], ?string $name = null): void
    {
        $this->addRoute('GET', $path, $handler, $middlewares, $name);
    }

    public function post(string $path, array|callable $handler, array $middlewares = [], ?string $name = null): void
    {
        $this->addRoute('POST', $path, $handler, $middlewares, $name);
    }

    public function put(string $path, array|callable $handler, array $middlewares = [], ?string $name = null): void
    {
        $this->addRoute('PUT', $path, $handler, $middlewares, $name);
    }

    public function patch(string $path, array|callable $handler, array $middlewares = [], ?string $name = null): void
    {
        $this->addRoute('PATCH', $path, $handler, $middlewares, $name);
    }

    public function delete(string $path, array|callable $handler, array $middlewares = [], ?string $name = null): void
    {
        $this->addRoute('DELETE', $path, $handler, $middlewares, $name);
    }

    /**
     * ลงทะเบียน Route สำหรับหลาย HTTP Method พร้อมกัน
     */
    public function match(array $methods, string $path, array|callable $handler, array $middlewares = [], ?string $name = null): void
    {
        foreach ($methods as $method) {
            $this->addRoute(strtoupper($method), $path, $handler, $middlewares, $name);
        }
    }

    /**
     * สร้าง Route Group (Prefix + Middleware ร่วมกัน)
     */
    public function group(string $prefix, array $middlewares, callable $callback): void
    {
        $group = new RouteGroup($this, $this->normalizePrefix($prefix), $middlewares);
        $callback($group);
    }

    /**
     * กำหนด Handler สำหรับกรณีไม่พบ Route (404)
     */
    public function fallback(callable $handler): void
    {
        $this->notFoundHandler = $handler;
    }

    public function addRoute(string $method, string $path, array|callable $handler, array $middlewares = [], ?string $name = null): void
    {
        $method = strtoupper($method);
        $path   = $this->normalizePath($path);

        $this->routes[$method][] = new Route(
            $method,
            $path,
            $handler,
            array_values($middlewares),
            $name
        );
    }

    // ─────────────────────────────────────────────────────────────
    // Dispatch
    // ─────────────────────────────────────────────────────────────

    public function resolve(): void
    {
        $method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
        $uri    = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

        $uri = $this->normalizeUri($uri);

        $routes = $this->routes[$method] ?? [];

        foreach ($routes as $route) {
            $pattern = Route::compilePath($route->path);

            if (!preg_match($pattern, $uri, $matches)) {
                continue;
            }

            // ดึง Named Parameter ออกมา
            $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

            // รวม Default Parameter (สำหรับ Optional Segment)
            if (!empty($route->defaults)) {
                $params = array_merge($route->defaults, $params);
            }

            // ถ้าไม่ผ่าน Middleware ให้หยุดทันที
            if (!$this->runMiddlewares($route->middlewares)) {
                return;
            }

            $this->runHandler($route->handler, $params);

            return;
        }

        // ── ไม่พบ Route ──────────────────────────────────────────
        $this->handleNotFound();
    }

    /**
     * รัน Middleware ทั้งหมด — คืน false ถ้ามี Middleware ที่ abort request
     */
    private function runMiddlewares(array $middlewares): bool
    {
        foreach ($middlewares as $middlewareConfig) {
            if (is_array($middlewareConfig)) {
                $middlewareClass = $middlewareConfig[0];
                $args            = array_slice($middlewareConfig, 1);
            } else {
                $middlewareClass = $middlewareConfig;
                $args            = [];
            }

            if (!class_exists($middlewareClass)) {
                throw new \Exception("Middleware {$middlewareClass} not found");
            }

            $middleware = new $middlewareClass(...$args);

            if (!$middleware instanceof MiddlewareInterface) {
                throw new \Exception("Middleware {$middlewareClass} must implement MiddlewareInterface");
            }

            if (!$middleware->handle()) {
                return false; // Middleware aborted the request
            }
        }

        return true;
    }

    /**
     * เรียก Handler พร้อมกรอง Parameter ที่ Handler ไม่ได้รับ (ป้องกัน PHP 8 Named Argument Error)
     */
    private function runHandler(array|callable $handler, array $params): void
    {
        if (is_array($handler)) {
            [$controllerClass, $methodName] = $handler;

            if (!class_exists($controllerClass)) {
                throw new \Exception("Controller class {$controllerClass} not found");
            }

            $controller = new $controllerClass();

            if (!method_exists($controller, $methodName)) {
                throw new \Exception("Method {$methodName} not found in {$controllerClass}");
            }

            $reflected = new \ReflectionMethod($controller, $methodName);
            $args      = $this->filterParams($reflected, $params);

            $reflected->invokeArgs($controller, $args);

            return;
        }

        if ($handler instanceof \Closure) {
            $reflected = new \ReflectionFunction($handler);
            $args      = $this->filterParams($reflected, $params);

            $reflected->invokeArgs($args);

            return;
        }

        if (is_object($handler) && method_exists($handler, '__invoke')) {
            $reflected = new \ReflectionMethod($handler, '__invoke');
            $args      = $this->filterParams($reflected, $params);

            $reflected->invokeArgs($handler, $args);

            return;
        }

        throw new \Exception('Invalid route handler');
    }

    /**
     * กรองเฉพาะ Parameter ที่ Handler ประกาศรับจริง
     * ป้องกัน "Unknown named parameter $xxx" ใน PHP 8+
     */
    private function filterParams(\ReflectionFunctionAbstract $reflected, array $params): array
    {
        $accepted = [];

        foreach ($reflected->getParameters() as $parameter) {
            $name = $parameter->getName();

            if (array_key_exists($name, $params)) {
                $accepted[$name] = $params[$name];
            }
        }

        return $accepted;
    }

    private function handleNotFound(): void
    {
        if ($this->notFoundHandler !== null) {
            ($this->notFoundHandler)();

            return;
        }

        $this->abort(404);
    }

    public function abort(int $code = 404): void
    {
        throw new \Exception("Route Not Found", $code);
    }

    // ─────────────────────────────────────────────────────────────
    // Introspection
    // ─────────────────────────────────────────────────────────────

    /**
     * รายการ Route ทั้งหมด (สำหรับ Route Listing / Debug)
     *
     * @return array<int, array{method: string, path: string, handler: string, middlewares: array}>
     */
    public function routes(): array
    {
        $list = [];

        foreach ($this->routes as $method => $routes) {
            foreach ($routes as $route) {
                $list[] = [
                    'method'      => $route->method,
                    'path'        => $route->path,
                    'handler'     => $route->handlerName(),
                    'middlewares' => $route->middlewares,
                    'name'        => $route->name,
                ];
            }
        }

        return $list;
    }

    public function routeCount(): int
    {
        return array_sum(array_map('count', $this->routes));
    }

    // ─────────────────────────────────────────────────────────────
    // Path Helpers
    // ─────────────────────────────────────────────────────────────

    /**
     * Normalize Path: ตัด Trailing Slash (ยกเว้น Root) และตัด Slash ซ้ำ
     */
    protected function normalizePath(string $path): string
    {
        $path = '/' . trim($path, '/');

        return $path === '/' ? '/' : rtrim($path, '/');
    }

    protected function normalizePrefix(string $prefix): string
    {
        $prefix = trim($prefix, '/');

        return $prefix === '' ? '' : '/' . $prefix;
    }

    /**
     * Normalize URI ของ Request: ตัด Trailing Slash (ยกเว้น Root)
     */
    protected function normalizeUri(string $uri): string
    {
        if ($uri === '/' || $uri === '') {
            return '/';
        }

        return rtrim($uri, '/');
    }
}
