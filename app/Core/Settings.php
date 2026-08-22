<?php

namespace App\Core;

class Settings
{
    private static array $cache = [];
    private static bool $loaded = false;

    public static function get(string $key, $default = null)
    {
        if (!self::$loaded) {
            self::loadAll();
        }

        return self::$cache[$key] ?? $default;
    }

    private static function loadAll(): void
    {
        $db = Database::getInstance();
        $stmt = $db->query("SELECT setting_key, setting_value FROM system_settings");
        $results = $stmt->fetchAll();
        foreach ($results as $row) {
            self::$cache[$row['setting_key']] = $row['setting_value'];
        }
        self::$loaded = true;
    }

    public static function set(string $key, string $value): void
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            INSERT INTO system_settings (setting_key, setting_value) 
            VALUES (:key, :val) 
            ON DUPLICATE KEY UPDATE setting_value = :val2
        ");
        $stmt->execute([
            'key' => $key,
            'val' => $value,
            'val2' => $value
        ]);
        
        self::$cache[$key] = $value;
    }
}
