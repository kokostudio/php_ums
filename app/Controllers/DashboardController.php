<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;

class DashboardController extends Controller
{
    public function index()
    {
        $db = Database::getInstance();
        
        // Fetch some basic stats for the dashboard
        $stats = [
            'total_users' => $db->query("SELECT COUNT(id) FROM users")->fetchColumn(),
            'total_roles' => $db->query("SELECT COUNT(id) FROM roles")->fetchColumn(),
            'active_users' => $db->query("SELECT COUNT(id) FROM users WHERE status = 'active'")->fetchColumn(),
        ];

        // Ensure we pass the page title to the layout
        $this->view('dashboard/index', [
            'pageTitle' => 'Overview Dashboard',
            'stats' => $stats
        ]);
    }
}
