<?php

namespace App\Core;

/**
 * Route — คำอธิบาย Route เดียว (Single Route Definition)
 *
 * เก็บข้อมูลทั้งหมดของ Route ไว้ใน Object เดียว เพื่อให้ Router จัดการได้ง่าย
 * และสามารถนำไปแสดงผลเป็นตาราง (Route Listing) ได้ในอนาคต
 */
class Route
{
    /**
     * @param array|callable $handler  Handler ของ Route ([Controller::class, 'method'] หรือ Closure)
     */
    public function __construct(
        public readonly string $method,
        public readonly string $path,
        public readonly mixed $handler,
        public readonly array $middlewares = [],
        public readonly ?string $name = null,
        public readonly array $defaults = []
    ) {
    }

    /**
     * ดึงชื่อ Controller จาก Handler (สำหรับ Route Listing)
     */
    public function handlerName(): string
    {
        if (is_array($this->handler)) {
            [$class, $method] = $this->handler;
            return class_basename($class) . '@' . $method;
        }

        if (is_object($this->handler) && !$this->handler instanceof \Closure) {
            return class_basename($this->handler) . '@__invoke';
        }

        return 'Closure';
    }

    /**
     * แปลง Path เป็น Regex Pattern สำหรับการ Match
     * รองรับ:
     *   - {param}      → Named parameter (a-zA-Z0-9_,-)
     *   - {param?}     → Optional parameter
     *   - {param:\d+}  → Custom constraint (ต้องอยู่ในวงเล็บ)
     */
    public static function compilePath(string $path): string
    {
        $pattern = preg_replace_callback(
            '/\{([a-zA-Z_][a-zA-Z0-9_]*)(?::([^}]+))?(\?)?\}/',
            static function (array $m): string {
                $name     = $m[1];
                $custom   = $m[2] ?? null;
                $optional = isset($m[3]) && $m[3] === '?';

                if ($custom !== null && $custom !== '') {
                    $inner = $custom;
                } else {
                    $inner = '[a-zA-Z0-9_,-]+';
                }

                // Optional parameter ต้องทำให้ slash ก่อนหน้ากลายเป็น optional ด้วย
                return $optional
                    ? '(?:/(?P<' . $name . '>' . $inner . '))?'
                    : '(?P<' . $name . '>' . $inner . ')';
            },
            $path
        );

        return '#^' . $pattern . '$#';
    }
}
