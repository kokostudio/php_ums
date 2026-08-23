<?php
require 'vendor/autoload.php';

\App\Core\Config::load('.env');

$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/api/notifications';
require 'public/index.php';
