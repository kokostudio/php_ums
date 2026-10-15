<?php

namespace App\Controllers\Pos;

use App\Core\Controller;
use App\Core\Database;

class QueueController extends Controller
{
    public function index()
    {
        $queueData = $this->getQueueData();

        $this->view('pos/queue/display', [
            'pageTitle' => 'จอแสดงคิวรับอาหารหน้าร้าน (Queue TV)',
            'preparing' => $queueData['preparing'],
            'ready'     => $queueData['ready']
        ], null); // Render fullscreen without standard sidebar layout
    }

    public function data()
    {
        return $this->json([
            'success'   => true,
            'timestamp' => time(),
            'data'      => $this->getQueueData()
        ]);
    }

    private function getQueueData(): array
    {
        $db = Database::getInstance();

        // Orders currently cooking (preparing)
        $preparingStmt = $db->query("
            SELECT DISTINCT o.id, o.order_number, o.order_type, o.queue_number, t.table_number
            FROM orders o
            LEFT JOIN restaurant_tables t ON o.table_id = t.id
            WHERE o.status IN ('pending', 'cooking')
              AND EXISTS (
                  SELECT 1 FROM order_items oi 
                  WHERE oi.order_id = o.id AND oi.kitchen_status IN ('pending', 'cooking')
              )
            ORDER BY o.id ASC
            LIMIT 12
        ");
        $preparing = $preparingStmt->fetchAll(\PDO::FETCH_ASSOC);

        // Orders ready for pickup
        $readyStmt = $db->query("
            SELECT DISTINCT o.id, o.order_number, o.order_type, o.queue_number, t.table_number, o.updated_at
            FROM orders o
            LEFT JOIN restaurant_tables t ON o.table_id = t.id
            WHERE o.status != 'cancelled'
              AND EXISTS (
                  SELECT 1 FROM order_items oi 
                  WHERE oi.order_id = o.id AND oi.kitchen_status = 'ready'
              )
              AND NOT EXISTS (
                  SELECT 1 FROM order_items oi2 
                  WHERE oi2.order_id = o.id AND oi2.kitchen_status IN ('pending', 'cooking')
              )
            ORDER BY o.updated_at DESC
            LIMIT 12
        ");
        $ready = $readyStmt->fetchAll(\PDO::FETCH_ASSOC);

        // Format label
        $formatLabel = function($row) {
            if (!empty($row['queue_number'])) {
                return $row['queue_number'];
            }
            if ($row['order_type'] === 'takeaway') {
                return 'T-' . substr($row['order_number'], -4);
            }
            return 'โต๊ะ ' . ($row['table_number'] ?? '-');
        };

        return [
            'preparing' => array_map(function($r) use ($formatLabel) {
                return [
                    'id'          => $r['id'],
                    'label'       => $formatLabel($r),
                    'order_type'  => $r['order_type'],
                    'order_number'=> $r['order_number']
                ];
            }, $preparing),
            'ready' => array_map(function($r) use ($formatLabel) {
                return [
                    'id'          => $r['id'],
                    'label'       => $formatLabel($r),
                    'order_type'  => $r['order_type'],
                    'order_number'=> $r['order_number']
                ];
            }, $ready)
        ];
    }
}
