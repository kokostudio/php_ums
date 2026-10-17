<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Security;
use App\Core\Session;

class LogController extends Controller
{
    private function getRoleExclusionClause(): string
    {
        return Session::get('role_id') != 1 ? "(u.role_id != 1 OR u.role_id IS NULL)" : "";
    }

    public function activity()
    {
        $db = Database::getInstance();
        $exclusion = $this->getRoleExclusionClause();
        $whereSql = $exclusion ? "WHERE $exclusion" : "";
        
        $query = "
            SELECT a.id, a.action, a.ip_address, a.created_at, u.username
            FROM activity_logs a
            LEFT JOIN users u ON a.user_id = u.id
            $whereSql
            ORDER BY a.id DESC
            LIMIT 1000
        ";
        $logs = $db->query($query)->fetchAll();

        $this->view('logs/activity', [
            'pageTitle' => 'Activity Logs',
            'logs' => $logs
        ]);
    }

    public function activityData()
    {
        $db = Database::getInstance();
        
        $draw = $_GET['draw'] ?? 1;
        $start = $_GET['start'] ?? 0;
        $length = $_GET['length'] ?? 10;
        $searchValue = $_GET['search']['value'] ?? '';

        $params = [];
        $whereConditions = [];
        
        $exclusion = $this->getRoleExclusionClause();
        if ($exclusion) {
            $whereConditions[] = $exclusion;
        }

        if (!empty($searchValue)) {
            $whereConditions[] = "(a.action LIKE :search1 OR u.username LIKE :search2 OR a.ip_address LIKE :search3)";
            $params['search1'] = "%{$searchValue}%";
            $params['search2'] = "%{$searchValue}%";
            $params['search3'] = "%{$searchValue}%";
        }

        $whereClause = !empty($whereConditions) ? "WHERE " . implode(" AND ", $whereConditions) : "";

        // Total records (respecting role exclusion)
        $totalRecordsQuery = "SELECT COUNT(a.id) FROM activity_logs a LEFT JOIN users u ON a.user_id = u.id";
        if ($exclusion) {
            $totalRecordsQuery .= " WHERE $exclusion";
        }
        $totalRecords = $db->query($totalRecordsQuery)->fetchColumn();

        $stmtFiltered = $db->prepare("SELECT COUNT(a.id) FROM activity_logs a LEFT JOIN users u ON a.user_id = u.id {$whereClause}");
        $stmtFiltered->execute($params);
        $totalFiltered = $stmtFiltered->fetchColumn();

        $query = "
            SELECT a.id, a.action, a.ip_address, a.created_at, u.username
            FROM activity_logs a
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

        foreach ($data as &$row) {
            $row['id'] = Security::encodeId((int)$row['id']);
        }

        $this->json([
            "draw" => intval($draw),
            "recordsTotal" => intval($totalRecords),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $data
        ]);
    }

    public function audit()
    {
        $db = Database::getInstance();
        $exclusion = $this->getRoleExclusionClause();
        $whereSql = $exclusion ? "WHERE $exclusion" : "";
        
        $query = "
            SELECT a.id, a.table_name, a.record_id, a.action, a.old_values, a.new_values, a.created_at, u.username
            FROM audit_logs a
            LEFT JOIN users u ON a.user_id = u.id
            $whereSql
            ORDER BY a.id DESC
            LIMIT 1000
        ";
        $logs = $db->query($query)->fetchAll();

        $this->view('logs/audit', [
            'pageTitle' => 'Audit Logs',
            'logs' => $logs
        ]);
    }

    public function auditData()
    {
        $db = Database::getInstance();
        
        $draw = $_GET['draw'] ?? 1;
        $start = $_GET['start'] ?? 0;
        $length = $_GET['length'] ?? 10;
        $searchValue = $_GET['search']['value'] ?? '';

        $params = [];
        $whereConditions = [];
        
        $exclusion = $this->getRoleExclusionClause();
        if ($exclusion) {
            $whereConditions[] = $exclusion;
        }

        if (!empty($searchValue)) {
            $whereConditions[] = "(a.table_name LIKE :search1 OR a.action LIKE :search2 OR u.username LIKE :search3)";
            $params['search1'] = "%{$searchValue}%";
            $params['search2'] = "%{$searchValue}%";
            $params['search3'] = "%{$searchValue}%";
        }

        $whereClause = !empty($whereConditions) ? "WHERE " . implode(" AND ", $whereConditions) : "";

        // Total records (respecting role exclusion)
        $totalRecordsQuery = "SELECT COUNT(a.id) FROM audit_logs a LEFT JOIN users u ON a.user_id = u.id";
        if ($exclusion) {
            $totalRecordsQuery .= " WHERE $exclusion";
        }
        $totalRecords = $db->query($totalRecordsQuery)->fetchColumn();

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

        foreach ($data as &$row) {
            $row['id'] = Security::encodeId((int)$row['id']);
            $row['record_id'] = Security::encodeId((int)$row['record_id']);
        }

        $this->json([
            "draw" => intval($draw),
            "recordsTotal" => intval($totalRecords),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $data
        ]);
    }
}
