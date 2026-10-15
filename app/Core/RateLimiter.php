<?php

namespace App\Core;

class RateLimiter
{

    /**
     * Check if the rate limit is exceeded
     * Returns true if allowed, false if blocked
     */
    public static function check(string $endpoint): bool
    {
        $ip = self::getIpAddress();
        $db = Database::getInstance();
        
        $stmt = $db->prepare("SELECT attempts, lockout_time, updated_at FROM rate_limits WHERE ip_address = ? AND endpoint = ?");
        $stmt->execute([$ip, $endpoint]);
        $record = $stmt->fetch();
        
        if ($record) {
            $lockoutMinutes = (int)Settings::get('login_lockout_time', 15);
            $now = new \DateTime();

            // 1. Check if locked out
            if (!empty($record['lockout_time'])) {
                $lockoutTime = new \DateTime($record['lockout_time']);
                if ($now < $lockoutTime) {
                    return false; // Still locked out
                } else {
                    // Lockout expired, reset
                    self::reset($endpoint);
                    return true;
                }
            }

            // 2. Sliding window: If past attempts were made longer ago than the lockout window, reset attempts
            if (!empty($record['updated_at'])) {
                $lastUpdated = new \DateTime($record['updated_at']);
                $windowExpiry = (clone $lastUpdated)->add(new \DateInterval('PT' . $lockoutMinutes . 'M'));
                if ($now > $windowExpiry) {
                    self::reset($endpoint);
                    return true;
                }
            }
            
            // 3. Lock out if reached maximum allowed attempts
            if ($record['attempts'] >= (int)Settings::get('login_max_attempts', 5)) {
                $lockoutUntil = (new \DateTime())->add(new \DateInterval('PT' . $lockoutMinutes . 'M'))->format('Y-m-d H:i:s');
                $updateStmt = $db->prepare("UPDATE rate_limits SET lockout_time = ?, updated_at = NOW() WHERE ip_address = ? AND endpoint = ?");
                $updateStmt->execute([$lockoutUntil, $ip, $endpoint]);
                return false;
            }
        }
        
        return true;
    }

    /**
     * Increment the attempt count
     */
    public static function increment(string $endpoint): void
    {
        $ip = self::getIpAddress();
        $db = Database::getInstance();
        
        $stmt = $db->prepare("SELECT id FROM rate_limits WHERE ip_address = ? AND endpoint = ?");
        $stmt->execute([$ip, $endpoint]);
        
        if ($stmt->fetch()) {
            $updateStmt = $db->prepare("UPDATE rate_limits SET attempts = attempts + 1, updated_at = NOW() WHERE ip_address = ? AND endpoint = ?");
            $updateStmt->execute([$ip, $endpoint]);
        } else {
            $insertStmt = $db->prepare("INSERT INTO rate_limits (ip_address, endpoint, attempts, created_at, updated_at) VALUES (?, ?, 1, NOW(), NOW())");
            $insertStmt->execute([$ip, $endpoint]);
        }
    }

    /**
     * Reset attempts (e.g. after successful login)
     */
    public static function reset(string $endpoint): void
    {
        $ip = self::getIpAddress();
        $db = Database::getInstance();
        $stmt = $db->prepare("DELETE FROM rate_limits WHERE ip_address = ? AND endpoint = ?");
        $stmt->execute([$ip, $endpoint]);
    }

    private static function getIpAddress(): string
    {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        // HTTP_X_FORWARDED_FOR can be a comma-separated list, take the first one
        if (strpos($ip, ',') !== false) {
            $ip = explode(',', $ip)[0];
        }
        return trim($ip);
    }
}
