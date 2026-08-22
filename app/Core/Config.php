<?php

namespace App\Core;

class Config
{
    private static array $env = [];

    public static function load(string $path): void
    {
        if (!file_exists($path)) {
            throw new \Exception("Environment file (.env) not found at: {$path}");
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            // Skip comments
            if (str_starts_with(trim($line), '#')) {
                continue;
            }

            // Parse key-value
            if (strpos($line, '=') !== false) {
                list($key, $value) = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);
                
                // Remove quotes if they exist
                $value = trim($value, '"\'');

                self::$env[$key] = $value;
                $_ENV[$key] = $value;
                putenv("{$key}={$value}");
            }
        }
    }

    public static function get(string $key, $default = null)
    {
        return self::$env[$key] ?? $_ENV[$key] ?? getenv($key) ?: $default;
    }
}
