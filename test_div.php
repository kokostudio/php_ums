<?php
$html1 = file_get_contents('d:/laragon/www/php_ums/app/Views/layouts/app.php');
echo 'app.php div: ' . (substr_count($html1, '<div') - substr_count($html1, '</div')) . "\n";
$html2 = file_get_contents('d:/laragon/www/php_ums/app/Views/dashboard/index.php');
echo 'dashboard/index.php div: ' . (substr_count($html2, '<div') - substr_count($html2, '</div')) . "\n";
