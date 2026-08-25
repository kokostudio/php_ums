<?php
$files = ['auth.php', 'common.php', 'dashboard.php', 'logs.php', 'profile.php', 'roles.php', 'settings.php', 'users.php'];
$result = [];
foreach ($files as $file) {
    $en = require __DIR__ . '/app/Lang/en/' . $file;
    $th = require __DIR__ . '/app/Lang/th/' . $file;
    
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
