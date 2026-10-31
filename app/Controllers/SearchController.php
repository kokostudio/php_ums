<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\RBAC;
use App\Core\Security;
use App\Core\Session;

class SearchController extends Controller
{
    private function getSystemMenus(): array
    {
        $canViewUsers = RBAC::hasPermission('users', 'view');
        $canCreateUsers = RBAC::hasPermission('users', 'create');
        $canViewRoles = RBAC::hasPermission('roles', 'view');
        $canCreateRoles = RBAC::hasPermission('roles', 'create');

        $menus = [
            [
                'title' => __('common.dashboard') . ' (Dashboard)',
                'description' => 'ภาพรวมระบบ สถิติผู้ใช้งาน และข้อมูลสรุป / System overview & statistics',
                'url' => '/dashboard',
                'icon' => 'ph-squares-four',
                'color' => 'bg-blue-50 text-blue-500 dark:bg-blue-900/20 dark:text-blue-400',
                'keywords' => 'dashboard แดชบอร์ด หน้าหลัก หน้าแรก overview สถิติ summary home'
            ]
        ];

        if ($canViewUsers) {
            $menus[] = [
                'title' => __('common.user_management') . ' (Users)',
                'description' => 'จัดการรายชื่อผู้ใช้ ค้นหา บัญชี และสถานะการใช้งาน / User management',
                'url' => '/users',
                'icon' => 'ph-users',
                'color' => 'bg-sky-50 text-sky-500 dark:bg-sky-900/20 dark:text-sky-400',
                'keywords' => 'users user ผู้ใช้ จัดการผู้ใช้ บัญชี สมาชิก accounts members'
            ];
        }

        if ($canCreateUsers) {
            $menus[] = [
                'title' => (__('users.add_user') ?? 'เพิ่มผู้ใช้ใหม่') . ' (Create User)',
                'description' => 'สร้างบัญชีผู้ใช้งานใหม่ กำหนดบทบาทและรหัสผ่าน / Add new user',
                'url' => '/users/create',
                'icon' => 'ph-user-plus',
                'color' => 'bg-emerald-50 text-emerald-500 dark:bg-emerald-900/20 dark:text-emerald-400',
                'keywords' => 'create user add user เพิ่มผู้ใช้ สร้างผู้ใช้ new user register'
            ];
        }

        if ($canViewRoles) {
            $menus[] = [
                'title' => __('common.roles_permissions') . ' (Roles & Permissions)',
                'description' => 'จัดการกลุ่มสิทธิ์ บทบาทผู้ใช้ และเมทริกซ์สิทธิ์ / Roles & RBAC',
                'url' => '/roles',
                'icon' => 'ph-shield-check',
                'color' => 'bg-purple-50 text-purple-500 dark:bg-purple-900/20 dark:text-purple-400',
                'keywords' => 'roles permissions บทบาท สิทธิ์ rbac access control สิทธิ์การใช้งาน'
            ];
        }

        if ($canCreateRoles) {
            $menus[] = [
                'title' => (__('roles.add_role') ?? 'เพิ่มบทบาทใหม่') . ' (Create Role)',
                'description' => 'สร้างบทบาทใหม่และกำหนดสิทธิ์เข้าถึงโมดูลต่างๆ / Create new role',
                'url' => '/roles/create',
                'icon' => 'ph-shield-plus',
                'color' => 'bg-fuchsia-50 text-fuchsia-500 dark:bg-fuchsia-900/20 dark:text-fuchsia-400',
                'keywords' => 'create role add role เพิ่มบทบาท สร้างบทบาท new role'
            ];
        }

        if (RBAC::hasPermission('logs', 'view')) {
            $menus[] = [
                'title' => __('common.activity_logs') . ' (Activity Logs)',
                'description' => 'ประวัติกิจกรรมการเข้าใช้งานและการกระทำของผู้ใช้ / User activity history',
                'url' => '/logs/activity',
                'icon' => 'ph-activity',
                'color' => 'bg-teal-50 text-teal-500 dark:bg-teal-900/20 dark:text-teal-400',
                'keywords' => 'activity logs บันทึกกิจกรรม ประวัติการใช้งาน history actions login'
            ];

            $menus[] = [
                'title' => __('common.audit_logs') . ' (Audit Logs)',
                'description' => 'ประวัติการเปลี่ยนแปลงข้อมูล ตรวจสอบค่าก่อน-หลังแก้ไข / Data changes audit',
                'url' => '/logs/audit',
                'icon' => 'ph-file-search',
                'color' => 'bg-cyan-50 text-cyan-500 dark:bg-cyan-900/20 dark:text-cyan-400',
                'keywords' => 'audit logs บันทึกการตรวจสอบ ประวัติการแก้ไข diff changes tracking'
            ];
        }

        if (RBAC::hasPermission('settings', 'view')) {
            $menus[] = [
                'title' => __('common.system_settings') . ' (System Settings)',
                'description' => 'การตั้งค่าระบบหลัก ข้อมูลเว็บไซต์ โลโก้ และความปลอดภัย / Core system settings',
                'url' => '/settings',
                'icon' => 'ph-gear',
                'color' => 'bg-amber-50 text-amber-500 dark:bg-amber-900/20 dark:text-amber-400',
                'keywords' => 'settings ตั้งค่าระบบ config configuration ระบบ'
            ];

            $menus[] = [
                'title' => 'ตั้งค่าทั่วไป (General Settings)',
                'description' => 'ชื่อระบบ, โลโก้, Favicon, โหมดปิดปรับปรุง และการเปิดรับสมัครสมาชิก / Site info & maintenance',
                'url' => '/settings/general',
                'icon' => 'ph-sliders',
                'color' => 'bg-orange-50 text-orange-500 dark:bg-orange-900/20 dark:text-orange-400',
                'keywords' => 'general settings ตั้งค่าทั่วไป logo โลโก้ maintenance ปิดปรับปรุง registration'
            ];

            $menus[] = [
                'title' => 'ความปลอดภัย & รหัสผ่าน (Security Settings)',
                'description' => 'นโยบายรหัสผ่าน บังคับใช้ 2FA สำหรับ Admin / Password policy & 2FA',
                'url' => '/settings/security',
                'icon' => 'ph-lock-key',
                'color' => 'bg-rose-50 text-rose-500 dark:bg-rose-900/20 dark:text-rose-400',
                'keywords' => 'security password policy ความปลอดภัย รหัสผ่าน 2fa two factor admin'
            ];

            $menus[] = [
                'title' => 'ดีไซน์หน้าเข้าสู่ระบบ (Login Appearance)',
                'description' => 'ลวดลายพื้นหลัง (Pattern) โลโก้ และข้อความหน้าเข้าสู่ระบบ / Login screen design',
                'url' => '/settings/login',
                'icon' => 'ph-paint-brush',
                'color' => 'bg-pink-50 text-pink-500 dark:bg-pink-900/20 dark:text-pink-400',
                'keywords' => 'login design appearance พื้นหลัง pattern ล็อกอิน หน้าเข้าสู่ระบบ'
            ];

            $menus[] = [
                'title' => 'เข้าสู่ระบบด้วยโซเชียล (Social Login)',
                'description' => 'เปิดใช้งานและตั้งค่า API Keys สำหรับ Google และ LINE Login / Google & LINE OAuth',
                'url' => '/settings/social',
                'icon' => 'ph-share-network',
                'color' => 'bg-lime-50 text-lime-600 dark:bg-lime-900/20 dark:text-lime-400',
                'keywords' => 'social login google line oauth ไลน์ กูเกิล api keys'
            ];

            $menus[] = [
                'title' => 'จัดการคำแปลภาษา (Translations Manager)',
                'description' => 'จัดการข้อความแปลภาษาไทย-อังกฤษในระบบ / Multi-language manager',
                'url' => '/settings/translations',
                'icon' => 'ph-translate',
                'color' => 'bg-yellow-50 text-yellow-600 dark:bg-yellow-900/20 dark:text-yellow-400',
                'keywords' => 'translations language แปลภาษา จัดการภาษา พจนานุกรม i18n locale ภาษาไทย english'
            ];

            $menus[] = [
                'title' => __('settings.design_spec') . ' (Design Specification)',
                'description' => 'เอกสารข้อกำหนดการออกแบบระบบ สถาปัตยกรรม ฐานข้อมูล และมาตรฐาน UI/UX / System architecture & design spec',
                'url' => '/settings/design_spec',
                'icon' => 'ph-file-text',
                'color' => 'bg-indigo-50 text-indigo-500 dark:bg-indigo-900/20 dark:text-indigo-400',
                'keywords' => 'design spec specification ข้อกำหนดการออกแบบ สถาปัตยกรรม architecture database schema ui ux'
            ];
        }

        $menus[] = [
            'title' => (__('profile.title') ?? 'โปรไฟล์ของฉัน') . ' (My Profile)',
            'description' => 'จัดการข้อมูลส่วนตัว รูปโปรไฟล์ เปลี่ยนรหัสผ่าน และเปิดใช้งาน 2FA / Profile & 2FA',
            'url' => '/profile',
            'icon' => 'ph-user-circle',
            'color' => 'bg-violet-50 text-violet-500 dark:bg-violet-900/20 dark:text-violet-400',
            'keywords' => 'profile โปรไฟล์ ข้อมูลส่วนตัว รหัสผ่าน password 2fa account'
        ];

        $menus[] = [
            'title' => (__('profile.active_sessions') ?? 'เซสชันที่เปิดใช้งาน') . ' (Active Sessions)',
            'description' => 'ดูอุปกรณ์และเบราว์เซอร์ที่กำลังใช้งานอยู่ พร้อมยกเลิกเซสชัน / Manage logged in devices',
            'url' => '/profile/sessions',
            'icon' => 'ph-devices',
            'color' => 'bg-indigo-50 text-indigo-500 dark:bg-indigo-900/20 dark:text-indigo-400',
            'keywords' => 'sessions active sessions เซสชัน อุปกรณ์ devices revoke logout browsers'
        ];

        return $menus;
    }

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
                    'menus' => [],
                    'users' => [],
                    'roles' => [],
                    'logs' => [],
                    'translations' => []
                ]
            ]);
        }

        // 0. Search System Menus
        $allMenus = $this->getSystemMenus();
        $matchingMenus = [];
        $lowerQ = mb_strtolower($q, 'UTF-8');

        foreach ($allMenus as $m) {
            $searchableText = mb_strtolower($m['title'] . ' ' . $m['description'] . ' ' . $m['keywords'], 'UTF-8');
            if (mb_strpos($searchableText, $lowerQ) !== false) {
                $matchingMenus[] = [
                    'title' => $m['title'],
                    'description' => $m['description'],
                    'url' => $m['url'],
                    'icon' => $m['icon'],
                    'color' => $m['color']
                ];
            }
        }

        $likeQuery = '%' . $q . '%';
        $db = Database::getInstance();

        // ค้นหาได้เฉพาะหมวดที่ผู้ใช้มีสิทธิ์เข้าถึงเท่านั้น (RBAC) — กันการรั่วข้อมูลข้ามสิทธิ์
        $canSearchUsers         = RBAC::hasPermission('users', 'view');
        $canSearchRoles         = RBAC::hasPermission('roles', 'view');
        $canSearchLogs          = RBAC::hasPermission('logs', 'view');
        $canSearchTranslations  = RBAC::hasPermission('settings', 'view');

        try {
            // 1. Search Users
            $users = [];
            if ($canSearchUsers) {
                $stmt = $db->prepare("
                    SELECT id, username, email, status
                    FROM users
                    WHERE username LIKE :q1 OR email LIKE :q2
                    LIMIT 5
                ");
                $stmt->execute(['q1' => $likeQuery, 'q2' => $likeQuery]);
                $users = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            }

            // 2. Search Roles
            $roles = [];
            if ($canSearchRoles) {
                $stmt = $db->prepare("
                    SELECT id, name, description
                    FROM roles
                    WHERE name LIKE :q1 OR description LIKE :q2
                    LIMIT 5
                ");
                $stmt->execute(['q1' => $likeQuery, 'q2' => $likeQuery]);
                $roles = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            }

            // 3. Search Activity Logs
            $logs = [];
            if ($canSearchLogs) {
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
            }

            // 4. Search Translations
            $translations = [];
            if ($canSearchTranslations) {
                $stmt = $db->prepare("
                    SELECT id, namespace, key_name, text_value
                    FROM translations
                    WHERE text_value LIKE :q1 OR key_name LIKE :q2 OR namespace LIKE :q3
                    LIMIT 5
                ");
                $stmt->execute(['q1' => $likeQuery, 'q2' => $likeQuery, 'q3' => $likeQuery]);
                $translations = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            }

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
                    'menus' => $matchingMenus,
                    'users' => $users,
                    'roles' => $roles,
                    'logs' => $logs,
                    'translations' => $translations
                ]
            ]);

        } catch (\Exception $e) {
            \App\Core\Logger::error('Search failed: ' . $e->getMessage());
            return $this->json([
                'success' => false,
                'message' => __('common.unexpected_error')
            ], 500);
        }
    }
}
