<?php
/**
 * ตรวจสอบความสมบูรณ์ของคู่ภาษา TH/EN (Rule 3 / Rule 16)
 * ต้องได้ผลลัพธ์เป็น [] เสมอ — ถ้ามี key ขาดต้องเติมให้ครบก่อนจบงาน
 *
 * วิธีใช้: php cli/compare_lang.php
 */

$root = dirname(__DIR__);
$files = ['auth.php', 'common.php', 'dashboard.php', 'logs.php', 'messages.php', 'profile.php', 'roles.php', 'settings.php', 'users.php'];
$result = [];
foreach ($files as $file) {
    $en = require $root . '/app/Lang/en/' . $file;
    $th = require $root . '/app/Lang/th/' . $file;

    $enKeys = array_keys($en);
    $thKeys = array_keys($th);

    $missingInEn = array_diff($thKeys, $enKeys);
    $missingInTh = array_diff($enKeys, $thKeys);

    if (!empty($missingInEn)) {
        $result[$file]['missing_in_en'] = $missingInEn;
    }
    if (!empty($missingInTh)) {
        $result[$file]['missing_in_th'] = $missingInTh;
    }
}
echo json_encode($result, JSON_PRETTY_PRINT);
