<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Session;

class SearchController extends Controller
{


    public function search()
    {
        $userId = Session::get('user_id');
        if (!$userId) {
            return $this->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $q = $_GET['q'] ?? '';
        $q = trim($q);

        if (strlen($q) < 2) {
            return $this->json([
                'success' => true,
                'results' => [
                    'users' => [],
                    'roles' => [],
                    'logs' => [],
                    'translations' => []
                ]
            ]);
        }

        $likeQuery = '%' . $q . '%';
        $db = Database::getInstance();

        try {
            // 1. Search Users
            $stmt = $db->prepare("
                SELECT id, username, email, status 
                FROM users 
                WHERE username LIKE :q1 OR email LIKE :q2 
                LIMIT 5
            ");
            $stmt->execute(['q1' => $likeQuery, 'q2' => $likeQuery]);
            $users = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // 2. Search Roles
            $stmt = $db->prepare("
                SELECT id, name, description 
                FROM roles 
                WHERE name LIKE :q1 OR description LIKE :q2 
                LIMIT 5
            ");
            $stmt->execute(['q1' => $likeQuery, 'q2' => $likeQuery]);
            $roles = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // 3. Search Activity Logs
            $stmt = $db->prepare("
                SELECT al.id, al.action, al.created_at, u.username 
                FROM activity_logs al 
                LEFT JOIN users u ON al.user_id = u.id 
                WHERE al.action LIKE :q1 OR u.username LIKE :q2 
                ORDER BY al.created_at DESC 
                LIMIT 5
            ");
            $stmt->execute(['q1' => $likeQuery, 'q2' => $likeQuery]);
            $logs = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // 4. Search Translations
            $stmt = $db->prepare("
                SELECT id, namespace, key_name, text_value 
                FROM translations 
                WHERE text_value LIKE :q1 OR key_name LIKE :q2 OR namespace LIKE :q3
                LIMIT 5
            ");
            $stmt->execute(['q1' => $likeQuery, 'q2' => $likeQuery, 'q3' => $likeQuery]);
            $translations = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            foreach ($users as &$u) {
                $u['id'] = \App\Core\Security::encodeId((int)$u['id']);
            }
            foreach ($roles as &$r) {
                $r['id'] = \App\Core\Security::encodeId((int)$r['id']);
            }
            foreach ($logs as &$l) {
                $l['id'] = \App\Core\Security::encodeId((int)$l['id']);
            }
            foreach ($translations as &$t) {
                $t['id'] = \App\Core\Security::encodeId((int)$t['id']);
            }

            return $this->json([
                'success' => true,
                'results' => [
                    'users' => $users,
                    'roles' => $roles,
                    'logs' => $logs,
                    'translations' => $translations
                ]
            ]);

        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
