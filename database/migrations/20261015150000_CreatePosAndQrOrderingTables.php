<?php

use App\Core\Migration;

class CreatePosAndQrOrderingTables extends Migration
{
    public function up(): void
    {
        // 1. Restaurant Tables
        $this->db->exec("
            CREATE TABLE IF NOT EXISTS restaurant_tables (
                id INT AUTO_INCREMENT PRIMARY KEY,
                table_number VARCHAR(50) NOT NULL UNIQUE,
                zone VARCHAR(100) DEFAULT 'Main Zone',
                seat_capacity INT DEFAULT 4,
                qr_token VARCHAR(64) NOT NULL UNIQUE,
                status ENUM('available', 'occupied', 'billing') DEFAULT 'available',
                is_active TINYINT(1) DEFAULT 1,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // 2. Table Sessions
        $this->db->exec("
            CREATE TABLE IF NOT EXISTS table_sessions (
                id INT AUTO_INCREMENT PRIMARY KEY,
                table_id INT NOT NULL,
                session_token VARCHAR(64) NOT NULL UNIQUE,
                status ENUM('active', 'billing', 'paid', 'closed') DEFAULT 'active',
                total_amount DECIMAL(10,2) DEFAULT 0.00,
                opened_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                closed_at DATETIME NULL,
                FOREIGN KEY (table_id) REFERENCES restaurant_tables(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // 3. Menu Categories
        $this->db->exec("
            CREATE TABLE IF NOT EXISTS menu_categories (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name_th VARCHAR(100) NOT NULL,
                name_en VARCHAR(100) NOT NULL,
                sort_order INT DEFAULT 0,
                is_active TINYINT(1) DEFAULT 1,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // 4. Menu Items
        $this->db->exec("
            CREATE TABLE IF NOT EXISTS menu_items (
                id INT AUTO_INCREMENT PRIMARY KEY,
                category_id INT NOT NULL,
                name_th VARCHAR(150) NOT NULL,
                name_en VARCHAR(150) NOT NULL,
                description_th TEXT NULL,
                description_en TEXT NULL,
                price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
                image_url VARCHAR(255) NULL,
                is_available TINYINT(1) DEFAULT 1,
                is_recommended TINYINT(1) DEFAULT 0,
                sort_order INT DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (category_id) REFERENCES menu_categories(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // 5. Menu Item Options / Add-ons
        $this->db->exec("
            CREATE TABLE IF NOT EXISTS menu_item_options (
                id INT AUTO_INCREMENT PRIMARY KEY,
                menu_item_id INT NOT NULL,
                group_name VARCHAR(100) NOT NULL,
                option_name VARCHAR(100) NOT NULL,
                extra_price DECIMAL(10,2) DEFAULT 0.00,
                is_required TINYINT(1) DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (menu_item_id) REFERENCES menu_items(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // 6. Orders
        $this->db->exec("
            CREATE TABLE IF NOT EXISTS orders (
                id INT AUTO_INCREMENT PRIMARY KEY,
                table_session_id INT NOT NULL,
                table_id INT NOT NULL,
                order_number VARCHAR(50) NOT NULL UNIQUE,
                status ENUM('pending', 'cooking', 'ready', 'served', 'cancelled') DEFAULT 'pending',
                customer_note TEXT NULL,
                total_amount DECIMAL(10,2) DEFAULT 0.00,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (table_session_id) REFERENCES table_sessions(id) ON DELETE CASCADE,
                FOREIGN KEY (table_id) REFERENCES restaurant_tables(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // 7. Order Items
        $this->db->exec("
            CREATE TABLE IF NOT EXISTS order_items (
                id INT AUTO_INCREMENT PRIMARY KEY,
                order_id INT NOT NULL,
                menu_item_id INT NOT NULL,
                quantity INT NOT NULL DEFAULT 1,
                unit_price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
                subtotal DECIMAL(10,2) NOT NULL DEFAULT 0.00,
                selected_options JSON NULL,
                special_note VARCHAR(255) NULL,
                kitchen_status ENUM('pending', 'cooking', 'ready', 'served', 'cancelled') DEFAULT 'pending',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
                FOREIGN KEY (menu_item_id) REFERENCES menu_items(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // 8. Bills / Payments
        $this->db->exec("
            CREATE TABLE IF NOT EXISTS bills (
                id INT AUTO_INCREMENT PRIMARY KEY,
                table_session_id INT NOT NULL,
                table_id INT NOT NULL,
                bill_number VARCHAR(50) NOT NULL UNIQUE,
                subtotal_amount DECIMAL(10,2) DEFAULT 0.00,
                discount_amount DECIMAL(10,2) DEFAULT 0.00,
                service_charge DECIMAL(10,2) DEFAULT 0.00,
                vat_amount DECIMAL(10,2) DEFAULT 0.00,
                grand_total DECIMAL(10,2) DEFAULT 0.00,
                payment_method ENUM('cash', 'promptpay', 'credit_card', 'other') DEFAULT 'promptpay',
                payment_status ENUM('pending', 'paid', 'cancelled') DEFAULT 'pending',
                cashier_user_id INT NULL,
                paid_at DATETIME NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (table_session_id) REFERENCES table_sessions(id) ON DELETE CASCADE,
                FOREIGN KEY (table_id) REFERENCES restaurant_tables(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // 9. Register POS Permissions in RBAC
        $permissions = [
            ['pos', 'tables_view', 'ดูรายการโต๊ะและ QR Code'],
            ['pos', 'tables_manage', 'จัดการเพิ่ม/แก้ไขโต๊ะอาหาร'],
            ['pos', 'menu_view', 'ดูรายการเมนูอาหาร'],
            ['pos', 'menu_manage', 'จัดการเพิ่ม/แก้ไขเมนูและหมวดหมู่'],
            ['pos', 'orders_view', 'ดูรายการสั่งอาหารทั้งหมด'],
            ['pos', 'orders_manage', 'จัดการออเดอร์และยกเลิกรายการ'],
            ['pos', 'kitchen_view', 'เข้าใช้งานหน้าจอครัว (KDS)'],
            ['pos', 'cashier_view', 'เข้าใช้งานหน้าจอแคชเชียร์และบิล'],
            ['pos', 'cashier_manage', 'เช็คบิลและรับชำระเงิน'],
            ['pos', 'reports_view', 'ดูรายงานยอดขายอาหาร'],
        ];

        $stmt = $this->db->prepare("
            INSERT IGNORE INTO permissions (module, action, description) 
            VALUES (?, ?, ?)
        ");
        foreach ($permissions as $p) {
            $stmt->execute($p);
        }

        // Grant all POS permissions to role 1 (Super Admin) and role 2 (Admin)
        $permIds = $this->db->query("SELECT id FROM permissions WHERE module = 'pos'")->fetchAll(PDO::FETCH_COLUMN);
        $rolePermStmt = $this->db->prepare("INSERT IGNORE INTO role_permissions (role_id, permission_id) VALUES (?, ?)");
        foreach ([1, 2] as $roleId) {
            foreach ($permIds as $pId) {
                $rolePermStmt->execute([$roleId, $pId]);
            }
        }

        // 10. Seed Initial Sample Data (Tables, Categories, Menu Items)
        // Sample Tables
        $tables = [
            ['T01', 'Indoor', 4, bin2hex(random_bytes(16))],
            ['T02', 'Indoor', 4, bin2hex(random_bytes(16))],
            ['T03', 'Indoor', 2, bin2hex(random_bytes(16))],
            ['T04', 'Indoor', 6, bin2hex(random_bytes(16))],
            ['T05', 'Outdoor', 4, bin2hex(random_bytes(16))],
            ['T06', 'Outdoor', 4, bin2hex(random_bytes(16))],
        ];
        $tStmt = $this->db->prepare("
            INSERT IGNORE INTO restaurant_tables (table_number, zone, seat_capacity, qr_token) 
            VALUES (?, ?, ?, ?)
        ");
        foreach ($tables as $t) {
            $tStmt->execute($t);
        }

        // Sample Categories
        $categories = [
            ['อาหารจานหลัก', 'Main Dishes', 1],
            ['ของทานเล่น', 'Appetizers', 2],
            ['เครื่องดื่ม', 'Beverages', 3],
            ['ของหวาน', 'Desserts', 4],
        ];
        $catStmt = $this->db->prepare("
            INSERT IGNORE INTO menu_categories (name_th, name_en, sort_order) 
            VALUES (?, ?, ?)
        ");
        foreach ($categories as $c) {
            $catStmt->execute($c);
        }

        // Sample Menu Items
        $cat1 = $this->db->query("SELECT id FROM menu_categories WHERE name_th = 'อาหารจานหลัก' LIMIT 1")->fetchColumn();
        $cat2 = $this->db->query("SELECT id FROM menu_categories WHERE name_th = 'ของทานเล่น' LIMIT 1")->fetchColumn();
        $cat3 = $this->db->query("SELECT id FROM menu_categories WHERE name_th = 'เครื่องดื่ม' LIMIT 1")->fetchColumn();
        $cat4 = $this->db->query("SELECT id FROM menu_categories WHERE name_th = 'ของหวาน' LIMIT 1")->fetchColumn();

        $menuItems = [
            [$cat1, 'ข้าวกะเพราหมูกรอบไข่ดาว', 'Crispy Pork Basil with Rice & Fried Egg', 'รสเด็ดเผ็ดร้อน หอมกลิ่นกะเพราแท้', 'Spicy stir-fried crispy pork with holy basil', 79.00, 'https://images.unsplash.com/photo-1563379091339-03b21ab4a4f8?w=500&auto=format&fit=crop&q=60', 1, 1],
            [$cat1, 'ข้าวผัดต้มยำกุ้ง', 'Tom Yum Fried Rice with Shrimp', 'ข้าวผัดเครื่องต้มยำเข้มข้น กุ้งแม่น้ำตัวใหญ่', 'Fragrant tom yum flavored fried rice with river prawns', 89.00, 'https://images.unsplash.com/photo-1559847844-5315695dadae?w=500&auto=format&fit=crop&q=60', 1, 1],
            [$cat1, 'ผัดไทยกุ้งสด', 'Pad Thai with Fresh Prawns', 'เส้นจันท์เหนียวนุ่ม ซอสมะขามสูตรโบราณ', 'Traditional stir-fried rice noodles with prawns and tamarind sauce', 85.00, 'https://images.unsplash.com/photo-1559314809-0d155014e29e?w=500&auto=format&fit=crop&q=60', 1, 1],
            [$cat2, 'ไก่ทอดซอสเกาหลี', 'Korean Crispy Fried Chicken', 'ไก่ทอดกรอบนอกนุ่มใน คลุกซอสการ์ลิคฉ่ำๆ', 'Crispy chicken wings glazed with sweet garlic sauce', 69.00, 'https://images.unsplash.com/photo-1626082927389-6cd097cdc6ec?w=500&auto=format&fit=crop&q=60', 1, 0],
            [$cat2, 'เกี๊ยวซ่าทอดกรอบ', 'Crispy Pork Gyoza', 'เกี๊ยวซ่าหมูแน่นๆ ทอดกรอบเสิร์ฟพร้อมน้ำจิ้มงา', 'Pan-fried Japanese dumplings with seasoned dipping sauce', 59.00, 'https://images.unsplash.com/photo-1496116218417-1a781b1c416c?w=500&auto=format&fit=crop&q=60', 1, 0],
            [$cat3, 'ชาไทยโบราณเย็น', 'Traditional Thai Iced Tea', 'ชาไทยสูตรเข้มข้น หวานมันกลมกล่อม', 'Authentic creamy Thai tea with condensed milk', 35.00, 'https://images.unsplash.com/photo-1576092768241-dec231879fc3?w=500&auto=format&fit=crop&q=60', 1, 1],
            [$cat3, 'ชาเขียวมัทฉะลาเต้', 'Matcha Green Tea Latte', 'มัทฉะแท้นำเข้า หอมนุ่มละมุนลิ้น', 'Premium Japanese matcha with fresh milk', 45.00, 'https://images.unsplash.com/photo-1536256263959-770b48d82b0a?w=500&auto=format&fit=crop&q=60', 1, 0],
            [$cat4, 'บิงซูสตรอว์เบอร์รี', 'Strawberry Bingsu', 'เกล็ดหิมะนมสด ท็อปด้วยสตรอว์เบอร์รีสดและไอศกรีม', 'Fluffy milk snow ice topped with fresh strawberries', 89.00, 'https://images.unsplash.com/photo-1501443762994-82bd5dace89a?w=500&auto=format&fit=crop&q=60', 1, 1],
        ];

        $mStmt = $this->db->prepare("
            INSERT IGNORE INTO menu_items (category_id, name_th, name_en, description_th, description_en, price, image_url, is_available, is_recommended)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        foreach ($menuItems as $m) {
            $mStmt->execute($m);
        }

        // Add Options for Pad Krapow (Item 1)
        $krapowId = $this->db->query("SELECT id FROM menu_items WHERE name_th LIKE '%กะเพรา%' LIMIT 1")->fetchColumn();
        if ($krapowId) {
            $optStmt = $this->db->prepare("
                INSERT IGNORE INTO menu_item_options (menu_item_id, group_name, option_name, extra_price, is_required)
                VALUES (?, ?, ?, ?, ?)
            ");
            $optStmt->execute([$krapowId, 'ระดับความเผ็ด', 'เผ็ดน้อย', 0.00, 1]);
            $optStmt->execute([$krapowId, 'ระดับความเผ็ด', 'เผ็ดปกติ', 0.00, 1]);
            $optStmt->execute([$krapowId, 'ระดับความเผ็ด', 'เผ็ดมาก', 0.00, 1]);
            $optStmt->execute([$krapowId, 'ท็อปปิ้งเสริม', 'ไข่ดาวเพิ่ม', 10.00, 0]);
            $optStmt->execute([$krapowId, 'ท็อปปิ้งเสริม', 'หมูกรอบพิเศษ', 20.00, 0]);
        }
    }

    public function down(): void
    {
        $this->db->exec("DROP TABLE IF EXISTS bills");
        $this->db->exec("DROP TABLE IF EXISTS order_items");
        $this->db->exec("DROP TABLE IF EXISTS orders");
        $this->db->exec("DROP TABLE IF EXISTS menu_item_options");
        $this->db->exec("DROP TABLE IF EXISTS menu_items");
        $this->db->exec("DROP TABLE IF EXISTS menu_categories");
        $this->db->exec("DROP TABLE IF EXISTS table_sessions");
        $this->db->exec("DROP TABLE IF EXISTS restaurant_tables");
        $this->db->exec("DELETE FROM permissions WHERE module = 'pos'");
    }
}
