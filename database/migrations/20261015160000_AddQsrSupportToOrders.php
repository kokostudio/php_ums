<?php

use App\Core\Migration;

class AddQsrSupportToOrders extends Migration
{
    public function up(): void
    {
        // 1. Check and add order_type and queue_number to orders
        $cols = $this->db->query("SHOW COLUMNS FROM orders")->fetchAll(PDO::FETCH_COLUMN);
        
        if (!in_array('order_type', $cols)) {
            $this->db->exec("ALTER TABLE orders ADD COLUMN order_type ENUM('dine_in', 'takeaway', 'qsr') DEFAULT 'dine_in' AFTER status");
        }
        if (!in_array('queue_number', $cols)) {
            $this->db->exec("ALTER TABLE orders ADD COLUMN queue_number VARCHAR(20) NULL AFTER order_type");
        }

        // 2. Allow table_session_id and table_id to be nullable
        $this->db->exec("ALTER TABLE orders MODIFY COLUMN table_session_id INT NULL");
        $this->db->exec("ALTER TABLE orders MODIFY COLUMN table_id INT NULL");

        // 3. Add a dedicated counter/takeaway table for QSR orders
        $checkStmt = $this->db->query("SELECT id FROM restaurant_tables WHERE table_number = 'QSR-Counter'");
        if (!$checkStmt->fetch()) {
            $qsrToken = 'qsr_takeaway_token_' . substr(bin2hex(random_bytes(8)), 0, 12);
            $this->db->prepare("
                INSERT INTO restaurant_tables (table_number, zone, seat_capacity, qr_token, status)
                VALUES ('QSR-Counter', 'หน้าร้าน / กลับบ้าน', 0, ?, 'available')
            ")->execute([$qsrToken]);
        }

        // 4. Register queue display permission
        $this->db->exec("
            INSERT IGNORE INTO permissions (module, action, description) 
            VALUES ('pos', 'queue_view', 'เข้าใช้งานหน้าจอเรียกคิวหน้าร้าน (Queue Display TV)')
        ");

        $permId = $this->db->query("SELECT id FROM permissions WHERE module = 'pos' AND action = 'queue_view'")->fetchColumn();
        if ($permId) {
            $this->db->exec("INSERT IGNORE INTO role_permissions (role_id, permission_id) VALUES (1, {$permId}), (2, {$permId})");
        }
    }

    public function down(): void
    {
        $this->db->exec("DELETE FROM restaurant_tables WHERE table_number = 'QSR-Counter'");
        $this->db->exec("DELETE FROM permissions WHERE module = 'pos' AND action = 'queue_view'");
    }
}
