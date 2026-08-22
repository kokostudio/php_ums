<?php

use App\Core\Lang;

if (!function_exists('__')) {
    /**
     * Translate a language key.
     * 
     * @param string $key    Dot-notation key, e.g. 'common.save', 'auth.username'
     * @param array  $params Optional placeholder values, e.g. ['name' => 'Admin']
     */
    function __(string $key, array $params = []): string
    {
        return Lang::trans($key, $params);
    }
}

if (!function_exists('formatDate')) {
    /**
     * Format date or datetime to standard d/m/Y (e.g. 30/12/2026)
     * 
     * @param string|null $datetime    Datetime string from database
     * @param bool        $withTime    Include hours and minutes (d/m/Y H:i)
     * @param bool        $withSeconds Include seconds (d/m/Y H:i:s)
     * @return string
     */
    function formatDate(?string $datetime, bool $withTime = false, bool $withSeconds = false): string
    {
        if (empty($datetime)) {
            return '-';
        }
        $timestamp = strtotime($datetime);
        if (!$timestamp) {
            return $datetime;
        }
        if ($withSeconds) {
            return date('d/m/Y H:i:s', $timestamp);
        }
        if ($withTime) {
            return date('d/m/Y H:i', $timestamp);
        }
        return date('d/m/Y', $timestamp);
    }
}

