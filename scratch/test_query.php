<?php
require 'vendor/autoload.php';

\App\Core\Config::load('.env');
$db = \App\Core\Database::getInstance();
$userId = 1;

try {
    $stmt = $db->prepare("SELECT * FROM notifications WHERE user_id = :user_id ORDER BY created_at DESC LIMIT 20");
    $stmt->execute(['user_id' => $userId]);
    $notifications = $stmt->fetchAll();

    $unreadCountStmt = $db->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = :user_id AND is_read = 0");
    $unreadCountStmt->execute(['user_id' => $userId]);
    $unreadCount = $unreadCountStmt->fetchColumn();

    echo "Success! Unread: " . $unreadCount;
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
