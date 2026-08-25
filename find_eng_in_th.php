<?php
$files = ['auth.php', 'common.php', 'dashboard.php', 'logs.php', 'profile.php', 'roles.php', 'settings.php', 'users.php'];
$english = [];
foreach ($files as $file) {
    $th = require __DIR__ . '/app/Lang/th/' . $file;
    foreach ($th as $key => $val) {
        if (preg_match('/[a-zA-Z]/', $val) && !preg_match('/[\x{0E00}-\x{0E7F}]/u', $val)) {
            $english[$file][$key] = $val;
        }
    }
}
echo json_encode($english, JSON_PRETTY_PRINT);
