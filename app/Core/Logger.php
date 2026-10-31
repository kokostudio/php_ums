<?php

namespace App\Core;

class Logger
{
    /** ไฟล์ log ระดับระบบ (error/warning) — แยกจาก activity_logs/audit_logs ในฐานข้อมูล */
    private static string $fileLogFile = __DIR__ . '/../../storage/logs/error.log';

    /** ขนาดสูงสุดของ log ก่อนหมุนไฟล์ (5 MB) */
    private const ROTATE_SIZE = 5 * 1024 * 1024;

    /** จำนวนไฟล์สำรองที่เก็บหลังหมุน (error.log.1, .2, .3) */
    private const ROTATE_KEEP = 3;

    /**
     * Path ของไฟล์ error log (ใช้ร่วมกับ ErrorHandler เพื่อ ensure โฟลเดอร์)
     */
    public static function errorLogPath(): string
    {
        return self::$fileLogFile;
    }

    /**
     * Log user activity
     */
    public static function activity(string $action): void
    {
        $db = Database::getInstance();
        $userId = Session::get('user_id'); // null if not logged in
        $ip = $_SERVER['REMOTE_ADDR'] ?? null;
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? null;

        $stmt = $db->prepare("
            INSERT INTO activity_logs (user_id, action, ip_address, user_agent)
            VALUES (:user_id, :action, :ip, :ua)
        ");
        $stmt->execute([
            'user_id' => $userId,
            'action' => $action,
            'ip' => $ip,
            'ua' => $userAgent
        ]);
    }

    /**
     * Log deep data changes (Audit Log)
     */
    public static function audit(string $tableName, int $recordId, string $action, ?array $oldValues = null, ?array $newValues = null): void
    {
        $db = Database::getInstance();
        $userId = Session::get('user_id');

        $stmt = $db->prepare("
            INSERT INTO audit_logs (user_id, table_name, record_id, action, old_values, new_values)
            VALUES (:user_id, :table_name, :record_id, :action, :old_values, :new_values)
        ");
        $stmt->execute([
            'user_id' => $userId,
            'table_name' => $tableName,
            'record_id' => $recordId,
            'action' => $action,
            'old_values' => $oldValues ? json_encode($oldValues) : null,
            'new_values' => $newValues ? json_encode($newValues) : null
        ]);
    }

    /**
     * บันทึก error ระดับระบบลงไฟล์ (storage/logs/error.log)
     * ใช้ใน catch block ของ controller — ห้ามโยน exception ออกมาเด็ดขาด (กัน error ซ้อน)
     */
    public static function error(string $message, array $context = []): void
    {
        self::writeToFile('ERROR', $message, $context);
    }

    /**
     * บันทึก warning ระดับระบบลงไฟล์ (storage/logs/error.log)
     */
    public static function warning(string $message, array $context = []): void
    {
        self::writeToFile('WARNING', $message, $context);
    }

    /**
     * เขียน log ลงไฟล์พร้อม metadata อัตโนมัติ (วันที่, user_id, IP, URI)
     * ทุกอย่างครอบ try/catch — การเขียน log พังต้องไม่ทำให้ request พังตาม
     */
    private static function writeToFile(string $level, string $message, array $context): void
    {
        try {
            $logDir = dirname(self::$fileLogFile);
            if (!is_dir($logDir)) {
                @mkdir($logDir, 0755, true);
            }

            self::rotateIfNeeded();

            $date = date('Y-m-d H:i:s');
            $userId = Session::get('user_id');
            $ip = $_SERVER['REMOTE_ADDR'] ?? '-';
            $uri = $_SERVER['REQUEST_URI'] ?? '-';

            $meta = [];
            if ($userId) $meta[] = "user_id={$userId}";
            $meta[] = "ip={$ip}";
            $meta[] = "uri={$uri}";
            if ($context !== []) {
                $meta[] = 'context=' . json_encode($context, JSON_UNESCAPED_UNICODE | JSON_PARTIAL_OUTPUT_ON_ERROR);
            }

            $line = sprintf(
                "[%s] %s: %s (%s)\n-----------------------------------\n",
                $date,
                $level,
                $message,
                implode(', ', $meta)
            );

            @error_log($line, 3, self::$fileLogFile);
        } catch (\Throwable $e) {
            // การ log พังต้องเงียบ — ห้ามโยนออกไปซ้อนทับ error เดิม
        }
    }

    /**
     * หมุนไฟล์ log เมื่อเกินขนาด (error.log → error.log.1 → ... → .3 แล้วตัดทิ้ง)
     */
    private static function rotateIfNeeded(): void
    {
        clearstatcache(true, self::$fileLogFile);
        if (!is_file(self::$fileLogFile) || (int) filesize(self::$fileLogFile) < self::ROTATE_SIZE) {
            return;
        }

        // ตัดไฟล์เก่าสุดทิ้ง แล้วเลื่อนลำดับ .2→.3, .1→.2
        $oldest = self::$fileLogFile . '.' . self::ROTATE_KEEP;
        if (is_file($oldest)) {
            @unlink($oldest);
        }
        for ($i = self::ROTATE_KEEP - 1; $i >= 1; $i--) {
            $from = self::$fileLogFile . '.' . $i;
            $to   = self::$fileLogFile . '.' . ($i + 1);
            if (is_file($from)) {
                @rename($from, $to);
            }
        }
        @rename(self::$fileLogFile, self::$fileLogFile . '.1');
    }
}
