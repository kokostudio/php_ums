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

        // Dynamic Cards Registry
        $cards = [
            [
                'title' => __('dashboard.total_users'),
                'value' => number_format((float)$stats['total_users']),
                'icon'  => 'ph-users',
                'color' => 'blue', // Will be used as bg-blue-50 text-blue-600
            ],
            [
                'title' => __('dashboard.active_users'),
                'value' => number_format((float)$stats['active_users']),
                'icon'  => 'ph-user-check',
                'color' => 'emerald',
            ],
            [
                'title' => __('dashboard.total_roles'),
                'value' => number_format((float)$stats['total_roles']),
                'icon'  => 'ph-shield-star',
                'color' => 'purple',
            ],
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
        $locale = \App\Core\Lang::locale();
        $monthsTh = ['Jan' => 'ม.ค.', 'Feb' => 'ก.พ.', 'Mar' => 'มี.ค.', 'Apr' => 'เม.ย.', 'May' => 'พ.ค.', 'Jun' => 'มิ.ย.', 'Jul' => 'ก.ค.', 'Aug' => 'ส.ค.', 'Sep' => 'ก.ย.', 'Oct' => 'ต.ค.', 'Nov' => 'พ.ย.', 'Dec' => 'ธ.ค.'];

        foreach ($chartDataRaw as $row) {
            $time = strtotime($row['month'] . "-01");
            $m = date('M', $time);
            $y = date('Y', $time);
            
            if ($locale === 'th') {
                $y = (int)$y + 543;
                $monthName = ($monthsTh[$m] ?? $m) . " " . $y;
            } else {
                $monthName = $m . " " . $y;
            }
            
            $chartLabels[] = $monthName;
            $chartValues[] = (int)$row['count'];
        }

        // Fetch Roles Distribution
        $rolesQuery = "
            SELECT r.name, COUNT(u.id) as count
            FROM roles r
            LEFT JOIN users u ON u.role_id = r.id
            GROUP BY r.id
        ";
        $rolesDataRaw = $db->query($rolesQuery)->fetchAll();
        $roleLabels = [];
        $roleValues = [];
        foreach ($rolesDataRaw as $row) {
            $roleLabels[] = $row['name'];
            $roleValues[] = (int)$row['count'];
        }

        // Dynamic Charts Registry
        $charts = [
            [
                'id' => 'registrationsChart',
                'title' => __('dashboard.user_registrations'),
                'type' => 'line',
                'labels' => $chartLabels,
                'datasets' => [
                    [
                        'label' => __('dashboard.new_users'),
                        'data' => $chartValues,
                        'color' => 'blue',
                    ]
                ]
            ],
            [
                'id' => 'rolesDistributionChart',
                'title' => __('dashboard.roles_distribution'),
                'type' => 'doughnut',
                'labels' => $roleLabels,
                'datasets' => [
                    [
                        'label' => __('dashboard.users'),
                        'data' => $roleValues,
                        'color' => 'multi', // special handling in JS
                    ]
                ]
            ]
        ];

        // Fetch Recent Activities
        $recentActivities = $db->query("
            SELECT a.*, u.username, u.profile_image
            FROM activity_logs a 
            LEFT JOIN users u ON a.user_id = u.id 
            ORDER BY a.created_at DESC 
            LIMIT 5
        ")->fetchAll();

        // System Information
        $systemInfo = [
            'php_version' => PHP_VERSION,
            'os' => php_uname('s') . ' ' . php_uname('r'),
            'db_version' => $db->getAttribute(\PDO::ATTR_SERVER_VERSION),
            'server' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown Server',
            'time' => date('d/m/Y H:i:s')
        ];

        // Ensure we pass the page title to the layout
        $this->view('dashboard/index', [
            'pageTitle' => __('common.dashboard'),
            'cards' => $cards,
            'charts' => $charts,
            'recentActivities' => $recentActivities,
            'systemInfo' => $systemInfo
        ]);
    }
}
