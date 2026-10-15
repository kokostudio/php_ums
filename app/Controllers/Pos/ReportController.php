<?php

namespace App\Controllers\Pos;

use App\Core\Controller;
use App\Core\Database;

class ReportController extends Controller
{
    public function index()
    {
        $db = Database::getInstance();

        $fromDate = $_GET['from_date'] ?? date('Y-m-d');
        $toDate   = $_GET['to_date'] ?? date('Y-m-d');

        // Total metrics for selected period
        $statsStmt = $db->prepare("
            SELECT 
                COALESCE(SUM(grand_total), 0) AS total_sales,
                COUNT(id) AS total_bills,
                COALESCE(AVG(grand_total), 0) AS avg_bill,
                COALESCE(SUM(discount_amount), 0) AS total_discounts
            FROM bills
            WHERE DATE(paid_at) BETWEEN ? AND ? AND payment_status = 'paid'
        ");
        $statsStmt->execute([$fromDate, $toDate]);
        $stats = $statsStmt->fetch(\PDO::FETCH_ASSOC);

        // Total dishes/items sold
        $dishesStmt = $db->prepare("
            SELECT COALESCE(SUM(oi.quantity), 0)
            FROM order_items oi
            JOIN orders o ON oi.order_id = o.id
            JOIN bills b ON o.table_session_id = b.table_session_id
            WHERE DATE(b.paid_at) BETWEEN ? AND ? AND oi.kitchen_status != 'cancelled'
        ");
        $dishesStmt->execute([$fromDate, $toDate]);
        $stats['total_dishes'] = (int)$dishesStmt->fetchColumn();

        // Payment Methods Breakdown
        $paymentStmt = $db->prepare("
            SELECT payment_method, COUNT(id) as bill_count, COALESCE(SUM(grand_total), 0) as total_amount
            FROM bills
            WHERE DATE(paid_at) BETWEEN ? AND ? AND payment_status = 'paid'
            GROUP BY payment_method
        ");
        $paymentStmt->execute([$fromDate, $toDate]);
        $paymentBreakdown = $paymentStmt->fetchAll(\PDO::FETCH_ASSOC);

        // Top 5 Best-Selling Menu Items
        $topItemsStmt = $db->prepare("
            SELECT m.name_th, m.image_url, 
                   SUM(oi.quantity) as sold_qty, 
                   SUM(oi.subtotal) as total_revenue
            FROM order_items oi
            JOIN menu_items m ON oi.menu_item_id = m.id
            JOIN orders o ON oi.order_id = o.id
            JOIN bills b ON o.table_session_id = b.table_session_id
            WHERE DATE(b.paid_at) BETWEEN ? AND ? AND oi.kitchen_status != 'cancelled'
            GROUP BY m.id
            ORDER BY sold_qty DESC
            LIMIT 5
        ");
        $topItemsStmt->execute([$fromDate, $toDate]);
        $topItems = $topItemsStmt->fetchAll(\PDO::FETCH_ASSOC);

        // Daily Revenue Trend (Last 7 Days)
        $trendStmt = $db->query("
            SELECT DATE(paid_at) as sale_date, COALESCE(SUM(grand_total), 0) as daily_total
            FROM bills
            WHERE paid_at >= DATE_SUB(CURDATE(), INTERVAL 6 DAY) AND payment_status = 'paid'
            GROUP BY DATE(paid_at)
            ORDER BY sale_date ASC
        ");
        $trendData = $trendStmt->fetchAll(\PDO::FETCH_ASSOC);

        // Recent Completed Bills
        $billsStmt = $db->prepare("
            SELECT b.*, t.table_number, t.zone, u.username as cashier_name
            FROM bills b
            JOIN restaurant_tables t ON b.table_id = t.id
            LEFT JOIN users u ON b.cashier_user_id = u.id
            WHERE DATE(b.paid_at) BETWEEN ? AND ? AND b.payment_status = 'paid'
            ORDER BY b.id DESC
            LIMIT 50
        ");
        $billsStmt->execute([$fromDate, $toDate]);
        $bills = $billsStmt->fetchAll(\PDO::FETCH_ASSOC);

        $this->view('pos/reports/index', [
            'pageTitle'        => 'รายงานยอดขายและสถิติ',
            'fromDate'         => $fromDate,
            'toDate'           => $toDate,
            'stats'            => $stats,
            'paymentBreakdown' => $paymentBreakdown,
            'topItems'         => $topItems,
            'trendData'        => $trendData,
            'bills'            => $bills
        ]);
    }
}
