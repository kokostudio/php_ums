<?php

namespace App\Core;

/**
 * Lang — Multi-language helper
 * 
 * Usage: Lang::trans('common.save')  or via helper __('common.save')
 * Supports dot-notation: 'namespace.key'
 * Supports placeholders: __('common.welcome', ['name' => 'Admin'])
 */
class Lang
{
    private static string $locale = 'th';
    private static array $loaded = [];

    /**
     * Bootstrap: load locale from session (call once on every request)
     */
    public static function init(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $stored = $_SESSION['app_locale'] ?? 'th';
        static::$locale = in_array($stored, ['th', 'en']) ? $stored : 'th';
    }

    /**
     * Get current locale code
     */
    public static function locale(): string
    {
        return static::$locale;
    }

    /**
     * Set locale and persist to session
     */
    public static function set(string $locale): void
    {
        $locale = in_array($locale, ['th', 'en']) ? $locale : 'th';
        static::$locale = $locale;
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['app_locale'] = $locale;
    }

    /**
     * Translate a key with optional placeholder substitution.
     * Key format: 'namespace.key'  e.g. 'common.save', 'auth.username'
     * If no namespace given, falls back to searching 'common'
     */
    public static function trans(string $key, array $params = []): string
    {
        if (strpos($key, '.') !== false) {
            [$namespace, $subKey] = explode('.', $key, 2);
        } else {
            $namespace = 'common';
            $subKey    = $key;
        }

        $lines = static::load($namespace);
        $text  = $lines[$subKey] ?? $key; // fallback to key itself

        // Replace :placeholder with values
        foreach ($params as $placeholder => $value) {
            $text = str_replace(':' . $placeholder, $value, $text);
        }

        return $text;
    }

    /**
     * Lazy-load a language namespace file from the database.
     * Tries current locale first, falls back to 'en', then 'th'.
     */
    private static function load(string $namespace): array
    {
        $cacheKey = static::$locale . '.' . $namespace;
        if (isset(static::$loaded[$cacheKey])) {
            return static::$loaded[$cacheKey];
        }

        try {
            $db = Database::getInstance();
            $locales = [static::$locale, 'th', 'en'];

            foreach ($locales as $locale) {
                $stmt = $db->prepare("SELECT key_name, text_value FROM translations WHERE locale = ? AND namespace = ?");
                $stmt->execute([$locale, $namespace]);
                $data = $stmt->fetchAll(\PDO::FETCH_KEY_PAIR);

                if (!empty($data)) {
                    static::$loaded[$cacheKey] = $data;
                    return $data;
                }
            }
        } catch (\Exception $e) {
            // Fallback to empty array if DB fails or table not found
        }

        static::$loaded[$cacheKey] = [];
        return [];
    }
}
