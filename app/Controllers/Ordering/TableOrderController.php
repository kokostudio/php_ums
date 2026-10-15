<?php

namespace App\Controllers\Ordering;

use App\Core\Controller;
use App\Core\Database;

class TableOrderController extends Controller
{
    /**
     * Show digital menu when customer scans table QR code
     */
    public function menu($token)
    {
        $db = Database::getInstance();

        $table = $this->getTableByToken($token);
        if (!$table) {
            return $this->view('ordering/error', ['message' => 'ไม่พบข้อมูลโต๊ะอาหาร หรือ QR Code ไม่ถูกต้อง'], null);
        }

        // Get or create active session
        $session = $this->getOrCreateSession($table['id']);

        // Fetch categories with available items
        $categories = $db->query("
            SELECT * FROM menu_categories 
            WHERE is_active = 1 
            ORDER BY sort_order ASC, id ASC
        ")->fetchAll(\PDO::FETCH_ASSOC);

        $itemsStmt = $db->prepare("
            SELECT m.* 
            FROM menu_items m 
            WHERE m.category_id = ? AND m.is_available = 1
            ORDER BY m.is_recommended DESC, m.sort_order ASC, m.id ASC
        ");

        $optionsStmt = $db->prepare("
            SELECT * FROM menu_item_options 
            WHERE menu_item_id = ? 
            ORDER BY id ASC
        ");

        foreach ($categories as &$cat) {
            $itemsStmt->execute([$cat['id']]);
            $items = $itemsStmt->fetchAll(\PDO::FETCH_ASSOC);
            foreach ($items as &$item) {
                $optionsStmt->execute([$item['id']]);
                $item['options'] = $optionsStmt->fetchAll(\PDO::FETCH_ASSOC);
            }
            $cat['items'] = $items;
        }

        // Count items already ordered in this session
        $orderCountStmt = $db->prepare("
            SELECT COUNT(oi.id) 
            FROM order_items oi
            JOIN orders o ON oi.order_id = o.id
            WHERE o.table_session_id = ? AND oi.kitchen_status != 'cancelled'
        ");
        $orderCountStmt->execute([$session['id']]);
        $orderedCount = (int)$orderCountStmt->fetchColumn();

        $this->view('ordering/menu', [
            'table'        => $table,
            'session'      => $session,
            'categories'   => $categories,
            'orderedCount' => $orderedCount,
            'qr_token'     => $token
        ], null); // Render customer mobile layout
    }

    /**
     * Customer submits order to kitchen
     */
    public function submitOrder($token)
    {
        $table = $this->getTableByToken($token);
        if (!$table) {
            return $this->json(['success' => false, 'message' => 'โต๊ะไม่ถูกต้อง'], 400);
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $cartItems = $input['items'] ?? [];
        $note = trim($input['note'] ?? '');

        if (empty($cartItems)) {
            return $this->json(['success' => false, 'message' => 'กรุณาเลือกรายการอาหารในตะกร้า'], 400);
        }

        $db = Database::getInstance();
        $session = $this->getOrCreateSession($table['id']);

        $orderType = in_array($input['order_type'] ?? '', ['takeaway', 'qsr']) ? 'takeaway' : 'dine_in';
        if ($table['table_number'] === 'QSR-Counter') {
            $orderType = 'takeaway';
        }

        $queueNumber = null;
        if ($orderType === 'takeaway') {
            $countToday = (int)$db->query("SELECT COUNT(*) FROM orders WHERE DATE(created_at) = CURDATE() AND order_type = 'takeaway'")->fetchColumn();
            $queueNumber = 'Q-' . sprintf('%02d', $countToday + 1);
        }

        $db->beginTransaction();
        try {
            // Generate order number
            $orderNumber = 'ORD-' . date('Ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 5));

            $orderStmt = $db->prepare("
                INSERT INTO orders (table_session_id, table_id, order_number, status, order_type, queue_number, customer_note, total_amount)
                VALUES (?, ?, ?, 'pending', ?, ?, ?, 0)
            ");
            $orderStmt->execute([$session['id'], $table['id'], $orderNumber, $orderType, $queueNumber, $note]);
            $orderId = $db->lastInsertId();

            $orderTotal = 0;
            $itemStmt = $db->prepare("
                INSERT INTO order_items (order_id, menu_item_id, quantity, unit_price, subtotal, selected_options, special_note, kitchen_status)
                VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')
            ");

            $menuPriceStmt = $db->prepare("SELECT price, name_th FROM menu_items WHERE id = ?");

            foreach ($cartItems as $cItem) {
                $menuId   = (int)($cItem['id'] ?? 0);
                $quantity = max(1, (int)($cItem['qty'] ?? 1));
                $options  = $cItem['options'] ?? [];
                $special  = trim($cItem['special_note'] ?? '');

                $menuPriceStmt->execute([$menuId]);
                $baseItem = $menuPriceStmt->fetch(\PDO::FETCH_ASSOC);
                if (!$baseItem) continue;

                $unitPrice = (float)$baseItem['price'];
                if (!empty($options)) {
                    foreach ($options as $opt) {
                        $unitPrice += (float)($opt['extra_price'] ?? 0);
                    }
                }

                $subtotal = $unitPrice * $quantity;
                $orderTotal += $subtotal;

                $itemStmt->execute([
                    $orderId,
                    $menuId,
                    $quantity,
                    $unitPrice,
                    $subtotal,
                    !empty($options) ? json_encode($options, JSON_UNESCAPED_UNICODE) : null,
                    $special
                ]);
            }

            // Update order and session total
            $db->prepare("UPDATE orders SET total_amount = ? WHERE id = ?")->execute([$orderTotal, $orderId]);
            $db->prepare("UPDATE table_sessions SET total_amount = total_amount + ? WHERE id = ?")->execute([$orderTotal, $session['id']]);
            $db->prepare("UPDATE restaurant_tables SET status = 'occupied' WHERE id = ?")->execute([$table['id']]);

            $db->commit();

            return $this->json([
                'success'      => true,
                'order_id'     => $orderId,
                'order_number' => $orderNumber,
                'message'      => 'ส่งออเดอร์เข้าครัวเรียบร้อยแล้ว'
            ]);
        } catch (\Exception $e) {
            $db->rollBack();
            return $this->json(['success' => false, 'message' => 'เกิดข้อผิดพลาดในการสั่งอาหาร: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Customer tracks food status in real-time
     */
    public function track($token)
    {
        $table = $this->getTableByToken($token);
        if (!$table) {
            return $this->view('ordering/error', ['message' => 'ไม่พบข้อมูลโต๊ะอาหาร'], null);
        }

        $session = $this->getOrCreateSession($table['id']);
        $orders = $this->getSessionOrders($session['id']);

        $this->view('ordering/track', [
            'table'    => $table,
            'session'  => $session,
            'orders'   => $orders,
            'qr_token' => $token
        ], null);
    }

    /**
     * Bill summary for customer on mobile
     */
    public function bill($token)
    {
        $table = $this->getTableByToken($token);
        if (!$table) {
            return $this->view('ordering/error', ['message' => 'ไม่พบข้อมูลโต๊ะอาหาร'], null);
        }

        $session = $this->getOrCreateSession($table['id']);
        $orders = $this->getSessionOrders($session['id']);

        // Calculate bill breakdown
        $subtotal = 0;
        $totalItemsCount = 0;
        foreach ($orders as $o) {
            foreach ($o['items'] as $item) {
                if ($item['kitchen_status'] !== 'cancelled') {
                    $subtotal += (float)$item['subtotal'];
                    $totalItemsCount += (int)$item['quantity'];
                }
            }
        }

        $this->view('ordering/bill', [
            'table'           => $table,
            'session'         => $session,
            'orders'          => $orders,
            'subtotal'        => $subtotal,
            'totalItemsCount' => $totalItemsCount,
            'qr_token'        => $token
        ], null);
    }

    /**
     * Customer calls waiter / requests billing
     */
    public function callWaiter($token)
    {
        $table = $this->getTableByToken($token);
        if (!$table) {
            return $this->json(['success' => false, 'message' => 'ไม่พบข้อมูลโต๊ะ'], 400);
        }

        $action = trim($_POST['action'] ?? 'bill'); // 'bill' or 'waiter'
        $db = Database::getInstance();

        if ($action === 'bill') {
            $db->prepare("UPDATE restaurant_tables SET status = 'billing' WHERE id = ?")->execute([$table['id']]);
            $db->prepare("UPDATE table_sessions SET status = 'billing' WHERE table_id = ? AND status = 'active'")->execute([$table['id']]);
            $msg = 'ส่งคำขอเช็คบิลไปยังแคชเชียร์เรียบร้อยแล้ว พนักงานจะมาให้บริการโดยเร็วครับ';
        } else {
            $msg = 'ส่งสัญญาณเรียกพนักงานเรียบร้อยแล้วครับ';
        }

        return $this->json([
            'success' => true,
            'message' => $msg
        ]);
    }

    /**
     * Real-time polling API for customer order status
     */
    public function apiStatus($token)
    {
        $table = $this->getTableByToken($token);
        if (!$table) {
            return $this->json(['success' => false, 'message' => 'Invalid table'], 400);
        }

        $session = $this->getOrCreateSession($table['id']);
        $orders = $this->getSessionOrders($session['id']);

        return $this->json([
            'success'      => true,
            'table_status' => $table['status'],
            'orders'       => $orders
        ]);
    }

    /**
     * Generate dynamic PromptPay QR code with exact grand total
     */
    public function promptPayQr($token)
    {
        $table = $this->getTableByToken($token);
        if (!$table) {
            http_response_code(404);
            exit;
        }

        $session = $this->getOrCreateSession($table['id']);
        $subtotal = 0;
        $orders = $this->getSessionOrders($session['id']);
        foreach ($orders as $o) {
            foreach ($o['items'] as $item) {
                if ($item['kitchen_status'] !== 'cancelled') {
                    $subtotal += (float)$item['subtotal'];
                }
            }
        }

        $promptPayTarget = \App\Core\Settings::get('promptpay_number', '0812345678');
        $payload = \App\Core\PromptPay::generatePayload($promptPayTarget, $subtotal > 0 ? $subtotal : null);

        $renderer = new \BaconQrCode\Renderer\ImageRenderer(
            new \BaconQrCode\Renderer\RendererStyle\RendererStyle(280),
            new \BaconQrCode\Renderer\Image\SvgImageBackEnd()
        );
        $writer = new \BaconQrCode\Writer($renderer);
        $svg = $writer->writeString($payload);

        header('Content-Type: image/svg+xml');
        echo $svg;
        exit;
    }

    private function getTableByToken(string $token): ?array
    {
        $db = Database::getInstance();
        if ($token === 'takeaway' || $token === 'qsr') {
            $stmt = $db->prepare("SELECT * FROM restaurant_tables WHERE table_number = 'QSR-Counter' AND is_active = 1");
            $stmt->execute();
            return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
        }

        $stmt = $db->prepare("SELECT * FROM restaurant_tables WHERE qr_token = ? AND is_active = 1");
        $stmt->execute([$token]);
        $table = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $table ?: null;
    }

    private function getOrCreateSession(int $tableId): array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT * FROM table_sessions 
            WHERE table_id = ? AND status IN ('active', 'billing')
            ORDER BY id DESC LIMIT 1
        ");
        $stmt->execute([$tableId]);
        $session = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$session) {
            $sessionToken = bin2hex(random_bytes(16));
            $createStmt = $db->prepare("
                INSERT INTO table_sessions (table_id, session_token, status)
                VALUES (?, ?, 'active')
            ");
            $createStmt->execute([$tableId, $sessionToken]);
            $newId = $db->lastInsertId();

            $stmt->execute([$tableId]);
            $session = $stmt->fetch(\PDO::FETCH_ASSOC);
        }

        return $session;
    }

    private function getSessionOrders(int $sessionId): array
    {
        $db = Database::getInstance();
        $ordersStmt = $db->prepare("
            SELECT o.*, 
                   TIMESTAMPDIFF(MINUTE, o.created_at, NOW()) as elapsed_minutes
            FROM orders o
            WHERE o.table_session_id = ?
            ORDER BY o.id DESC
        ");
        $ordersStmt->execute([$sessionId]);
        $orders = $ordersStmt->fetchAll(\PDO::FETCH_ASSOC);

        $itemStmt = $db->prepare("
            SELECT oi.*, m.name_th, m.name_en, m.image_url
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
