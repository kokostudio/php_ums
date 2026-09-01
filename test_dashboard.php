<?php
require 'vendor/autoload.php';
App\Core\Config::load(__DIR__ . '/.env');
require 'app/helpers.php';
$systemInfo = [
    'os' => 'Windows',
    'php_version' => '8.2.0',
    'server' => 'Apache',
    'db_driver' => 'MySQL',
    'disk_free' => '100 GB',
    'memory_limit' => '128M'
];
$recentActivities = [];
$stats = [
    'total_users' => 10,
    'total_roles' => 3,
    'online_users' => 1,
    'active_users' => 5
];
$username = 'TestUser';
ob_start();
require 'app/Views/dashboard/index.php';
$content = ob_get_clean();

require 'app/Views/layouts/app.php';
