<?php

namespace App\Core;

class ErrorHandler
{
    public static function register(): void
    {
        // Ensure log directory exists
        $logDir = dirname(Logger::errorLogPath());
        if (!is_dir($logDir)) {
            @mkdir($logDir, 0755, true);
        }

        // บังคับปิดการแสดง error บนหน้าจอตั้งแต่ต้น — เปิดเฉพาะเมื่อ APP_DEBUG=true เท่านั้น
        // (เดิม ?debug=1 เปิด display_errors ได้จากภายนอก = รั่ว stack trace บน production)
        if (self::isDebug()) {
            ini_set('display_errors', '1');
            ini_set('display_startup_errors', '1');
        } else {
            ini_set('display_errors', '0');
            ini_set('display_startup_errors', '0');
        }

        error_reporting(E_ALL);
        set_error_handler([self::class, 'handleError']);
        set_exception_handler([self::class, 'handleException']);
        register_shutdown_function([self::class, 'handleFatalError']);
    }

    /**
     * โหมด debug — ยึดจาก APP_DEBUG เท่านั้น (ค่า ?debug=1 ใช้ได้เฉพาะเมื่อ APP_DEBUG=true แล้ว)
     */
    private static function isDebug(): bool
    {
        try {
            $flag = Config::get('APP_DEBUG');
            if ($flag === 'true' || $flag === true || $flag === '1') {
                return true;
            }
        } catch (\Throwable $e) {
            // Config โหลดไม่ได้ — ถือว่าเป็น production (ปิด debug ไว้ก่อน)
        }
        return false;
    }

    public static function handleError(int $level, string $message, string $file = '', int $line = 0): bool
    {
        // Ignore errors suppressed with @ (in PHP 8, error_reporting() returns 0 when @ is used)
        if (!(error_reporting() & $level)) {
            return false;
        }

        // For non-fatal warnings, notices, and deprecations: log them but do NOT crash the page
        if (in_array($level, [E_WARNING, E_NOTICE, E_USER_WARNING, E_USER_NOTICE, E_DEPRECATED, E_USER_DEPRECATED])) {
            Logger::warning("Non-fatal [{$level}]: {$message} in {$file} on line {$line}");
            return true;
        }

        throw new \ErrorException($message, 0, $level, $file, $line);
    }

