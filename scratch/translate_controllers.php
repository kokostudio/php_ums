<?php
require 'vendor/autoload.php';

$replacements = [
    "'Settings saved successfully.'" => "__('messages.settings_saved')",
    "'User created successfully.'" => "__('messages.user_created')",
    "'User updated successfully.'" => "__('messages.user_updated')",
    "'Profile updated successfully.'" => "__('messages.profile_updated')",
    "'Role created successfully.'" => "__('messages.role_created')",
    "'Role updated successfully.'" => "__('messages.role_updated')",
    "'Translation updated successfully.'" => "__('messages.translation_updated')",
    "'You have been logged out.'" => "__('messages.logged_out')",
    "'Password updated successfully. Next time you login, please use your new password.'" => "__('messages.password_updated')",
    "'Two-Factor Authentication has been enabled successfully.'" => "__('messages.2fa_enabled')",
    "'Two-Factor Authentication has been disabled.'" => "__('messages.2fa_disabled')",
    "'Session revoked successfully.'" => "__('messages.session_revoked')"
];

$translations = [
    'settings_saved' => ['th' => 'บันทึกการตั้งค่าเรียบร้อยแล้ว', 'en' => 'Settings saved successfully.'],
    'user_created' => ['th' => 'สร้างผู้ใช้เรียบร้อยแล้ว', 'en' => 'User created successfully.'],
    'user_updated' => ['th' => 'อัปเดตผู้ใช้เรียบร้อยแล้ว', 'en' => 'User updated successfully.'],
    'profile_updated' => ['th' => 'อัปเดตโปรไฟล์เรียบร้อยแล้ว', 'en' => 'Profile updated successfully.'],
    'role_created' => ['th' => 'สร้างบทบาทเรียบร้อยแล้ว', 'en' => 'Role created successfully.'],
    'role_updated' => ['th' => 'อัปเดตบทบาทเรียบร้อยแล้ว', 'en' => 'Role updated successfully.'],
    'translation_updated' => ['th' => 'อัปเดตคำแปลเรียบร้อยแล้ว', 'en' => 'Translation updated successfully.'],
    'logged_out' => ['th' => 'คุณได้ออกจากระบบแล้ว', 'en' => 'You have been logged out.'],
    'password_updated' => ['th' => 'อัปเดตรหัสผ่านเรียบร้อยแล้ว กรุณาใช้รหัสผ่านใหม่ในครั้งถัดไป', 'en' => 'Password updated successfully. Next time you login, please use your new password.'],
    '2fa_enabled' => ['th' => 'เปิดใช้งานยืนยันตัวตนสองชั้นเรียบร้อยแล้ว', 'en' => 'Two-Factor Authentication has been enabled successfully.'],
    '2fa_disabled' => ['th' => 'ปิดใช้งานยืนยันตัวตนสองชั้นเรียบร้อยแล้ว', 'en' => 'Two-Factor Authentication has been disabled.'],
    'session_revoked' => ['th' => 'เพิกถอนเซสชันเรียบร้อยแล้ว', 'en' => 'Session revoked successfully.']
];

$dir = 'app/Controllers/';
$files = glob($dir . '*.php');

foreach ($files as $file) {
    $content = file_get_contents($file);
    $newContent = str_replace(array_keys($replacements), array_values($replacements), $content);
    if ($content !== $newContent) {
        file_put_contents($file, $newContent);
        echo "Updated $file\n";
    }
}

\App\Core\Config::load('.env');
$db = \App\Core\Database::getInstance();

foreach ($translations as $key => $values) {
    foreach (['th', 'en'] as $locale) {
        $stmt = $db->prepare("INSERT INTO translations (locale, namespace, key_name, text_value) VALUES (?, 'messages', ?, ?) ON DUPLICATE KEY UPDATE text_value = VALUES(text_value)");
        $stmt->execute([$locale, $key, $values[$locale]]);
    }
}
echo "Database translations seeded.\n";
