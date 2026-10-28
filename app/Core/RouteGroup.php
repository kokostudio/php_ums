<?php

namespace App\Core;

/**
 * RouteGroup — กลุ่มของ Route ที่ใช้ร่วมกัน
 *
 * สืบทอดจาก Router เพื่อให้ Callback ใน $router->group(...) สามารถ Type-hint ได้ทั้ง:
 *   - function (Router $r) { ... }
 *   - function (RouteGroup $r) { ... }
 *   - function ($r) { ... }
 *
 * รองรับ:
 *   - Prefix      : เติม Path นำหน้าทุก Route ในกลุ่ม
 *   - Middleware  : Middleware ที่ใช้ร่วมกันทั้งกลุ่ม (ต่อท้ายด้วย Middleware ราย Route)
 *   - Nesting     : กลุ่มซ้อนกันได้ (Group ภายใน Group)
 *
 * ตัวอย่าง:
 *   $router->group('/api/v1', [AuthMiddleware::class], function (Router $r) {
 *       $r->get('/users', [UserController::class, 'index']);
 *   });
 */
class RouteGroup extends Router
{
    public function __construct(
        private readonly Router $parentRouter,
        private readonly string $groupPrefix = '',
        private readonly array $groupMiddlewares = []
    ) {
    }

    /**
     * ลงทะเบียน POST Delete ควบคู่กับ GET Fallback Redirect อัตโนมัติในกลุ่ม (Rule #8)
     */
    public function deleteWithFallback(
        string $path,
        array|callable $handler,
        string $redirectUrl,
        array $middlewares = []
    ): void {
        $this->post($path, $handler, $middlewares);

        $this->get($path, function () use ($redirectUrl): void {
            header('Location: ' . $redirectUrl);
            exit;
        }, $middlewares);
    }

    /**
     * สร้างกลุ่มย่อย (Nested Group)
     * รองรับทั้งแบบ php_health array attributes และ shorthand
     */
    public function group(string|array $prefixOrAttributes, array|callable $middlewaresOrCallback = [], ?callable $callback = null): void
    {
        $prefix = '';
        $middlewares = [];
        $cb = null;

        if (is_array($prefixOrAttributes)) {
            $prefix = (string)($prefixOrAttributes['prefix'] ?? '');
            $middlewares = (array)($prefixOrAttributes['middlewares'] ?? []);
            $cb = is_callable($middlewaresOrCallback) ? $middlewaresOrCallback : $callback;
        } elseif (is_callable($middlewaresOrCallback)) {
            $prefix = (string)$prefixOrAttributes;
            $cb = $middlewaresOrCallback;
        } else {
            $prefix = (string)$prefixOrAttributes;
            $middlewares = is_array($middlewaresOrCallback) ? $middlewaresOrCallback : [];
            $cb = $callback;
        }

        if (!is_callable($cb)) {
            throw new \InvalidArgumentException('Route group requires a valid callback');
        }

        $this->parentRouter->group(
            $this->groupPrefix . $this->normalizePrefix($prefix),
            array_merge($this->groupMiddlewares, $middlewares),
            $cb
        );
    }

    public function get(string $path, array|callable $handler, array $middlewares = [], ?string $name = null): void
    {
        $this->parentRouter->get(
            $this->groupPrefix . $path,
            $handler,
            array_merge($this->groupMiddlewares, $middlewares),
            $name
        );
    }

    public function post(string $path, array|callable $handler, array $middlewares = [], ?string $name = null): void
    {
        $this->parentRouter->post(
            $this->groupPrefix . $path,
            $handler,
            array_merge($this->groupMiddlewares, $middlewares),
            $name
        );
    }

    public function put(string $path, array|callable $handler, array $middlewares = [], ?string $name = null): void
    {
        $this->parentRouter->put(
            $this->groupPrefix . $path,
            $handler,
            array_merge($this->groupMiddlewares, $middlewares),
            $name
        );
    }

    public function delete(string $path, array|callable $handler, array $middlewares = [], ?string $name = null): void
    {
        $this->parentRouter->delete(
            $this->groupPrefix . $path,
            $handler,
            array_merge($this->groupMiddlewares, $middlewares),
            $name
        );
    }

    public function patch(string $path, array|callable $handler, array $middlewares = [], ?string $name = null): void
    {
        $this->parentRouter->patch(
            $this->groupPrefix . $path,
            $handler,
            array_merge($this->groupMiddlewares, $middlewares),
            $name
        );
    }

    public function match(array $methods, string $path, array|callable $handler, array $middlewares = [], ?string $name = null): void
    {
        foreach ($methods as $method) {
            $this->parentRouter->addRoute(
                strtoupper($method),
                $this->groupPrefix . $path,
                $handler,
                array_merge($this->groupMiddlewares, $middlewares),
                $name
            );
        }
    }

    public function addRoute(string $method, string $path, array|callable $handler, array $middlewares = [], ?string $name = null): void
    {
        $this->parentRouter->addRoute(
            $method,
            $this->groupPrefix . $path,
            $handler,
            array_merge($this->groupMiddlewares, $middlewares),
            $name
        );
    }

    public function fallback(callable $handler): void
    {
        $this->parentRouter->fallback($handler);
    }

    public function routes(): array
    {
        return $this->parentRouter->routes();
    }

    public function routeCount(): int
    {
        return $this->parentRouter->routeCount();
    }

    public function resolve(): void
    {
        $this->parentRouter->resolve();
    }
}
