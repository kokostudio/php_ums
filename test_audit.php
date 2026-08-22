<?php
require 'vendor/autoload.php';
require 'app/Core/Config.php';
App\Core\Config::load(__DIR__ . '/.env');

require 'app/Core/Database.php';

try {
    $db = App\Core\Database::getInstance();
    
    $start = 0;
    $length = 10;
    $searchValue = '';

    $params = [];
    $whereClause = "";

    if (!empty($searchValue)) {
        $whereClause = "WHERE a.table_name LIKE :search OR a.action LIKE :search OR u.username LIKE :search";
        $params['search'] = "%{$searchValue}%";
    }

    $totalRecords = $db->query("SELECT COUNT(id) FROM audit_logs")->fetchColumn();

    $stmtFiltered = $db->prepare("SELECT COUNT(a.id) FROM audit_logs a LEFT JOIN users u ON a.user_id = u.id {$whereClause}");
    $stmtFiltered->execute($params);
    $totalFiltered = $stmtFiltered->fetchColumn();

    $query = "
        SELECT a.id, a.table_name, a.record_id, a.action, a.old_values, a.new_values, a.created_at, u.username
        FROM audit_logs a
        LEFT JOIN users u ON a.user_id = u.id
        {$whereClause}
        ORDER BY a.id DESC
        LIMIT :start, :length
    ";
    
    $stmt = $db->prepare($query);
    foreach ($params as $key => $value) {
        $stmt->bindValue(":$key", $value);
    }
    $stmt->bindValue(':start', (int)$start, \PDO::PARAM_INT);
    $stmt->bindValue(':length', (int)$length, \PDO::PARAM_INT);
    $stmt->execute();
    
    $data = $stmt->fetchAll();

    echo json_encode([
        "draw" => 1,
        "recordsTotal" => intval($totalRecords),
        "recordsFiltered" => intval($totalFiltered),
        "data" => $data
    ]);
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
