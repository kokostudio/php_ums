<?php
require 'vendor/autoload.php';

$replacements = [
    "'Invalid username or password.'" => "__('messages.invalid_credentials')",
    "'Passwords do not match.'" => "__('messages.passwords_not_match')",
    "'Username or Email already exists.'" => "__('messages.user_exists')",
    "'All fields are required.'" => "__('messages.all_fields_required')",
    "'Failed to update settings: '" => "__('messages.failed_update_settings') . ' '",
    "'Failed to create user. Please try again.'" => "__('messages.failed_create_user')",
    "'User not found.'" => "__('messages.user_not_found')",
    "'Role name is required.'" => "__('messages.role_name_required')",
    "'Role name already exists.'" => "__('messages.role_exists')",
    "'Failed to update profile.'" => "__('messages.failed_update_profile')",
    "'Current password is incorrect.'" => "__('messages.current_password_incorrect')",
    "'Invalid or expired password reset token.'" => "__('messages.invalid_reset_token')",
    "'Please enter username and password.'" => "__('messages.enter_user_pass')",
    "'Your account is disabled.'" => "__('messages.account_disabled')"
];

$translations = [
    'invalid_credentials' => ['th' => 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง', 'en' => 'Invalid username or password.'],
    'passwords_not_match' => ['th' => 'รหัสผ่านไม่ตรงกัน', 'en' => 'Passwords do not match.'],
    'user_exists' => ['th' => 'ชื่อผู้ใช้หรืออีเมลนี้มีในระบบแล้ว', 'en' => 'Username or Email already exists.'],
    'all_fields_required' => ['th' => 'กรุณากรอกข้อมูลให้ครบทุกช่อง', 'en' => 'All fields are required.'],
    'failed_update_settings' => ['th' => 'ไม่สามารถบันทึกการตั้งค่าได้:', 'en' => 'Failed to update settings:'],
    'failed_create_user' => ['th' => 'ไม่สามารถสร้างผู้ใช้ได้ กรุณาลองใหม่', 'en' => 'Failed to create user. Please try again.'],
    'user_not_found' => ['th' => 'ไม่พบผู้ใช้นี้ในระบบ', 'en' => 'User not found.'],
    'role_name_required' => ['th' => 'กรุณาระบุชื่อบทบาท', 'en' => 'Role name is required.'],
    'role_exists' => ['th' => 'ชื่อบทบาทนี้มีอยู่แล้ว', 'en' => 'Role name already exists.'],
    'failed_update_profile' => ['th' => 'ไม่สามารถอัปเดตโปรไฟล์ได้', 'en' => 'Failed to update profile.'],
    'current_password_incorrect' => ['th' => 'รหัสผ่านปัจจุบันไม่ถูกต้อง', 'en' => 'Current password is incorrect.'],
    'invalid_reset_token' => ['th' => 'ลิงก์รีเซ็ตรหัสผ่านไม่ถูกต้องหรือหมดอายุแล้ว', 'en' => 'Invalid or expired password reset token.'],
    'enter_user_pass' => ['th' => 'กรุณากรอกชื่อผู้ใช้และรหัสผ่าน', 'en' => 'Please enter username and password.'],
    'account_disabled' => ['th' => 'บัญชีของคุณถูกระงับการใช้งาน', 'en' => 'Your account is disabled.']
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
echo "Database error translations seeded.\n";
