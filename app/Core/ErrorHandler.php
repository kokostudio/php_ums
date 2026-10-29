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
            @mkdir($logDir, 0755, true);
        }

        if (isset($_GET['debug']) && $_GET['debug'] === '1') {
            ini_set('display_errors', '1');
            ini_set('display_startup_errors', '1');
        }

        error_reporting(E_ALL);
        set_error_handler([self::class, 'handleError']);
        set_exception_handler([self::class, 'handleException']);
        register_shutdown_function([self::class, 'handleFatalError']);
    }

    public static function handleError(int $level, string $message, string $file = '', int $line = 0): bool
    {
        // Ignore errors suppressed with @ (in PHP 8, error_reporting() returns 0 when @ is used)
        if (!(error_reporting() & $level)) {
            return false;
        }

        // For non-fatal warnings, notices, and deprecations: log them but do NOT crash the page
        if (in_array($level, [E_WARNING, E_NOTICE, E_USER_WARNING, E_USER_NOTICE, E_DEPRECATED, E_USER_DEPRECATED])) {
            self::log("Non-fatal [{$level}]: {$message} in {$file} on line {$line}");
            return true;
        }

        throw new \ErrorException($message, 0, $level, $file, $line);
    }

    public static function handleException(\Throwable $exception): void
    {
        $code = (int)$exception->getCode();
        if (!in_array($code, [403, 404, 429])) {
            $code = 500;
        }
        
        http_response_code($code);

        $message = "Uncaught Exception: '" . get_class($exception) . "'\n";
        $message .= "Message: '" . $exception->getMessage() . "'\n";
        $message .= "Stack trace:\n" . $exception->getTraceAsString() . "\n";
        $message .= "Thrown in '" . $exception->getFile() . "' on line " . $exception->getLine() . "\n";

        self::log($message);

        // Auto-flush PHP OPcache on 500 errors so newly uploaded files take effect immediately
        if ($code === 500 && function_exists('opcache_reset')) {
            @opcache_reset();
        }

        $isDebug = Config::get('APP_DEBUG') === 'true' || Config::get('APP_DEBUG') === true || (isset($_GET['debug']) && $_GET['debug'] === '1');

        if ($isDebug && $code == 500) {
            echo "<h1>Fatal Error</h1>";
            echo "<pre style='background:#1e293b;color:#f8fafc;padding:1.5rem;border-radius:0.75rem;overflow:auto;font-size:13px;line-height:1.5;'>" . htmlspecialchars($message) . "</pre>";
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
                $shortFile = basename($exception->getFile()) . ':' . $exception->getLine();
                $errMsg = htmlspecialchars($exception->getMessage());
                echo "<!DOCTYPE html><html lang='th'><head><meta charset='UTF-8'><meta name='viewport' content='width=device-width,initial-scale=1.0'><title>System Notice</title></head>";
                echo "<body style='font-family:system-ui,-apple-system,sans-serif;background:#f8fafc;color:#1e293b;display:flex;align-items:center;justify-content:center;min-height:95vh;margin:0;padding:1rem;'>";
                echo "<div style='max-width:600px;width:100%;background:#ffffff;border:1px solid #e2e8f0;border-radius:1rem;padding:2rem;box-shadow:0 10px 25px -5px rgba(0,0,0,0.05);'>";
                echo "<h2 style='margin-top:0;color:#dc2626;font-size:1.25rem;'>ระบบพบข้อผิดพลาดระหว่างประมวลผล (500)</h2>";
                echo "<p style='color:#475569;font-size:0.9rem;line-height:1.6;'>ระบบได้ทำการล้างแคช (OPcache) ให้อัตโนมัติแล้ว กรุณากดรีเฟรชหน้าเว็บอีกครั้ง หากยังพบปัญหาสามารถดูสาเหตุด้านล่างได้ทันที:</p>";
                echo "<div style='background:#fef2f2;border:1px solid #fecaca;color:#991b1b;padding:0.875rem 1rem;border-radius:0.5rem;font-family:monospace;font-size:0.82rem;word-break:break-word;margin:1rem 0;'>";
                echo "<strong>[" . htmlspecialchars($shortFile) . "]</strong> " . $errMsg;
                echo "</div>";
                echo "<div style='display:flex;gap:0.75rem;margin-top:1.25rem;'>";
                echo "<button onclick='location.reload()' style='background:#059669;color:#fff;border:none;padding:0.6rem 1.2rem;border-radius:0.5rem;cursor:pointer;font-weight:600;'>รีเฟรชหน้าเว็บ (Reload)</button>";
                echo "<a href='/dashboard' style='background:#f1f5f9;color:#334155;text-decoration:none;padding:0.6rem 1.2rem;border-radius:0.5rem;font-weight:600;'>กลับสู่หน้าแดชบอร์ด</a>";
                echo "</div></div></body></html>";
            }
        }
    }

    public static function handleFatalError(): void
    {
        $error = error_get_last();
        if ($error !== null && in_array($error['type'], [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_PARSE])) {
            self::handleException(new \ErrorException($error['message'], 0, $error['type'], $error['file'], $error['line']));
        }
    }

    private static function log(string $message): void
    {
        $date = date('Y-m-d H:i:s');
        $logMessage = "[{$date}] {$message}\n-----------------------------------\n";
        @error_log($logMessage, 3, self::$logFile);
    }
}
