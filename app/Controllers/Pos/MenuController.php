<?php

namespace App\Controllers\Pos;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Security;
use App\Core\Session;
use App\Core\UploadManager;

class MenuController extends Controller
{
    public function index()
    {
        $db = Database::getInstance();

        $categories = $db->query("
            SELECT c.*, COUNT(m.id) as item_count 
            FROM menu_categories c
            LEFT JOIN menu_items m ON c.id = m.category_id
            GROUP BY c.id
            ORDER BY c.sort_order ASC, c.id ASC
        ")->fetchAll(\PDO::FETCH_ASSOC);

        $menuItems = $db->query("
            SELECT m.*, c.name_th as category_name_th, c.name_en as category_name_en,
                   (SELECT COUNT(*) FROM menu_item_options o WHERE o.menu_item_id = m.id) as options_count
            FROM menu_items m
            LEFT JOIN menu_categories c ON m.category_id = c.id
            ORDER BY m.category_id ASC, m.sort_order ASC, m.id DESC
        ")->fetchAll(\PDO::FETCH_ASSOC);

        $this->view('pos/menu/index', [
            'pageTitle'  => __('pos.menu_management'),
            'categories' => $categories,
            'menuItems'  => $menuItems
        ]);
    }

    public function store()
    {
        Security::checkCsrf();
        $db = Database::getInstance();

        $categoryId   = (int)($_POST['category_id'] ?? 0);
        $nameTh       = trim($_POST['name_th'] ?? '');
        $nameEn       = trim($_POST['name_en'] ?? '');
        $price        = (float)($_POST['price'] ?? 0);
        $descTh       = trim($_POST['description_th'] ?? '');
        $descEn       = trim($_POST['description_en'] ?? '');
        $imageUrl     = trim($_POST['image_url'] ?? '');
        $isAvailable  = isset($_POST['is_available']) ? 1 : 0;
        $isRecommended= isset($_POST['is_recommended']) ? 1 : 0;

        if (empty($nameTh) || $categoryId <= 0 || $price < 0) {
            Session::setFlash('error', 'กรุณากรอกข้อมูลชื่อเมนู หมวดหมู่ และราคาให้ถูกต้อง');
            $this->redirect('/pos/menu');
        }

        // Handle uploaded image if provided
        if (!empty($_FILES['image_file']['name'])) {
            try {
                $uploadManager = new UploadManager();
                $uploadedPath = $uploadManager->uploadImage($_FILES['image_file'], 'menu');
                if ($uploadedPath) {
                    $imageUrl = $uploadedPath;
                }
            } catch (\Exception $e) {
                // Ignore upload error or fallback to URL
            }
        }

        $stmt = $db->prepare("
            INSERT INTO menu_items (category_id, name_th, name_en, description_th, description_en, price, image_url, is_available, is_recommended)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$categoryId, $nameTh, $nameEn ?: $nameTh, $descTh, $descEn, $price, $imageUrl, $isAvailable, $isRecommended]);
        $itemId = $db->lastInsertId();

        // Save options if supplied
        $optionsRaw = $_POST['options_json'] ?? '';
        if (!empty($optionsRaw)) {
            $options = json_decode($optionsRaw, true);
            if (is_array($options)) {
                $optStmt = $db->prepare("
                    INSERT INTO menu_item_options (menu_item_id, group_name, option_name, extra_price, is_required)
                    VALUES (?, ?, ?, ?, ?)
                ");
                foreach ($options as $opt) {
                    $optStmt->execute([
                        $itemId,
                        $opt['group_name'] ?? 'ตัวเลือก',
                        $opt['option_name'] ?? '',
                        (float)($opt['extra_price'] ?? 0),
                        (int)($opt['is_required'] ?? 0)
                    ]);
                }
            }
        }

        Session::setFlash('success', 'เพิ่มเมนูอาหารเรียบร้อยแล้ว');
        $this->redirect('/pos/menu');
    }

    public function update($id)
    {
        Security::checkCsrf();
        $db = Database::getInstance();

        $categoryId   = (int)($_POST['category_id'] ?? 0);
        $nameTh       = trim($_POST['name_th'] ?? '');
        $nameEn       = trim($_POST['name_en'] ?? '');
        $price        = (float)($_POST['price'] ?? 0);
        $descTh       = trim($_POST['description_th'] ?? '');
        $descEn       = trim($_POST['description_en'] ?? '');
        $imageUrl     = trim($_POST['image_url'] ?? '');
        $isAvailable  = isset($_POST['is_available']) ? 1 : 0;
        $isRecommended= isset($_POST['is_recommended']) ? 1 : 0;

        if (empty($nameTh) || $categoryId <= 0) {
            Session::setFlash('error', 'กรุณากรอกข้อมูลให้ครบถ้วน');
            $this->redirect('/pos/menu');
        }

        if (!empty($_FILES['image_file']['name'])) {
            try {
                $uploadManager = new UploadManager();
                $uploadedPath = $uploadManager->uploadImage($_FILES['image_file'], 'menu');
                if ($uploadedPath) {
                    $imageUrl = $uploadedPath;
                }
            } catch (\Exception $e) {
                // Fallback
            }
        }

        $stmt = $db->prepare("
            UPDATE menu_items 
            SET category_id = ?, name_th = ?, name_en = ?, description_th = ?, description_en = ?, 
                price = ?, image_url = COALESCE(NULLIF(?, ''), image_url), is_available = ?, is_recommended = ?
            WHERE id = ?
        ");
        $stmt->execute([$categoryId, $nameTh, $nameEn ?: $nameTh, $descTh, $descEn, $price, $imageUrl, $isAvailable, $isRecommended, $id]);

        Session::setFlash('success', 'อัปเดตเมนูอาหารสำเร็จ');
        $this->redirect('/pos/menu');
    }

    public function delete($id)
    {
        Security::checkCsrf();
        $db = Database::getInstance();

        $stmt = $db->prepare("DELETE FROM menu_items WHERE id = ?");
        $stmt->execute([$id]);

        Session::setFlash('success', 'ลบเมนูอาหารเรียบร้อยแล้ว');
        $this->redirect('/pos/menu');
    }

    public function toggleStock($id)
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT is_available FROM menu_items WHERE id = ?");
        $stmt->execute([$id]);
        $current = $stmt->fetchColumn();

        if ($current === false) {
            return $this->json(['success' => false, 'message' => 'Item not found'], 404);
        }

        $newVal = $current ? 0 : 1;
        $update = $db->prepare("UPDATE menu_items SET is_available = ? WHERE id = ?");
        $update->execute([$newVal, $id]);

        return $this->json([
            'success'      => true,
            'is_available' => $newVal,
            'message'      => $newVal ? 'เปิดจำหน่ายเมนูแล้ว' : 'ปิดจำหน่าย (ของหมด)'
        ]);
    }

    public function storeCategory()
    {
        Security::checkCsrf();
        $db = Database::getInstance();

        $nameTh = trim($_POST['name_th'] ?? '');
        $nameEn = trim($_POST['name_en'] ?? '');
        $sort   = (int)($_POST['sort_order'] ?? 0);

        if (empty($nameTh)) {
            Session::setFlash('error', 'กรุณาระบุชื่อหมวดหมู่');
            $this->redirect('/pos/menu');
        }

        $stmt = $db->prepare("INSERT INTO menu_categories (name_th, name_en, sort_order) VALUES (?, ?, ?)");
        $stmt->execute([$nameTh, $nameEn ?: $nameTh, $sort]);

        Session::setFlash('success', 'เพิ่มหมวดหมู่สำเร็จ');
        $this->redirect('/pos/menu');
    }

    public function deleteCategory($id)
    {
        Security::checkCsrf();
        $db = Database::getInstance();

        $stmt = $db->prepare("DELETE FROM menu_categories WHERE id = ?");
        $stmt->execute([$id]);

        Session::setFlash('success', 'ลบหมวดหมู่เรียบร้อยแล้ว');
        $this->redirect('/pos/menu');
    }
}
