<?php

namespace App\Core;

class Logger
{
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
}
