<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;

class DashboardController extends Controller
{
    public function index()
    {
        $db = Database::getInstance();
        
        $stats = [
            'total_users' => $db->query("SELECT COUNT(id) FROM users")->fetchColumn(),
            'total_roles' => $db->query("SELECT COUNT(id) FROM roles")->fetchColumn(),
            'active_users' => $db->query("SELECT COUNT(id) FROM users WHERE status = 'active'")->fetchColumn(),
        ];

        // Fetch User Registrations over the last 6 months for Chart.js
        $chartQuery = "
            SELECT 
                DATE_FORMAT(created_at, '%Y-%m') as month, 
                COUNT(id) as count 
            FROM users 
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
            GROUP BY month 
            ORDER BY month ASC
        ";
        $chartDataRaw = $db->query($chartQuery)->fetchAll();
        
        $chartLabels = [];
        $chartValues = [];
        foreach ($chartDataRaw as $row) {
            $monthName = date("M Y", strtotime($row['month'] . "-01"));
            $chartLabels[] = $monthName;
            $chartValues[] = (int)$row['count'];
        }

        // Ensure we pass the page title to the layout
        $this->view('dashboard/index', [
            'pageTitle' => 'Overview Dashboard',
            'stats' => $stats,
            'chartLabels' => json_encode($chartLabels),
            'chartValues' => json_encode($chartValues)
        ]);
    }
}
