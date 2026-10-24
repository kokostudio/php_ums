<?php
require __DIR__ . '/../vendor/autoload.php';

use App\Core\Config;
use App\Core\Database;

Config::load(__DIR__ . '/../.env');
$db = Database::getInstance();

$stmt = $db->query("SELECT setting_key, setting_value FROM system_settings ORDER BY setting_key");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($rows as $row) {
    echo str_pad($row['setting_key'], 30) . ' = ' . $row['setting_value'] . PHP_EOL;
}
