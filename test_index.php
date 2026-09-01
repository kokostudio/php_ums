<?php
require __DIR__ . '/vendor/autoload.php';
use App\Core\Config;
Config::load(__DIR__ . '/.env');
require __DIR__ . '/app/helpers.php';
$db = \App\Core\Database::getInstance();
ob_start();
$app = new \App\Core\Router();
require __DIR__ . '/routes/web.php';
try {
    $app->dispatch();
} catch (\Throwable $e) {
    echo "ERROR: " . $e->getMessage() . " at " . $e->getFile() . ":" . $e->getLine();
}
$content = ob_get_clean();
echo $content;
