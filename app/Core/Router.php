<?php

namespace App\Core;

class Router
{
    private array $routes = [];

    public function get(string $path, array|callable $handler, array $middlewares = []): void
    {
        $this->addRoute('GET', $path, $handler, $middlewares);
    }

    public function post(string $path, array|callable $handler, array $middlewares = []): void
    {
        $this->addRoute('POST', $path, $handler, $middlewares);
    }

    private function addRoute(string $method, string $path, array|callable $handler, array $middlewares = []): void
    {
        // Simple regex conversion for dynamic parameters (e.g. {id})
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<\1>[a-zA-Z0-9_,-]+)', $path);
        $pattern = "#^{$pattern}$#";
        
        $this->routes[$method][$pattern] = [
            'handler' => $handler,
            'middlewares' => $middlewares
        ];
    }

    public function resolve(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        // Standardize URI (remove trailing slash except for root)
        if ($uri !== '/') {
            $uri = rtrim($uri, '/');
        }

        if (!isset($this->routes[$method])) {
            $this->abort(404);
        }

        foreach ($this->routes[$method] as $pattern => $route) {
            if (preg_match($pattern, $uri, $matches)) {
                
                // Execute middlewares
                foreach ($route['middlewares'] as $middlewareConfig) {
                    if (is_array($middlewareConfig)) {
                        $middlewareClass = $middlewareConfig[0];
                        $args = array_slice($middlewareConfig, 1);
                    } else {
                        $middlewareClass = $middlewareConfig;
                        $args = [];
                    }

                    if (class_exists($middlewareClass)) {
                        $middleware = new $middlewareClass(...$args);
                        if ($middleware instanceof MiddlewareInterface) {
                            if (!$middleware->handle()) {
                                return; // Middleware aborted the request
                            }
                        } else {
                            throw new \Exception("Middleware {$middlewareClass} must implement MiddlewareInterface");
                        }
                    }
                }

                // Extract named parameters
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                $handler = $route['handler'];

                if (is_callable($handler)) {
                    call_user_func_array($handler, $params);
                } elseif (is_array($handler)) {
                    [$controllerClass, $methodName] = $handler;
                    
                    if (class_exists($controllerClass)) {
                        $controller = new $controllerClass();
                        if (method_exists($controller, $methodName)) {
                            call_user_func_array([$controller, $methodName], $params);
                        } else {
                            throw new \Exception("Method {$methodName} not found in {$controllerClass}");
                        }
                    } else {
                        throw new \Exception("Controller class {$controllerClass} not found");
                    }
                }
                return;
            }
        }

        $this->abort(404);
    }

    public function abort(int $code = 404): void
    {
        http_response_code($code);
        // Can be replaced with a proper error view later
        echo "<h1>{$code} Not Found</h1>";
        exit;
    }
}
