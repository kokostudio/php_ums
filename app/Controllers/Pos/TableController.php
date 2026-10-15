<?php

namespace App\Controllers\Pos;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Security;
use App\Core\Session;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class TableController extends Controller
{
    public function index()
    {
        $db = Database::getInstance();

        // Get all tables with active session info
        $tables = $db->query("
            SELECT t.*, 
                   s.id AS active_session_id, 
                   s.total_amount,
                   s.opened_at,
                   (SELECT COUNT(*) FROM orders o WHERE o.table_session_id = s.id) AS order_count
            FROM restaurant_tables t
            LEFT JOIN table_sessions s ON t.id = s.table_id AND s.status IN ('active', 'billing')
            ORDER BY t.table_number ASC
        ")->fetchAll(\PDO::FETCH_ASSOC);

        // Calculate statistics
        $stats = [
            'total'     => count($tables),
            'available' => 0,
            'occupied'  => 0,
            'billing'   => 0
        ];

        foreach ($tables as $t) {
            if ($t['status'] === 'occupied') {
                $stats['occupied']++;
            } elseif ($t['status'] === 'billing') {
                $stats['billing']++;
            } else {
                $stats['available']++;
            }
        }

        $zones = $db->query("SELECT DISTINCT zone FROM restaurant_tables WHERE zone IS NOT NULL ORDER BY zone ASC")->fetchAll(\PDO::FETCH_COLUMN);

        $this->view('pos/tables/index', [
            'pageTitle' => __('pos.table_management'),
            'tables'    => $tables,
            'stats'     => $stats,
            'zones'     => $zones
        ]);
    }

    public function store()
    {
        Security::checkCsrf();
        $db = Database::getInstance();

        $tableNumber = trim($_POST['table_number'] ?? '');
        $zone        = trim($_POST['zone'] ?? 'Main Zone');
        $capacity    = (int)($_POST['seat_capacity'] ?? 4);

        if (empty($tableNumber)) {
            Session::setFlash('error', 'กรุณาระบุหมายเลขโต๊ะ');
            $this->redirect('/pos/tables');
        }

        // Check unique
        $stmt = $db->prepare("SELECT id FROM restaurant_tables WHERE table_number = ?");
        $stmt->execute([$tableNumber]);
        if ($stmt->fetch()) {
            Session::setFlash('error', 'หมายเลขโต๊ะนี้มีอยู่แล้วในระบบ');
            $this->redirect('/pos/tables');
        }

        $qrToken = bin2hex(random_bytes(16));
        $stmt = $db->prepare("
            INSERT INTO restaurant_tables (table_number, zone, seat_capacity, qr_token, status)
            VALUES (?, ?, ?, ?, 'available')
        ");
        $stmt->execute([$tableNumber, $zone, $capacity, $qrToken]);

        Session::setFlash('success', 'เพิ่มโต๊ะอาหารสำเร็จ');
        $this->redirect('/pos/tables');
    }

    public function update($id)
    {
        Security::checkCsrf();
        $db = Database::getInstance();

        $tableNumber = trim($_POST['table_number'] ?? '');
        $zone        = trim($_POST['zone'] ?? 'Main Zone');
        $capacity    = (int)($_POST['seat_capacity'] ?? 4);
        $isActive    = isset($_POST['is_active']) ? 1 : 0;

        $stmt = $db->prepare("SELECT id FROM restaurant_tables WHERE table_number = ? AND id != ?");
        $stmt->execute([$tableNumber, $id]);
        if ($stmt->fetch()) {
            Session::setFlash('error', 'หมายเลขโต๊ะนี้ซ้ำกับโต๊ะอื่น');
            $this->redirect('/pos/tables');
        }

        $stmt = $db->prepare("
            UPDATE restaurant_tables 
            SET table_number = ?, zone = ?, seat_capacity = ?, is_active = ?
            WHERE id = ?
        ");
        $stmt->execute([$tableNumber, $zone, $capacity, $isActive, $id]);

        Session::setFlash('success', 'อัปเดตข้อมูลโต๊ะสำเร็จ');
        $this->redirect('/pos/tables');
    }

    public function delete($id)
    {
        Security::checkCsrf();
        $db = Database::getInstance();

        $stmt = $db->prepare("DELETE FROM restaurant_tables WHERE id = ?");
        $stmt->execute([$id]);

        Session::setFlash('success', 'ลบโต๊ะเรียบร้อยแล้ว');
        $this->redirect('/pos/tables');
    }

    public function reset($id)
    {
        Security::checkCsrf();
        $db = Database::getInstance();

        // Close any active session
        $stmt = $db->prepare("
            UPDATE table_sessions 
            SET status = 'closed', closed_at = NOW() 
            WHERE table_id = ? AND status IN ('active', 'billing')
        ");
        $stmt->execute([$id]);

        // Reset table status
        $stmt = $db->prepare("UPDATE restaurant_tables SET status = 'available' WHERE id = ?");
        $stmt->execute([$id]);

        Session::setFlash('success', 'รีเซ็ตสถานะโต๊ะเป็นโต๊ะว่างเรียบร้อยแล้ว');
        $this->redirect('/pos/tables');
    }

    public function qr($token)
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT table_number FROM restaurant_tables WHERE qr_token = ?");
        $stmt->execute([$token]);
        $table = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$table) {
            http_response_code(404);
            echo "Table not found";
            exit;
        }

        // Build target URL
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
        $host = $_SERVER['HTTP_HOST'] ?? 'php_ums.localhost';
        $orderUrl = "{$protocol}{$host}/order/table/{$token}";

        $renderer = new ImageRenderer(
            new RendererStyle(300),
            new SvgImageBackEnd()
        );
        $writer = new Writer($renderer);
        $svg = $writer->writeString($orderUrl);

        header('Content-Type: image/svg+xml');
        echo $svg;
        exit;
    }

    public function printQr($id)
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM restaurant_tables WHERE id = ?");
        $stmt->execute([$id]);
        $table = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$table) {
            Session::setFlash('error', 'ไม่พบข้อมูลโต๊ะ');
            $this->redirect('/pos/tables');
        }

        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
        $host = $_SERVER['HTTP_HOST'] ?? 'php_ums.localhost';
        $orderUrl = "{$protocol}{$host}/order/table/{$table['qr_token']}";

        $renderer = new ImageRenderer(
            new RendererStyle(350),
            new SvgImageBackEnd()
        );
        $writer = new Writer($renderer);
        $svgQr = $writer->writeString($orderUrl);

        $this->view('pos/tables/print_qr', [
            'table'    => $table,
            'orderUrl' => $orderUrl,
            'svgQr'    => $svgQr
        ], null); // Render without admin layout for clean printing
    }
}
