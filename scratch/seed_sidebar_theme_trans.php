<?php
require 'vendor/autoload.php';

\App\Core\Config::load('.env');
$db = \App\Core\Database::getInstance();

$trans = [
    'sidebar_theme' => [
        'th' => 'ธีมสีของแถบข้าง',
        'en' => 'Sidebar Color Theme'
    ],
    'sidebar_theme_desc' => [
        'th' => 'ปรับเปลี่ยนเฉดสีสำหรับแผงเมนูด้านซ้ายของระบบควบคุมหลัก',
        'en' => 'Adjust the color theme for the sidebar menu navigation.'
    ]
];

foreach ($trans as $key => $locales) {
    foreach ($locales as $locale => $val) {
        $stmt = $db->prepare("
            INSERT INTO translations (locale, namespace, key_name, text_value) 
            VALUES (?, 'settings', ?, ?) 
            ON DUPLICATE KEY UPDATE text_value = VALUES(text_value)
        ");
        $stmt->execute([$locale, $key, $val]);
    }
}

echo "Sidebar theme translations seeded.\n";
