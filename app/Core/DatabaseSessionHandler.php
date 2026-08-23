<?php

namespace App\Core;

use SessionHandlerInterface;

class DatabaseSessionHandler implements SessionHandlerInterface
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function open(string $path, string $name): bool
    {
        return true;
    }

    public function close(): bool
    {
        return true;
    }

    public function read(string $id): string|false
    {
        $stmt = $this->db->prepare("SELECT payload FROM user_sessions WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        if ($row) {
            return $row['payload'];
        }

        return '';
    }

    public function write(string $id, string $data): bool
    {
        // We can optionally parse the session data to extract user_id, but it's easier 
        // to have a helper method to update user_id in the row separately.
        $userId = $_SESSION['user_id'] ?? null;
        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? null;
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? null;
        $time = time();

        $stmt = $this->db->prepare("
            INSERT INTO user_sessions (id, user_id, ip_address, user_agent, payload, last_activity)
            VALUES (:id, :user_id, :ip_address, :user_agent, :payload, :last_activity)
            ON DUPLICATE KEY UPDATE 
                user_id = VALUES(user_id),
                payload = VALUES(payload),
                last_activity = VALUES(last_activity),
                ip_address = VALUES(ip_address),
                user_agent = VALUES(user_agent)
        ");

        $stmt->execute([
            'id' => $id,
            'user_id' => $userId,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'payload' => $data,
            'last_activity' => $time
        ]);

        return true;
    }

    public function destroy(string $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM user_sessions WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return true;
    }

    public function gc(int $max_lifetime): int|false
    {
        $past = time() - $max_lifetime;
        $stmt = $this->db->prepare("DELETE FROM user_sessions WHERE last_activity < :past");
        $stmt->execute(['past' => $past]);
        return $stmt->rowCount();
    }
}
