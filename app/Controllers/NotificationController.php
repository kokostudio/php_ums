<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Session;

class NotificationController extends Controller
{
    private function json($data, $status = 200)
    {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code($status);
        echo json_encode($data);
        exit;
    }

    public function index()
    {
        $userId = Session::get('user_id');
        $db = Database::getInstance();

        $stmt = $db->prepare("SELECT * FROM notifications WHERE user_id = :user_id ORDER BY created_at DESC LIMIT 20");
        $stmt->execute(['user_id' => $userId]);
        $notifications = $stmt->fetchAll();

        $unreadCountStmt = $db->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = :user_id AND is_read = 0");
        $unreadCountStmt->execute(['user_id' => $userId]);
        $unreadCount = $unreadCountStmt->fetchColumn();

        $this->json([
            'success' => true,
            'notifications' => $notifications,
            'unreadCount' => (int) $unreadCount
        ]);
    }

    public function markAsRead()
    {
        $input = json_decode(file_get_contents('php://input'), true);
        $id = $input['id'] ?? null;
        $userId = Session::get('user_id');

        if ($id) {
            $db = Database::getInstance();
            $stmt = $db->prepare("UPDATE notifications SET is_read = 1 WHERE id = :id AND user_id = :user_id");
            $stmt->execute(['id' => $id, 'user_id' => $userId]);
        }

        $this->json(['success' => true]);
    }

    public function markAllAsRead()
    {
        $userId = Session::get('user_id');
        $db = Database::getInstance();
        
        $stmt = $db->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = :user_id");
        $stmt->execute(['user_id' => $userId]);

        $this->json(['success' => true]);
    }
}
