<?php
require 'vendor/autoload.php';

\App\Core\Config::load('.env');
$db = \App\Core\Database::getInstance();
\App\Core\Session::start();
\App\Core\Session::set('user_id', 1); // Simulate logged in

$controller = new \App\Controllers\NotificationController();
$controller->index();