    public static function handleException(\Throwable $exception): void
    {
        $code = (int)$exception->getCode();
        if (!in_array($code, [403, 404, 419, 429, 401])) {
            $code = 500;
        }

        http_response_code($code);

        $message = "Uncaught Exception: '" . get_class($exception) . "'\n";
        $message .= "Message: '" . $exception->getMessage() . "'\n";
        $message .= "Stack trace:\n" . $exception->getTraceAsString() . "\n";
        $message .= "Thrown in '" . $exception->getFile() . "' on line " . $exception->getLine() . "\n";

        // รายละเอียดเต็มเก็บลง log เสมอ (production ไม่แสดงบนหน้าจอ)
        Logger::error($message);

        // Auto-flush PHP OPcache on 500 errors so newly uploaded files take effect immediately
        if ($code === 500 && function_exists('opcache_reset')) {
            @opcache_reset();
        }

        // AJAX/JSON request → ตอบ JSON ตาม contract ของระบบ (Dual-Response Pattern)
        if (self::wantsJson()) {
            header('Content-Type: application/json; charset=utf-8');
            $payload = [
                'success' => false,
                'type'    => 'error',
                'message' => self::isDebug() ? $exception->getMessage() : self::safeMessage($code),
            ];
            echo json_encode($payload, JSON_UNESCAPED_UNICODE);
            return;
        }

        if (self::isDebug()) {
            echo "<h1>Fatal Error</h1>";
            echo "<pre style='background:#1e293b;color:#f8fafc;padding:1.5rem;border-radius:0.75rem;overflow:auto;font-size:13px;line-height:1.5;'>" . htmlspecialchars($message) . "</pre>";
            return;
        }

        // Production: ข้อความ generic ตามรหัส — ไม่เปิดเผย message/file/line จริง
        $heading = self::heading($code);
        $bodyText = self::safeMessage($code);

        if ($code == 404) {
            echo "<h1>404 Not Found</h1><p>{$bodyText}</p>";
            echo "<a href='/dashboard'>Dashboard</a>";
            return;
        }
        if ($code == 403 || $code == 401 || $code == 419 || $code == 429) {
            echo "<h1>{$heading}</h1><p>{$bodyText}</p>";
            echo "<a href='/dashboard'>Dashboard</a>";
            return;
        }

        // 500 — หน้าจอมาตรฐานของระบบ (รายละเอียดอยู่ใน storage/logs/error.log แล้ว)
        $shortFile = htmlspecialchars(basename($exception->getFile()) . ':' . $exception->getLine());
        echo "<!DOCTYPE html><html lang='th'><head><meta charset='UTF-8'><meta name='viewport' content='width=device-width,initial-scale=1.0'><title>System Notice</title></head>";
        echo "<body style='font-family:system-ui,-apple-system,sans-serif;background:#f8fafc;color:#1e293b;display:flex;align-items:center;justify-content:center;min-height:95vh;margin:0;padding:1rem;'>";
        echo "<div style='max-width:600px;width:100%;background:#ffffff;border:1px solid #e2e8f0;border-radius:1rem;padding:2rem;box-shadow:0 10px 25px -5px rgba(0,0,0,0.05);'>";
        echo "<h2 style='margin-top:0;color:#dc2626;font-size:1.25rem;'>{$heading}</h2>";
        echo "<p style='color:#475569;font-size:0.9rem;line-height:1.6;'>{$bodyText}</p>";
        echo "<div style='background:#fef2f2;border:1px solid #fecaca;color:#991b1b;padding:0.875rem 1rem;border-radius:0.5rem;font-family:monospace;font-size:0.82rem;word-break:break-word;margin:1rem 0;'>";
        echo "<strong>[{$shortFile}]</strong>";
        echo "</div>";
        echo "<div style='display:flex;gap:0.75rem;margin-top:1.25rem;'>";
        echo "<button onclick='location.reload()' style='background:#059669;color:#fff;border:none;padding:0.6rem 1.2rem;border-radius:0.5rem;cursor:pointer;font-weight:600;'>Reload</button>";
        echo "<a href='/dashboard' style='background:#f1f5f9;color:#334155;text-decoration:none;padding:0.6rem 1.2rem;border-radius:0.5rem;font-weight:600;'>Dashboard</a>";
        echo "</div></div></body></html>";
    }

    public static function handleFatalError(): void
    {
        $error = error_get_last();
        if ($error !== null && in_array($error['type'], [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_PARSE])) {
            self::handleException(new \ErrorException($error['message'], 0, $error['type'], $error['file'], $error['line']));
        }
    }

    /**
     * ตรวจว่า client ขอ JSON หรือไม่ (ใช้ logic เดียวกับ Controller::isAjax)
     */
    private static function wantsJson(): bool
    {
        return Request::wantsJson();
    }

    /**
     * ข้อความ generic ปลอดภัยต่อการเปิดเผยข้อมูล (i18n ถ้าโหลดได้ — ไม่ได้ก็ใช้ข้อความสำรอง)
     */
    private static function safeMessage(int $code): string
    {
        $fallback = match ($code) {
            401, 403  => 'You do not have permission to access this resource.',
            404       => 'The page you are looking for could not be found.',
            419       => 'Your session has expired. Please refresh the page and try again.',
            429       => 'Too many requests. Please try again later.',
            default   => 'The system encountered an unexpected error. Please try again.',
        };

        try {
            $key = match ($code) {
                401, 403  => 'common.no_permission',
                404       => 'common.page_not_found',
                419       => 'messages.session_expired',
                429       => 'auth.too_many_attempts',
                default   => 'common.unexpected_error',
            };
            $text = Lang::trans($key);
            // Lang::trans คืน key เองเมื่อแปลไม่เจอ — กรณีนั้นใช้ข้อความสำรองอังกฤษแทน
            return $text === $key ? $fallback : htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
        } catch (\Throwable $e) {
            return $fallback;
        }
    }

    private static function heading(int $code): string
    {
        return match ($code) {
            401       => '401 Unauthorized',
            403       => '403 Forbidden',
            404       => '404 Not Found',
            419       => '419 Session Expired',
            429       => '429 Too Many Requests',
            default   => 'ระบบพบข้อผิดพลาดระหว่างประมวลผล (500)',
        };
    }
}
