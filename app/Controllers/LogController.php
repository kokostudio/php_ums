<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\DataTableHelper;
use App\Core\Database;
use App\Core\Logger;
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

    /**
     * Activity Logs — Server-side Processing ผ่าน DataTableHelper (Rule 14)
     */
    public function activityData()
    {
        try {
            $db = Database::getInstance();
            $exclusion = $this->getRoleExclusionClause();

            $result = DataTableHelper::process(
                $db,
                $_GET,
                "SELECT a.id, a.action, a.ip_address, a.created_at, u.username
                 FROM activity_logs a
                 LEFT JOIN users u ON a.user_id = u.id",
                ['a.action', 'u.username', 'a.ip_address'],
                [0 => 'a.id', 1 => 'a.action', 2 => 'a.ip_address', 3 => 'a.created_at', 4 => 'u.username'],
                'a.id',
                'DESC',
                $exclusion ? [$exclusion] : [],
                [],
                $exclusion
                    ? "SELECT COUNT(a.id) FROM activity_logs a LEFT JOIN users u ON a.user_id = u.id WHERE {$exclusion}"
                    : null
            );

            foreach ($result['data'] as &$row) {
                $row['id'] = Security::encodeId((int) $row['id']);
            }

            $this->json($result);
        } catch (\Throwable $e) {
            Logger::error('Activity logs data failed: ' . $e->getMessage());
            $this->json([
                'draw' => intval($_GET['draw'] ?? 1),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => __('common.unexpected_error'),
            ], 500);
        }
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

    /**
     * Audit Logs — Server-side Processing ผ่าน DataTableHelper (Rule 14)
     */
    public function auditData()
    {
        try {
            $db = Database::getInstance();
            $exclusion = $this->getRoleExclusionClause();

            $result = DataTableHelper::process(
                $db,
                $_GET,
                "SELECT a.id, a.table_name, a.record_id, a.action, a.old_values, a.new_values, a.created_at, u.username
                 FROM audit_logs a
                 LEFT JOIN users u ON a.user_id = u.id",
                ['a.table_name', 'a.action', 'u.username'],
                [0 => 'a.id', 1 => 'a.table_name', 2 => 'a.record_id', 3 => 'a.action', 4 => 'a.created_at', 5 => 'u.username'],
                'a.id',
                'DESC',
                $exclusion ? [$exclusion] : [],
                [],
                $exclusion
                    ? "SELECT COUNT(a.id) FROM audit_logs a LEFT JOIN users u ON a.user_id = u.id WHERE {$exclusion}"
                    : null
            );

            foreach ($result['data'] as &$row) {
                $row['id'] = Security::encodeId((int) $row['id']);
                $row['record_id'] = Security::encodeId((int) $row['record_id']);
            }

            $this->json($result);
        } catch (\Throwable $e) {
            Logger::error('Audit logs data failed: ' . $e->getMessage());
            $this->json([
                'draw' => intval($_GET['draw'] ?? 1),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => __('common.unexpected_error'),
            ], 500);
        }
    }
}
