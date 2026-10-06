<?php

namespace App\Core;

class ErrorHandler
{
    private static string $logFile = __DIR__ . '/../../storage/logs/error.log';

    public static function register(): void
    {
        // Ensure log directory exists
        $logDir = dirname(self::$logFile);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }

        error_reporting(E_ALL);
        set_error_handler([self::class, 'handleError']);
        set_exception_handler([self::class, 'handleException']);
        register_shutdown_function([self::class, 'handleFatalError']);
    }

    public static function handleError(int $level, string $message, string $file = '', int $line = 0): bool
    {
        if (error_reporting() & $level) {
            throw new \ErrorException($message, 0, $level, $file, $line);
        }
        return false;
    }

    public static function handleException(\Throwable $exception): void
    {
        $code = $exception->getCode();
        if (!in_array($code, [403, 404, 429])) {
            $code = 500;
        }
        
        http_response_code($code);

        $message = "Uncaught Exception: '" . get_class($exception) . "'\n";
        $message .= "Message: '" . $exception->getMessage() . "'\n";
        $message .= "Stack trace:\n" . $exception->getTraceAsString() . "\n";
        $message .= "Thrown in '" . $exception->getFile() . "' on line " . $exception->getLine() . "\n";

        self::log($message);

        if (Config::get('APP_DEBUG') === 'true' && $code == 500) {
            echo "<h1>Fatal Error</h1>";
            echo "<pre>{$message}</pre>";
        } else {
            if ($code == 404) {
                echo "<h1>404 Not Found</h1>";
                echo "<p>The page you are looking for could not be found.</p>";
            } elseif ($code == 403) {
                echo "<h1>403 Forbidden</h1>";
                echo "<p>" . htmlspecialchars($exception->getMessage()) . "</p>";
                echo "<a href='/dashboard'>Return to Dashboard</a>";
            } elseif ($code == 429) {
                echo "<h1>429 Too Many Requests</h1>";
                echo "<p>" . htmlspecialchars($exception->getMessage()) . "</p>";
            } else {
                echo "<h1>500 Internal Server Error</h1>";
                echo "<p>An unexpected error occurred. Please try again later.</p>";
            }
        }
    }

    public static function handleFatalError(): void
    {
        $error = error_get_last();
        if ($error !== null && in_array($error['type'], [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_PARSE])) {
            self::handleError($error['type'], $error['message'], $error['file'], $error['line']);
        }
    }

    private static function log(string $message): void
    {
        $date = date('Y-m-d H:i:s');
        $logMessage = "[{$date}] {$message}\n-----------------------------------\n";
        error_log($logMessage, 3, self::$logFile);
    }
}
