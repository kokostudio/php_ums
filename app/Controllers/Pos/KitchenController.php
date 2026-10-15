<?php

namespace App\Controllers\Pos;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Security;

class KitchenController extends Controller
{
    public function index()
    {
        $orders = $this->getActiveOrders();

        $this->view('pos/kitchen/index', [
            'pageTitle' => __('pos.kitchen_display'),
            'orders'    => $orders
        ]);
    }

    public function activeOrdersJson()
    {
        $orders = $this->getActiveOrders();
        return $this->json([
            'success'   => true,
            'count'     => count($orders),
            'orders'    => $orders,
            'timestamp' => time()
        ]);
    }

    public function updateStatus()
    {
        $itemId = (int)($_POST['item_id'] ?? 0);
        $status = trim($_POST['status'] ?? '');

        $validStatuses = ['pending', 'cooking', 'ready', 'served', 'cancelled'];
        if ($itemId <= 0 || !in_array($status, $validStatuses)) {
            return $this->json(['success' => false, 'message' => 'Invalid parameters'], 400);
        }

        $db = Database::getInstance();
        $stmt = $db->prepare("UPDATE order_items SET kitchen_status = ? WHERE id = ?");
        $stmt->execute([$status, $itemId]);

        // Check if all items in this order are served, update parent order status
        $orderIdStmt = $db->prepare("SELECT order_id FROM order_items WHERE id = ?");
        $orderIdStmt->execute([$itemId]);
        $orderId = $orderIdStmt->fetchColumn();

        if ($orderId) {
            $unservedStmt = $db->prepare("
                SELECT COUNT(*) FROM order_items 
                WHERE order_id = ? AND kitchen_status NOT IN ('served', 'cancelled')
            ");
            $unservedStmt->execute([$orderId]);
            $unservedCount = (int)$unservedStmt->fetchColumn();

            if ($unservedCount === 0) {
                $db->prepare("UPDATE orders SET status = 'served' WHERE id = ?")->execute([$orderId]);
            } else {
                $cookingStmt = $db->prepare("
                    SELECT COUNT(*) FROM order_items 
                    WHERE order_id = ? AND kitchen_status = 'cooking'
                ");
                $cookingStmt->execute([$orderId]);
                if ((int)$cookingStmt->fetchColumn() > 0) {
                    $db->prepare("UPDATE orders SET status = 'cooking' WHERE id = ?")->execute([$orderId]);
                }
            }
        }

        return $this->json([
            'success' => true,
            'status'  => $status,
            'message' => 'Status updated'
        ]);
    }

    private function getActiveOrders(): array
    {
        $db = Database::getInstance();

        // Fetch orders where items are not yet fully served
        $orders = $db->query("
            SELECT o.id, o.order_number, o.created_at, o.customer_note, o.status as order_status,
                   o.order_type, o.queue_number,
                   t.table_number, t.zone,
                   TIMESTAMPDIFF(MINUTE, o.created_at, NOW()) as elapsed_minutes
            FROM orders o
            JOIN restaurant_tables t ON o.table_id = t.id
            WHERE EXISTS (
                SELECT 1 FROM order_items oi 
                WHERE oi.order_id = o.id AND oi.kitchen_status IN ('pending', 'cooking', 'ready')
            )
            ORDER BY o.created_at ASC
        ")->fetchAll(\PDO::FETCH_ASSOC);

        $itemStmt = $db->prepare("
            SELECT oi.id, oi.quantity, oi.selected_options, oi.special_note, oi.kitchen_status,
                   m.name_th, m.name_en, m.image_url
            FROM order_items oi
            JOIN menu_items m ON oi.menu_item_id = m.id
            WHERE oi.order_id = ?
            ORDER BY oi.id ASC
        ");

        foreach ($orders as &$order) {
            $itemStmt->execute([$order['id']]);
            $items = $itemStmt->fetchAll(\PDO::FETCH_ASSOC);
            foreach ($items as &$it) {
                $it['options'] = !empty($it['selected_options']) ? json_decode($it['selected_options'], true) : [];
            }
            $order['items'] = $items;
        }

        return $orders;
    }
}
