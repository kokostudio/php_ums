<?php
/**
 * ซิงค์พจนานุกรมภาษาจากไฟล์ (app/Lang) เข้าสู่ตาราง translations ใน MySQL
 *
 * วิธีใช้: php cli/sync_lang.php
 */

$root = dirname(__DIR__);
require $root . '/vendor/autoload.php';

use App\Core\Config;
Config::load($root . '/.env');

$db = \App\Core\Database::getInstance();
$langDir = $root . '/app/Lang';
$locales = ['th', 'en'];

$stmt = $db->prepare("
    INSERT INTO translations (locale, namespace, key_name, text_value)
    VALUES (:locale, :namespace, :key_name, :text_value)
    ON DUPLICATE KEY UPDATE text_value = VALUES(text_value)
");

foreach ($locales as $locale) {
    $localeDir = $langDir . '/' . $locale;
    if (!is_dir($localeDir)) continue;

    $files = glob($localeDir . '/*.php');
    foreach ($files as $file) {
        $namespace = basename($file, '.php');
        $data = require $file;

        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $stmt->execute([
                    ':locale' => $locale,
                    ':namespace' => $namespace,
                    ':key_name' => $key,
                    ':text_value' => $value
                ]);
            }
        }
    }
}
echo "Translations synced to DB.\n";
