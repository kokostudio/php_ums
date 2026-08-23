<?php
require 'vendor/autoload.php';

\App\Core\Config::load('.env');
\App\Core\Session::init();
\App\Core\Session::set('user_id', 1);

// Set up mock GET params
$_GET['q'] = 'admin';

// Execute SearchController
$controller = new \App\Controllers\SearchController();
ob_start();
$controller->search();
$response = ob_get_clean();

echo "Response content:\n";
echo $response;
echo "\n";
