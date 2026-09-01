<?php
require __DIR__ . '/vendor/autoload.php';
use App\Core\Config;
Config::load(__DIR__ . '/.env');

$db = App\Core\Database::getInstance();

$translations = [
    ['dashboard', 'th', 'total_users', 'ผู้ใช้งานทั้งหมด'],
    ['dashboard', 'th', 'active_users', 'ผู้ใช้งานที่ใช้งาน'],
    ['dashboard', 'th', 'total_roles', 'บทบาททั้งหมด'],
    ['dashboard', 'th', 'online_users', 'ออนไลน์ขณะนี้'],
    ['dashboard', 'th', 'add_user', 'เพิ่มผู้ใช้ใหม่'],
    ['dashboard', 'th', 'system_info', 'ข้อมูลระบบ'],
    ['dashboard', 'th', 'recent_activities', 'กิจกรรมล่าสุด'],
    ['dashboard', 'th', 'user_growth', 'การเติบโตของผู้ใช้ (6 เดือนล่าสุด)'],
    ['dashboard', 'th', 'role_dist', 'สัดส่วนบทบาท'],
    ['dashboard', 'th', 'os', 'ระบบปฏิบัติการ'],
    ['dashboard', 'th', 'php_version', 'เวอร์ชัน PHP'],
    ['dashboard', 'th', 'server', 'เซิร์ฟเวอร์'],
    ['dashboard', 'th', 'database', 'ฐานข้อมูล'],
    ['dashboard', 'th', 'db_driver', 'ไดรเวอร์ฐานข้อมูล'],
    ['dashboard', 'th', 'disk_free', 'พื้นที่ว่าง'],
    ['dashboard', 'th', 'memory_limit', 'หน่วยความจำ'],
    ['dashboard', 'th', 'last_updated', 'อัปเดตล่าสุด'],

    ['dashboard', 'en', 'total_users', 'Total Users'],
    ['dashboard', 'en', 'active_users', 'Active Users'],
    ['dashboard', 'en', 'total_roles', 'Total Roles'],
    ['dashboard', 'en', 'online_users', 'Online Now'],
    ['dashboard', 'en', 'add_user', 'Add New User'],
    ['dashboard', 'en', 'system_info', 'System Information'],
    ['dashboard', 'en', 'recent_activities', 'Recent Activities'],
    ['dashboard', 'en', 'user_growth', 'User Growth (Last 6 Months)'],
    ['dashboard', 'en', 'role_dist', 'Role Distribution'],
    ['dashboard', 'en', 'os', 'OS'],
    ['dashboard', 'en', 'php_version', 'PHP Version'],
    ['dashboard', 'en', 'server', 'Server'],
    ['dashboard', 'en', 'database', 'Database'],
    ['dashboard', 'en', 'db_driver', 'DB Driver'],
    ['dashboard', 'en', 'disk_free', 'Disk Free'],
    ['dashboard', 'en', 'memory_limit', 'Memory Limit'],
    ['dashboard', 'en', 'last_updated', 'Last Updated']
];

$stmt = $db->prepare('INSERT INTO translations (namespace, locale, key_name, text_value) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE text_value = VALUES(text_value)');
foreach($translations as $t) {
    $stmt->execute($t);
}
echo 'Translations inserted successfully.';
