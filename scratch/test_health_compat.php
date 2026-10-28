<?php

require __DIR__ . '/../vendor/autoload.php';

$r = new \App\Core\Router();

// ทดสอบสไตล์ php_health: group(['prefix' => ..., 'middlewares' => ...], function($r) { ... })
$r->group(['prefix' => '/test-health', 'middlewares' => ['DummyAuth']], function (\App\Core\Router $g) {
    $g->get('/hello', function () {});

    // Nested group array style
    $g->group(['prefix' => '/sub'], function ($sub) {
        $sub->get('/world', function () {});
        $sub->deleteWithFallback('/item/{id}', function () {}, '/items');
    });
});

$routes = $r->routes();
$map = [];
foreach ($routes as $item) {
    $map[$item['method'] . ' ' . $item['path']] = $item;
}

$checks = [
    'GET /test-health/hello' => isset($map['GET /test-health/hello']) && in_array('DummyAuth', $map['GET /test-health/hello']['middlewares'], true),
    'GET /test-health/sub/world' => isset($map['GET /test-health/sub/world']) && in_array('DummyAuth', $map['GET /test-health/sub/world']['middlewares'], true),
    'POST /test-health/sub/item/{id}' => isset($map['POST /test-health/sub/item/{id}']),
    'GET /test-health/sub/item/{id}' => isset($map['GET /test-health/sub/item/{id}']),
];

$allPass = true;
foreach ($checks as $name => $ok) {
    if (!$ok) {
        echo "[FAIL] $name\n";
        $allPass = false;
    } else {
        echo "[PASS] $name\n";
    }
}

if ($allPass) {
    echo "php_health routing style is 100% COMPATIBLE!\n";
}
