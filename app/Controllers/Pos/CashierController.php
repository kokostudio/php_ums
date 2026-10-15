<?php

namespace App\Controllers\Pos;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Security;
use App\Core\Session;

class CashierController extends Controller
{
    public function index()
    {
        $db = Database::getInstance();

        $tables = $db->query("
            SELECT t.*, 
                   s.id AS active_session_id, 
                   s.total_amount,
                   s.opened_at,
                   TIMESTAMPDIFF(MINUTE, s.opened_at, NOW()) as dining_minutes,
                   (SELECT COUNT(*) FROM orders o WHERE o.table_session_id = s.id) AS order_count
            FROM restaurant_tables t
            LEFT JOIN table_sessions s ON t.id = s.table_id AND s.status IN ('active', 'billing')
            ORDER BY t.table_number ASC
        ")->fetchAll(\PDO::FETCH_ASSOC);

        $recentBills = $db->query("
            SELECT b.*, t.table_number, u.username as cashier_name
            FROM bills b
            JOIN restaurant_tables t ON b.table_id = t.id
            LEFT JOIN users u ON b.cashier_user_id = u.id
            ORDER BY b.id DESC
            LIMIT 15
        ")->fetchAll(\PDO::FETCH_ASSOC);

        $this->view('pos/cashier/index', [
            'pageTitle'   => __('pos.cashier_billing'),
            'tables'      => $tables,
            'recentBills' => $recentBills
        ]);
    }

    public function tableDetails($tableId)
    {
        $db = Database::getInstance();

        $sessionStmt = $db->prepare("
            SELECT s.*, t.table_number, t.zone, t.qr_token 
            FROM table_sessions s
            JOIN restaurant_tables t ON s.table_id = t.id
            WHERE s.table_id = ? AND s.status IN ('active', 'billing')
            ORDER BY s.id DESC LIMIT 1
        ");
        $sessionStmt->execute([$tableId]);
        $session = $sessionStmt->fetch(\PDO::FETCH_ASSOC);

        if (!$session) {
            return $this->json(['success' => false, 'message' => 'No active session for this table'], 404);
        }

        $ordersStmt = $db->prepare("
            SELECT o.id, o.order_number, o.created_at, o.customer_note
            FROM orders o
            WHERE o.table_session_id = ?
            ORDER BY o.created_at ASC
        ");
        $ordersStmt->execute([$session['id']]);
        $orders = $ordersStmt->fetchAll(\PDO::FETCH_ASSOC);

        $itemStmt = $db->prepare("
            SELECT oi.*, m.name_th, m.name_en 
            FROM order_items oi
            JOIN menu_items m ON oi.menu_item_id = m.id
            WHERE oi.order_id = ?
        ");

        $totalSubtotal = 0;
        foreach ($orders as &$order) {
            $itemStmt->execute([$order['id']]);
            $items = $itemStmt->fetchAll(\PDO::FETCH_ASSOC);
            foreach ($items as &$it) {
                $it['options'] = !empty($it['selected_options']) ? json_decode($it['selected_options'], true) : [];
                $totalSubtotal += (float)$it['subtotal'];
            }
            $order['items'] = $items;
        }

        return $this->json([
            'success'        => true,
            'session'        => $session,
            'orders'         => $orders,
            'calculated_total' => $totalSubtotal
        ]);
    }

    public function checkout()
    {
        Security::checkCsrf();
        $db = Database::getInstance();

        $sessionId     = (int)($_POST['session_id'] ?? 0);
        $paymentMethod = trim($_POST['payment_method'] ?? 'cash');
        $discount      = (float)($_POST['discount_amount'] ?? 0);

        $sessionStmt = $db->prepare("SELECT * FROM table_sessions WHERE id = ? AND status IN ('active', 'billing')");
        $sessionStmt->execute([$sessionId]);
        $session = $sessionStmt->fetch(\PDO::FETCH_ASSOC);

        if (!$session) {
            Session::setFlash('error', 'ไม่พบเซสชันโต๊ะที่เปิดอยู่');
            $this->redirect('/pos/cashier');
        }

        // Calculate accurate total from items
        $sumStmt = $db->prepare("
            SELECT COALESCE(SUM(oi.subtotal), 0) as subtotal
            FROM order_items oi
            JOIN orders o ON oi.order_id = o.id
            WHERE o.table_session_id = ? AND oi.kitchen_status != 'cancelled'
        ");
        $sumStmt->execute([$sessionId]);
        $subtotal = (float)$sumStmt->fetchColumn();

        $grandTotal = max(0, $subtotal - $discount);
        $billNumber = 'BILL-' . date('Ymd') . '-' . sprintf('%04d', $sessionId);

        // Insert Bill
        $billStmt = $db->prepare("
            INSERT INTO bills (table_session_id, table_id, bill_number, subtotal_amount, discount_amount, grand_total, payment_method, payment_status, cashier_user_id, paid_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, 'paid', ?, NOW())
        ");
        $billStmt->execute([
            $sessionId,
            $session['table_id'],
            $billNumber,
            $subtotal,
            $discount,
            $grandTotal,
            $paymentMethod,
            Session::get('user_id')
        ]);
        $billId = $db->lastInsertId();

        // Close session
        $db->prepare("
            UPDATE table_sessions 
            SET status = 'paid', total_amount = ?, closed_at = NOW() 
            WHERE id = ?
        ")->execute([$grandTotal, $sessionId]);

        // Reset Table status
        $db->prepare("UPDATE restaurant_tables SET status = 'available' WHERE id = ?")->execute([$session['table_id']]);

        Session::setFlash('success', "ชำระเงินสำเร็จ (บิล {$billNumber}) ปิดโต๊ะเรียบร้อยแล้ว");
        $this->redirect('/pos/cashier/receipt/' . $billId);
    }

    public function receipt($billId)
    {
        $db = Database::getInstance();

        $stmt = $db->prepare("
            SELECT b.*, t.table_number, t.zone, u.username as cashier_name
            FROM bills b
            JOIN restaurant_tables t ON b.table_id = t.id
            LEFT JOIN users u ON b.cashier_user_id = u.id
            WHERE b.id = ?
        ");
        $stmt->execute([$billId]);
        $bill = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$bill) {
            Session::setFlash('error', 'ไม่พบใบเสร็จนี้');
            $this->redirect('/pos/cashier');
        }

        // Get items for this bill
        $itemsStmt = $db->prepare("
            SELECT oi.quantity, oi.unit_price, oi.subtotal, oi.selected_options, m.name_th, m.name_en
            FROM order_items oi
            JOIN orders o ON oi.order_id = o.id
            JOIN menu_items m ON oi.menu_item_id = m.id
            WHERE o.table_session_id = ? AND oi.kitchen_status != 'cancelled'
        ");
        $itemsStmt->execute([$bill['table_session_id']]);
        $items = $itemsStmt->fetchAll(\PDO::FETCH_ASSOC);
        foreach ($items as &$it) {
            $it['options'] = !empty($it['selected_options']) ? json_decode($it['selected_options'], true) : [];
        }

        $this->view('pos/cashier/receipt', [
            'bill'  => $bill,
            'items' => $items
        ], null); // Render slip without layout for printing
    }
}
