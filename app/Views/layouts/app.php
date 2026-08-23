<?php
use App\Core\Settings;
use App\Core\Session;

$sidebarTheme = Settings::get('sidebar_theme', 'slate');
$themes = [
    'slate' => [
        'bg' => 'bg-slate-900',
        'border' => 'border-slate-800',
        'footer' => 'bg-slate-950'
    ],
    'black' => [
        'bg' => 'bg-neutral-950',
        'border' => 'border-neutral-900',
        'footer' => 'bg-black/40'
    ],
    'indigo' => [
        'bg' => 'bg-indigo-950',
        'border' => 'border-indigo-900',
        'footer' => 'bg-indigo-950/60'
    ],
    'emerald' => [
        'bg' => 'bg-emerald-950',
        'border' => 'border-emerald-900',
        'footer' => 'bg-emerald-950/60'
    ],
    'rose' => [
        'bg' => 'bg-rose-950',
        'border' => 'border-rose-900',
        'footer' => 'bg-rose-950/60'
    ]
];
$theme = $themes[$sidebarTheme] ?? $themes['slate'];
?>
<!DOCTYPE html>
<html lang="<?= \App\Core\Lang::locale() ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars(Settings::get('site_name', 'System')) ?></title>
    
    <?php $favicon = Settings::get('site_favicon'); if ($favicon): ?>
        <link rel="icon" href="<?= htmlspecialchars($favicon) ?>">
    <?php endif; ?>
    <!-- Dark Mode Init Script (Prevents FOUC) -->
    <script>
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark')
        } else {
            document.documentElement.classList.remove('dark')
        }
    </script>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: '#3b82f6',
                        secondary: '#64748b',
                    },
                    fontFamily: {
                        sans: ['Prompt', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Phosphor Icons -->
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <!-- Animate.css for smooth Toast animations -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Axios -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <!-- Cropper.js -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
    <script>
        /**
         * Global standard date formatter for frontend / DataTables
         * Returns DD/MM/YYYY (e.g. 30/12/2026) or DD/MM/YYYY HH:mm(:ss)
         */
        function formatDate(dateStr, withTime = false, withSeconds = false) {
            if (!dateStr || dateStr === '-') return '-';
            const parts = String(dateStr).trim().split(/[- :T]/);
            if (parts.length >= 3) {
                const year = parts[0];
                const month = parts[1];
                const day = parts[2].substring(0, 2);
                let result = `${day}/${month}/${year}`;
                if (withTime && parts.length >= 5) {
                    const hour = parts[3] || '00';
                    const min = parts[4] || '00';
                    result += ` ${hour}:${min}`;
                    if (withSeconds && parts.length >= 6) {
                        const sec = parts[5].substring(0, 2) || '00';
                        result += `:${sec}`;
                    }
                }
                return result;
            }
            return dateStr;
        }
    </script>

    <style>
        body { font-family: 'Prompt', sans-serif; }
        .sidebar-item { transition: all 0.2s ease-in-out; }
        .sidebar-item:hover { background-color: rgba(255, 255, 255, 0.1); }
        .sidebar-item.active { background-color: rgba(255, 255, 255, 0.2); border-left: 4px solid white; }

        /* ── Custom Scrollbar ── */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: transparent;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;        /* slate-300 – light mode */
            border-radius: 9999px;
            transition: background 0.2s;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #3b82f6;        /* blue-500 – brand accent */
        }
        ::-webkit-scrollbar-corner {
            background: transparent;
        }

        /* Dark mode scrollbar */
        .dark ::-webkit-scrollbar-thumb {
            background: #334155;        /* slate-700 */
        }
        .dark ::-webkit-scrollbar-thumb:hover {
            background: #3b82f6;        /* blue-500 – same accent */
        }

        /* Firefox scrollbar */
        * {
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 transparent;
        }
        .dark * {
            scrollbar-color: #334155 transparent;
        }

        /* ── Premium SweetAlert2 Styling ── */
        .swal2-popup:not(.swal2-toast) {
            border-radius: 1rem !important;
            padding: 2rem !important;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04) !important;
            font-family: 'Prompt', sans-serif !important;
        }
        .swal2-popup:not(.swal2-toast) .swal2-title { font-weight: 600 !important; font-size: 1.5rem !important; }
        .swal2-popup:not(.swal2-toast) .swal2-confirm { border-radius: 0.5rem !important; font-weight: 500 !important; padding: 0.625rem 1.5rem !important; }
        .swal2-popup:not(.swal2-toast) .swal2-cancel { border-radius: 0.5rem !important; font-weight: 500 !important; padding: 0.625rem 1.5rem !important; }

        /* Dark mode overrides for SweetAlert2 */
        .dark .swal2-popup {
            background-color: #1e293b !important;
            color: #f8fafc !important;
            border: 1px solid #334155 !important;
        }
        .dark .swal2-title, .dark .swal2-html-container {
            color: #f8fafc !important;
        }
        .dark .swal2-icon.swal2-warning { border-color: #eab308 !important; color: #eab308 !important; }
        .dark .swal2-icon.swal2-error { border-color: #ef4444 !important; color: #ef4444 !important; }
        .dark .swal2-icon.swal2-success { border-color: #22c55e !important; color: #22c55e !important; }
        .dark .swal2-icon.swal2-info { border-color: #3b82f6 !important; color: #3b82f6 !important; }

        /* ── Premium DataTables Styling ── */
        .dataTables_wrapper {
            padding: 0.5rem 0;
        }
        .dataTables_wrapper .dataTables_length select,
        .dataTables_wrapper .dataTables_filter input {
            border-radius: 0.5rem; 
            border: 1px solid #e2e8f0; 
            padding: 0.4rem 1rem; 
            outline: none;
            transition: all 0.2s ease;
            font-size: 0.875rem;
            background-color: white;
            color: #1e293b;
        }
        .dataTables_wrapper .dataTables_filter input {
            margin-left: 0.5rem;
            min-width: 200px;
        }
        .dataTables_wrapper .dataTables_length select:focus,
        .dataTables_wrapper .dataTables_filter input:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        
        table.dataTable.no-footer { border-bottom: none; }
        table.dataTable thead th {
            border-bottom: 2px solid #e2e8f0;
            color: #64748b;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
            padding: 1rem;
        }
        table.dataTable tbody th, table.dataTable tbody td { 
            border-bottom: 1px solid #f1f5f9; 
            padding: 1rem; 
            vertical-align: middle;
        }
        table.dataTable tbody tr {
            transition: all 0.2s ease;
        }
        table.dataTable tbody tr:hover {
            background-color: #f8fafc;
        }
        
        /* Pagination */
        .dataTables_wrapper .dataTables_paginate {
            padding-top: 1rem;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button { 
            border-radius: 0.5rem !important; 
            padding: 0.4rem 0.8rem; 
            margin-left: 0.25rem;
            border: 1px solid transparent !important;
            font-weight: 500;
            font-size: 0.875rem;
            transition: all 0.2s ease;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: #f1f5f9 !important;
            color: #0f172a !important;
            border-color: #e2e8f0 !important;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button.current,
        .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover { 
            background: #3b82f6 !important; 
            color: white !important; 
            border-color: #3b82f6 !important;
            box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.2);
        }
        .dataTables_wrapper .dataTables_info {
            padding-top: 1.2rem;
            font-size: 0.875rem;
            color: #64748b;
        }

        /* Dark mode overrides for DataTables */
        .dark .dataTables_wrapper .dataTables_length select,
        .dark .dataTables_wrapper .dataTables_filter input { 
            border-color: #334155; 
            background-color: #1e293b; 
            color: #f1f5f9; 
        }
        .dark .dataTables_wrapper .dataTables_length select:focus,
        .dark .dataTables_wrapper .dataTables_filter input:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
        }
        .dark table.dataTable thead th { border-bottom-color: #334155; color: #94a3b8; }
        .dark table.dataTable tbody th, .dark table.dataTable tbody td { border-bottom-color: #1e293b; }
        .dark table.dataTable tbody tr:hover { background-color: #0f172a; }
        .dark .dataTables_wrapper .dataTables_paginate .paginate_button { color: #cbd5e1 !important; }
        .dark .dataTables_wrapper .dataTables_paginate .paginate_button:hover { 
            background: #334155 !important; 
            border-color: #475569 !important; 
            color: white !important; 
        }
        .dark .dataTables_wrapper .dataTables_paginate .paginate_button.disabled { color: #475569 !important; }
        .dark .dataTables_wrapper .dataTables_info { color: #94a3b8 !important; }
        .dark .dataTables_wrapper .dataTables_length, .dark .dataTables_wrapper .dataTables_filter { color: #cbd5e1 !important; }

        /* Custom Skeleton Loading for DataTables */
        div.dataTables_wrapper div.dataTables_processing {
            position: absolute;
            top: 50%;
            left: 50%;
            width: 100%;
            height: 100%;
            margin-left: -50%;
            margin-top: -50px;
            text-align: center;
            padding: 2rem;
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(4px);
            z-index: 10;
            border: none;
            box-shadow: none;
        }
        .dark div.dataTables_wrapper div.dataTables_processing {
            background: rgba(15, 23, 42, 0.8);
        }
    </style>

    <!-- jQuery & DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
    <script>
        // Global DataTables Defaults (Premium UI + i18n + Skeleton Loader)
        $.extend(true, $.fn.dataTable.defaults, {
            dom: '<"top"f>rt<"flex flex-col md:flex-row justify-between items-center mt-4"lip><"clear">',
            language: {
                <?= \App\Core\Lang::locale() === 'th' ? '
                "emptyTable": "ไม่มีข้อมูลในตาราง",
                "info": "แสดง _START_ ถึง _END_ จาก _TOTAL_ รายการ",
                "infoEmpty": "แสดง 0 ถึง 0 จาก 0 รายการ",
                "infoFiltered": "(กรองข้อมูล _MAX_ ทุกรายการ)",
                "lengthMenu": "แสดง _MENU_ รายการ",
                "loadingRecords": "กำลังโหลดข้อมูล...",
                "processing": "<div class=\'animate-pulse space-y-4\'><div class=\'h-8 bg-slate-200 dark:bg-slate-700 rounded w-full\'></div><div class=\'h-8 bg-slate-200 dark:bg-slate-700 rounded w-full\'></div><div class=\'h-8 bg-slate-200 dark:bg-slate-700 rounded w-full\'></div></div>",
                "search": "ค้นหา:",
                "zeroRecords": "ไม่พบข้อมูล",
                "paginate": {
                    "first": "หน้าแรก",
                    "last": "หน้าสุดท้าย",
                    "next": "ถัดไป",
                    "previous": "ก่อนหน้า"
                }
                ' : '
                "search": "Search:",
                "lengthMenu": "Show _MENU_ entries",
                "processing": "<div class=\'animate-pulse space-y-4\'><div class=\'h-8 bg-slate-200 dark:bg-slate-700 rounded w-full\'></div><div class=\'h-8 bg-slate-200 dark:bg-slate-700 rounded w-full\'></div><div class=\'h-8 bg-slate-200 dark:bg-slate-700 rounded w-full\'></div></div>"
                ' ?>
            }
        });
    </script>
</head>
<body class="text-gray-800 dark:text-slate-200 antialiased h-screen flex overflow-hidden bg-slate-50 dark:bg-slate-950 transition-colors duration-300">

    <!-- Mobile Sidebar Overlay -->
    <div id="sidebarOverlay" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-40 hidden md:hidden transition-opacity opacity-0"></div>

    <!-- Sidebar -->
    <aside id="sidebar" class="w-64 <?= $theme['bg'] ?> text-white flex-col fixed inset-y-0 left-0 transform -translate-x-full md:relative md:translate-x-0 z-50 transition-transform duration-300 shadow-xl flex">
        <div class="h-16 flex items-center px-6 border-b <?= $theme['border'] ?> font-bold text-lg tracking-wider">
            <?php $logo = Settings::get('site_logo'); if ($logo): ?>
                <img src="<?= htmlspecialchars($logo) ?>" alt="Logo" class="h-8 w-auto mr-3">
            <?php else: ?>
                <i class="ph-fill ph-cube text-blue-400 text-2xl mr-2"></i>
            <?php endif; ?>
            <span class="truncate"><?= htmlspecialchars(Settings::get('site_name', 'CORE SYSTEM')) ?></span>
        </div>
        
        <div class="flex-1 overflow-y-auto py-6">
            <nav class="space-y-1 px-3">
                <!-- Group: System Management -->
                <?php 
                $systemRoutes = ['/dashboard', '/users', '/roles', '/logs/activity', '/logs/audit', '/settings'];
                $isSystemActive = false;
                $currentUri = $_SERVER['REQUEST_URI'] ?? '';
                foreach ($systemRoutes as $route) {
                    if (strpos($currentUri, $route) === 0) {
                        $isSystemActive = true;
                        break;
                    }
                }
                ?>
                <div class="sidebar-group">
                    <button type="button" class="sidebar-item w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium <?= $isSystemActive ? 'text-white bg-white/10' : 'text-slate-300' ?>" onclick="$(this).next('.submenu').slideToggle(200); $(this).find('.caret-icon').toggleClass('rotate-90');">
                        <div class="flex items-center">
                            <i class="ph ph-desktop text-lg mr-3"></i> 
                            <span><?= __('common.administration') ?></span>
                        </div>
                        <i class="ph ph-caret-right text-xs caret-icon transition-transform duration-200 <?= $isSystemActive ? 'rotate-90' : '' ?>"></i>
                    </button>
                    <div class="submenu overflow-hidden <?= $isSystemActive ? '' : 'hidden' ?> mt-1 space-y-1 pl-4 border-l-2 border-slate-700/50 ml-5">
                        <a href="/dashboard" class="flex items-center px-3 py-2 text-sm font-medium transition-colors <?= strpos($currentUri, '/dashboard') === 0 ? 'text-white border-l-[3px] border-white' : 'text-slate-400 hover:text-slate-200 hover:bg-white/5 border-l-[3px] border-transparent' ?>">
                            <i class="ph ph-squares-four text-lg mr-3 opacity-70"></i> <?= __('common.dashboard') ?>
                        </a>
                        <a href="/users" class="flex items-center px-3 py-2 text-sm font-medium transition-colors <?= strpos($currentUri, '/users') === 0 ? 'text-white border-l-[3px] border-white' : 'text-slate-400 hover:text-slate-200 hover:bg-white/5 border-l-[3px] border-transparent' ?>">
                            <i class="ph ph-users text-lg mr-3 opacity-70"></i> <?= __('common.user_management') ?>
                        </a>
                        <a href="/roles" class="flex items-center px-3 py-2 text-sm font-medium transition-colors <?= strpos($currentUri, '/roles') === 0 ? 'text-white border-l-[3px] border-white' : 'text-slate-400 hover:text-slate-200 hover:bg-white/5 border-l-[3px] border-transparent' ?>">
                            <i class="ph ph-shield-check text-lg mr-3 opacity-70"></i> <?= __('common.roles_permissions') ?>
                        </a>
                        <a href="/logs/activity" class="flex items-center px-3 py-2 text-sm font-medium transition-colors <?= strpos($currentUri, '/logs/activity') === 0 ? 'text-white border-l-[3px] border-white' : 'text-slate-400 hover:text-slate-200 hover:bg-white/5 border-l-[3px] border-transparent' ?>">
                            <i class="ph ph-activity text-lg mr-3 opacity-70"></i> <?= __('common.activity_logs') ?>
                        </a>
                        <a href="/logs/audit" class="flex items-center px-3 py-2 text-sm font-medium transition-colors <?= strpos($currentUri, '/logs/audit') === 0 ? 'text-white border-l-[3px] border-white' : 'text-slate-400 hover:text-slate-200 hover:bg-white/5 border-l-[3px] border-transparent' ?>">
                            <i class="ph ph-file-search text-lg mr-3 opacity-70"></i> <?= __('common.audit_logs') ?>
                        </a>
                        <a href="/settings" class="flex items-center px-3 py-2 text-sm font-medium transition-colors <?= strpos($currentUri, '/settings') === 0 ? 'text-white border-l-[3px] border-white' : 'text-slate-400 hover:text-slate-200 hover:bg-white/5 border-l-[3px] border-transparent' ?>">
                            <i class="ph ph-gear text-lg mr-3 opacity-70"></i> <?= __('common.system_settings') ?>
                        </a>
                    </div>
                </div>
            </nav>
        </div>
        
        <div class="p-4 border-t <?= $theme['border'] ?> <?= $theme['footer'] ?> relative group cursor-pointer">
            <a href="/profile" class="flex items-center hover:opacity-80 transition-opacity">
                <?php $profileImg = Session::get('profile_image'); if ($profileImg): ?>
                    <img src="<?= htmlspecialchars($profileImg) ?>" alt="Profile" class="w-9 h-9 rounded-full object-cover shadow-inner border border-slate-700">
                <?php else: ?>
                    <div class="w-9 h-9 rounded-full bg-gradient-to-tr from-blue-500 to-purple-500 flex items-center justify-center font-bold text-sm shadow-inner text-white">
                        <?= htmlspecialchars(strtoupper(substr(Session::get('username', 'U'), 0, 1))) ?>
                    </div>
                <?php endif; ?>
                <div class="ml-3 overflow-hidden flex-1 text-white">
                    <p class="text-sm font-medium truncate"><?= htmlspecialchars(Session::get('username', 'Guest')) ?></p>
                    <p class="text-xs text-slate-400 truncate"><?= __('common.view_profile') ?></p>
                </div>
                <i class="ph ph-caret-right text-slate-500 text-sm"></i>
            </a>
        </div>
    </aside>

    <!-- Main Content Wrapper -->
    <div class="flex-1 flex flex-col overflow-hidden relative">
        <!-- Topbar -->
        <header class="h-16 bg-white/80 dark:bg-slate-900/80 backdrop-blur-md border-b border-gray-200 dark:border-slate-800 flex items-center justify-between px-6 z-10 sticky top-0 transition-colors duration-300">
            <div class="flex items-center space-x-3">
                <button id="sidebarToggle" class="md:hidden text-gray-500 dark:text-slate-400 hover:text-gray-700 dark:hover:text-slate-200 transition-colors mr-1">
                    <i class="ph ph-list text-2xl"></i>
                </button>
                
                <!-- Desktop Global Search Trigger -->
                <div class="hidden md:flex items-center bg-slate-100/80 hover:bg-slate-200/50 dark:bg-slate-800/80 dark:hover:bg-slate-700/50 border border-transparent dark:border-slate-800 rounded-full px-4 py-1.5 w-64 text-slate-400 dark:text-slate-500 cursor-pointer transition-all duration-200" onclick="openSearchPalette()">
                    <i class="ph ph-magnifying-glass text-base mr-2.5"></i>
                    <span class="text-xs font-medium"><?= __('common.search') ?>...</span>
                    <kbd class="ml-auto bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 px-1.5 py-0.5 rounded text-[9px] text-slate-400 dark:text-slate-500 shadow-sm font-sans font-medium uppercase">Ctrl K</kbd>
                </div>

                <!-- Mobile Global Search Trigger -->
                <button class="md:hidden text-slate-400 hover:text-slate-600 dark:text-slate-500 dark:hover:text-slate-300 transition-colors p-2" onclick="openSearchPalette()" title="<?= __('common.search') ?>">
                    <i class="ph ph-magnifying-glass text-xl"></i>
                </button>
            </div>
            <div class="flex items-center space-x-4">
                <!-- Theme Toggle -->
                <button id="themeToggle" class="text-gray-400 hover:text-primary dark:text-slate-400 dark:hover:text-yellow-400 transition-colors relative" title="<?= __('common.toggle_theme') ?>">
                    <i id="themeToggleIcon" class="ph ph-moon text-xl"></i>
                </button>
                
                <!-- Notifications Dropdown -->
                <div class="relative">
                    <button id="notificationToggle" class="text-gray-400 hover:text-primary dark:text-slate-400 dark:hover:text-primary transition-colors relative" title="<?= __('common.notifications') ?>">
                        <i class="ph ph-bell text-xl"></i>
                        <span id="notificationBadge" class="hidden absolute top-0 right-0 -mt-1 -mr-1 h-3 w-3">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
                        </span>
                    </button>
                    
                    <div id="notificationDropdown" class="hidden absolute right-0 mt-2 w-80 bg-white dark:bg-slate-800 rounded-xl shadow-lg border border-gray-100 dark:border-slate-700 z-50 overflow-hidden transition-all transform origin-top-right">
                        <div class="px-4 py-3 border-b border-gray-100 dark:border-slate-700 flex justify-between items-center bg-gray-50/50 dark:bg-slate-800/50">
                            <h3 class="text-sm font-semibold text-gray-800 dark:text-white"><?= __('common.notifications') ?></h3>
                            <button id="markAllReadBtn" class="text-xs text-blue-600 dark:text-blue-400 hover:underline"><?= __('common.mark_all_read') ?></button>
                        </div>
                        <div id="notificationList" class="max-h-96 overflow-y-auto">
                            <!-- Injected by JS -->
                            <div class="p-4 text-center text-sm text-gray-500 dark:text-slate-400"><?= __('common.loading') ?></div>
                        </div>
                    </div>
                </div>
                
                <div class="h-6 w-px bg-gray-200 dark:bg-slate-700"></div>

                <form action="/logout" method="POST" id="logoutForm" class="inline m-0 p-0">
                    <input type="hidden" name="csrf_token" value="<?= Session::getCsrfToken() ?>">
                    <button type="button" onclick="confirmLogout()" class="text-slate-500 dark:text-slate-400 hover:text-red-600 dark:hover:text-red-400 text-sm font-medium flex items-center transition-colors">
                        <i class="ph ph-sign-out text-lg md:mr-1.5"></i> 
                        <span class="hidden md:inline"><?= __('common.logout') ?></span>
                    </button>
                </form>
            </div>
        </header>

        <!-- Dynamic Content Area -->
        <main class="flex-1 overflow-x-hidden overflow-y-auto p-6 md:p-8">
            <div class="w-full">
                <!-- Flash Messages (Toast Notifications) -->
                <?php if ($flashSuccess = Session::getFlash('success')): ?>
                    <script>
                        document.addEventListener("DOMContentLoaded", function() {
                            const Toast = Swal.mixin({
                                toast: true,
                                position: 'top',
                                showConfirmButton: false,
                                timer: 3000,
                                timerProgressBar: true,
                                width: 'auto',
                                customClass: {
                                    popup: 'dark:bg-slate-800 dark:text-white border border-gray-100 dark:border-slate-700 shadow-xl',
                                    title: 'text-[13px] font-medium font-sans whitespace-nowrap px-2'
                                },
                                showClass: {
                                    popup: 'animate__animated animate__fadeInDown animate__faster'
                                },
                                hideClass: {
                                    popup: 'animate__animated animate__fadeOutUp animate__faster'
                                },
                                didOpen: (toast) => {
                                    toast.onmouseenter = Swal.stopTimer;
                                    toast.onmouseleave = Swal.resumeTimer;
                                }
                            });
                            Toast.fire({
                                icon: 'success',
                                title: <?= json_encode(htmlspecialchars($flashSuccess)) ?>
                            });
                        });
                    </script>
                <?php endif; ?>
                
                <?php if ($flashError = Session::getFlash('error')): ?>
                    <script>
                        document.addEventListener("DOMContentLoaded", function() {
                            const Toast = Swal.mixin({
                                toast: true,
                                position: 'top',
                                showConfirmButton: false,
                                timer: 4000,
                                timerProgressBar: true,
                                width: 'auto',
                                customClass: {
                                    popup: 'dark:bg-slate-800 dark:text-white border border-gray-100 dark:border-slate-700 shadow-xl',
                                    title: 'text-[13px] font-medium font-sans whitespace-nowrap px-2'
                                },
                                showClass: {
                                    popup: 'animate__animated animate__fadeInDown animate__faster'
                                },
                                hideClass: {
                                    popup: 'animate__animated animate__fadeOutUp animate__faster'
                                },
                                didOpen: (toast) => {
                                    toast.onmouseenter = Swal.stopTimer;
                                    toast.onmouseleave = Swal.resumeTimer;
                                }
                            });
                            Toast.fire({
                                icon: 'error',
                                title: <?= json_encode(htmlspecialchars($flashError)) ?>
                            });
                        });
                    </script>
                <?php endif; ?>

                <!-- View Content Injected Here -->
                <?= $content ?? '' ?>
                
                <!-- Footer -->
                <footer class="mt-8 pt-4 border-t border-gray-200 dark:border-slate-800 text-center text-sm text-gray-500 dark:text-slate-400 pb-4 transition-colors">
                    <?= Settings::get('site_footer', '&copy; ' . date('Y') . ' Standard Core System. All rights reserved.') ?>
                </footer>
            </div>
        </main>
    </div>

    <!-- Theme Toggle Logic -->
    <script>
        const themeToggleBtn = document.getElementById('themeToggle');
        const themeToggleIcon = document.getElementById('themeToggleIcon');

        // Function to update icon based on current theme
        function updateIcon() {
            if (document.documentElement.classList.contains('dark')) {
                themeToggleIcon.classList.remove('ph-moon');
                themeToggleIcon.classList.add('ph-sun');
            } else {
                themeToggleIcon.classList.remove('ph-sun');
                themeToggleIcon.classList.add('ph-moon');
            }
        }

        // Initialize icon
        updateIcon();

        document.addEventListener('DOMContentLoaded', function() {
            // Notifications Logic
            const notifToggle = document.getElementById('notificationToggle');
            const notifDropdown = document.getElementById('notificationDropdown');
            const notifBadge = document.getElementById('notificationBadge');
            const notifList = document.getElementById('notificationList');
            const markAllBtn = document.getElementById('markAllReadBtn');
            
            let notificationsOpen = false;

            function fetchNotifications() {
                axios.get('/api/notifications')
                    .then(response => {
                        const data = response.data;
                        if (data.success) {
                            if (data.unreadCount > 0) {
                                notifBadge.classList.remove('hidden');
                                notifBadge.classList.add('flex');
                            } else {
                                notifBadge.classList.add('hidden');
                                notifBadge.classList.remove('flex');
                            }
                            
                            renderNotifications(data.notifications);
                        }
                    });
            }

            function renderNotifications(notifications) {
                if (!notifications || notifications.length === 0) {
                    notifList.innerHTML = '<div class="p-4 text-center text-sm text-gray-500 dark:text-slate-400"><?= __('common.no_notifications') ?></div>';
                    return;
                }
                
                notifList.innerHTML = '';
                notifications.forEach(n => {
                    const isReadClass = n.is_read == 1 ? 'opacity-60' : 'bg-blue-50/50 dark:bg-blue-900/10';
                    const icon = n.type === 'alert' ? 'ph-warning text-yellow-500' : 'ph-info text-blue-500';
                    
                    const item = document.createElement('div');
                    item.className = `p-3 border-b border-gray-100 dark:border-slate-700/50 hover:bg-gray-50 dark:hover:bg-slate-700/50 transition-colors cursor-pointer flex items-start gap-3 ${isReadClass}`;
                    item.innerHTML = `
                        <div class="mt-0.5"><i class="ph-fill ${icon} text-lg"></i></div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate">${n.title}</p>
                            <p class="text-xs text-gray-500 dark:text-slate-400 line-clamp-2 mt-0.5">${n.message}</p>
                            <p class="text-[10px] text-gray-400 mt-1">${formatDate(n.created_at, true)}</p>
                        </div>
                    `;
                    
                    item.addEventListener('click', () => {
                        if (n.is_read == 0) {
                            axios.post('/api/notifications/read', { id: n.id }).then(() => {
                                fetchNotifications();
                            });
                        }
                        if (n.link) window.location.href = n.link;
                    });
                    
                    notifList.appendChild(item);
                });
            }

            notifToggle.addEventListener('click', (e) => {
                e.stopPropagation();
                notificationsOpen = !notificationsOpen;
                if (notificationsOpen) {
                    notifDropdown.classList.remove('hidden');
                    fetchNotifications();
                } else {
                    notifDropdown.classList.add('hidden');
                }
            });

            markAllBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                axios.post('/api/notifications/read-all').then(() => {
                    fetchNotifications();
                });
            });

            document.addEventListener('click', (e) => {
                if (notificationsOpen && !notifDropdown.contains(e.target) && !notifToggle.contains(e.target)) {
                    notificationsOpen = false;
                    notifDropdown.classList.add('hidden');
                }
            });

            // Initial fetch
            fetchNotifications();
            // Poll every 60s
            setInterval(fetchNotifications, 60000);
        });

        // Toggle action
        themeToggleBtn.addEventListener('click', function() {
            if (document.documentElement.classList.contains('dark')) {
                document.documentElement.classList.remove('dark');
                localStorage.theme = 'light';
            } else {
                document.documentElement.classList.add('dark');
                localStorage.theme = 'dark';
            }
            updateIcon();
        });

        // Mobile Sidebar Toggle
        const $sidebar = $('#sidebar');
        const $overlay = $('#sidebarOverlay');
        const $toggleBtn = $('#sidebarToggle');

        function openSidebar() {
            $sidebar.removeClass('-translate-x-full');
            $overlay.removeClass('hidden');
            // small delay for transition
            setTimeout(() => $overlay.removeClass('opacity-0'), 10);
        }

        function closeSidebar() {
            $sidebar.addClass('-translate-x-full');
            $overlay.addClass('opacity-0');
            setTimeout(() => $overlay.addClass('hidden'), 300); // match duration
        }

        $toggleBtn.on('click', openSidebar);
        $overlay.on('click', closeSidebar);

        // Logout Confirmation
        function confirmLogout() {
            Swal.fire({
                title: '<?= __('common.logout_confirm_title') ?>',
                text: '<?= __('common.logout_confirm_text') ?>',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: '<?= __('common.yes_logout') ?>',
                cancelButtonText: '<?= __('common.cancel') ?>'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('logoutForm').submit();
                }
            });
        }

        // --- Command Palette Search Logic ---
        document.addEventListener('DOMContentLoaded', function() {
            const searchModal = document.getElementById('searchPaletteModal');
            const searchInput = document.getElementById('globalSearchInput');
            const searchDefaultState = document.getElementById('searchDefaultState');
            const searchResultsContainer = document.getElementById('searchResultsContainer');

            if (!searchModal || !searchInput) return;

            let debounceTimeout = null;

            window.openSearchPalette = function() {
                searchModal.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
                setTimeout(() => searchInput.focus(), 50);
            };

            window.closeSearchPalette = function() {
                searchModal.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
                searchInput.value = '';
                searchResultsContainer.innerHTML = '';
                searchResultsContainer.classList.add('hidden');
                searchDefaultState.classList.remove('hidden');
            };

            // Hotkeys Ctrl+K / Cmd+K
            document.addEventListener('keydown', (e) => {
                if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
                    e.preventDefault();
                    window.openSearchPalette();
                }
                if (e.key === 'Escape') {
                    window.closeSearchPalette();
                }
            });

            searchInput.addEventListener('input', (e) => {
                const query = e.target.value.trim();

                clearTimeout(debounceTimeout);

                if (query.length < 2) {
                    searchResultsContainer.innerHTML = '';
                    searchResultsContainer.classList.add('hidden');
                    searchDefaultState.classList.remove('hidden');
                    return;
                }

                debounceTimeout = setTimeout(() => {
                    axios.get('/api/search', { params: { q: query } })
                        .then(response => {
                            const data = response.data;
                            if (data.success) {
                                renderSearchResults(data.results, query);
                            }
                        })
                        .catch(error => {
                            console.error('Search error:', error);
                        });
                }, 250);
            });

            function renderSearchResults(results, query) {
                searchDefaultState.classList.add('hidden');
                searchResultsContainer.classList.remove('hidden');
                searchResultsContainer.innerHTML = '';

                let totalResults = 0;

                const categories = {
                    users: {
                        title: '<?= __('common.user_management') ?>',
                        icon: 'ph-users-three',
                        color: 'bg-blue-50 text-blue-500 dark:bg-blue-900/20 dark:text-blue-400',
                        getUrl: item => `/users?search=${encodeURIComponent(item.username)}`,
                        formatName: item => `${highlight(item.username, query)} <span class="text-xs text-slate-400 dark:text-slate-500">(${highlight(item.email, query)})</span>`,
                        formatSub: item => `<span class="px-1.5 py-0.5 rounded text-[10px] uppercase font-bold tracking-wider ${item.status === 'active' ? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-950/30 dark:text-emerald-400' : 'bg-rose-50 text-rose-600 dark:bg-rose-950/30 dark:text-rose-400'}">${item.status}</span>`
                    },
                    roles: {
                        title: '<?= __('common.roles_permissions') ?>',
                        icon: 'ph-shield-check',
                        color: 'bg-purple-50 text-purple-500 dark:bg-purple-900/20 dark:text-purple-400',
                        getUrl: item => `/roles`,
                        formatName: item => highlight(item.name, query),
                        formatSub: item => highlight(item.description || '', query)
                    },
                    logs: {
                        title: '<?= __('common.activity_logs') ?>',
                        icon: 'ph-activity',
                        color: 'bg-emerald-50 text-emerald-500 dark:bg-emerald-900/20 dark:text-emerald-400',
                        getUrl: item => `/logs/activity`,
                        formatName: item => highlight(item.action, query),
                        formatSub: item => `<span class="text-slate-400">by</span> ${highlight(item.username || 'System', query)} <span class="text-[10px] text-slate-400 ml-2">${formatDate(item.created_at, true)}</span>`
                    },
                    translations: {
                        title: '<?= __('settings.trans_title') ?? 'Translations' ?>',
                        icon: 'ph-translate',
                        color: 'bg-amber-50 text-amber-500 dark:bg-amber-900/20 dark:text-amber-400',
                        getUrl: item => `/settings/translations`,
                        formatName: item => `<span class="font-mono text-xs text-slate-400 dark:text-slate-500">${highlight(item.namespace, query)}.${highlight(item.key_name, query)}</span>`,
                        formatSub: item => highlight(item.text_value, query)
                    }
                };

                function highlight(text, search) {
                    if (!text) return '';
                    const pattern = new RegExp(`(${escapeRegExp(search)})`, 'gi');
                    return text.replace(pattern, '<mark class="bg-yellow-100 dark:bg-yellow-500/30 text-yellow-950 dark:text-yellow-100 rounded-sm px-0.5">$1</mark>');
                }

                function escapeRegExp(string) {
                    return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
                }

                for (const [key, category] of Object.entries(categories)) {
                    const items = results[key] || [];
                    if (items.length === 0) continue;

                    totalResults += items.length;

                    const section = document.createElement('div');
                    section.className = 'space-y-2';
                    
                    const title = document.createElement('div');
                    title.className = 'text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider px-2';
                    title.textContent = category.title;
                    section.appendChild(title);

                    const list = document.createElement('div');
                    list.className = 'space-y-1';

                    items.forEach(item => {
                        const link = document.createElement('a');
                        link.href = category.getUrl(item);
                        link.className = 'flex items-center gap-3 p-2.5 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800/30 border border-transparent hover:border-slate-100 dark:hover:border-slate-800/50 transition-all';
                        link.innerHTML = `
                            <div class="w-8 h-8 rounded-lg ${category.color} flex items-center justify-center flex-shrink-0">
                                <i class="ph-fill ${category.icon} text-lg"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-800 dark:text-slate-200 truncate">${category.formatName(item)}</p>
                                <p class="text-xs text-slate-400 dark:text-slate-500 truncate mt-0.5">${category.formatSub(item)}</p>
                            </div>
                            <i class="ph ph-caret-right text-slate-400 text-xs"></i>
                        `;
                        list.appendChild(link);
                    });

                    section.appendChild(list);
                    searchResultsContainer.appendChild(section);
                }

                if (totalResults === 0) {
                    searchResultsContainer.innerHTML = `
                        <div class="flex flex-col items-center justify-center py-12 text-center">
                            <i class="ph-fill ph-folder-open text-slate-300 dark:text-slate-700 text-5xl mb-3"></i>
                            <p class="text-sm font-medium text-slate-500 dark:text-slate-400">ไม่พบผลลัพธ์สำหรับ "${query}"</p>
                            <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">กรุณาลองป้อนคำค้นหาคำอื่น</p>
                        </div>
                    `;
                }
            }
        });
    </script>

    <!-- Global Search Command Palette Modal -->
    <div id="searchPaletteModal" class="fixed inset-0 z-50 overflow-y-auto hidden" role="dialog" aria-modal="true">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onclick="closeSearchPalette()"></div>

        <!-- Modal Panel wrapper -->
        <div class="flex min-h-full items-start justify-center p-4 pt-16 sm:p-6 sm:pt-24">
            <div class="relative w-full max-w-2xl transform overflow-hidden rounded-2xl bg-white dark:bg-slate-900 shadow-2xl border border-gray-100 dark:border-slate-800 transition-all duration-300 flex flex-col max-h-[70vh]">
                <!-- Search Input Wrapper -->
                <div class="relative flex items-center border-b border-gray-100 dark:border-slate-800 px-4">
                    <i class="ph ph-magnifying-glass text-slate-400 dark:text-slate-500 text-xl mr-3"></i>
                    <input type="text" id="globalSearchInput" placeholder="<?= __('common.search') ?>..." class="w-full bg-transparent border-0 py-4 text-gray-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:ring-0 text-base" autocomplete="off">
                    <button onclick="closeSearchPalette()" class="text-slate-400 dark:text-slate-500 hover:text-gray-600 dark:hover:text-gray-300 text-xs font-medium px-2 py-1 rounded-md border border-gray-200 dark:border-slate-800">ESC</button>
                </div>

                <!-- Search Results Area -->
                <div id="globalSearchResults" class="flex-1 overflow-y-auto p-4 space-y-4">
                    <!-- Quick Links / Default State -->
                    <div id="searchDefaultState" class="space-y-4">
                        <div class="text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider px-2"><?= __('common.main_menu') ?></div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                            <a href="/users" class="flex items-center gap-3 p-3 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800/50 border border-transparent hover:border-slate-100 dark:hover:border-slate-800 transition-all">
                                <div class="w-8 h-8 rounded-lg bg-blue-50 dark:bg-blue-900/20 text-blue-500 flex items-center justify-center"><i class="ph ph-users text-lg"></i></div>
                                <div class="text-sm font-medium text-gray-700 dark:text-slate-300"><?= __('common.user_management') ?></div>
                            </a>
                            <a href="/roles" class="flex items-center gap-3 p-3 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800/50 border border-transparent hover:border-slate-100 dark:hover:border-slate-800 transition-all">
                                <div class="w-8 h-8 rounded-lg bg-purple-50 dark:bg-purple-900/20 text-purple-500 flex items-center justify-center"><i class="ph ph-shield-check text-lg"></i></div>
                                <div class="text-sm font-medium text-gray-700 dark:text-slate-300"><?= __('common.roles_permissions') ?></div>
                            </a>
                            <a href="/logs/activity" class="flex items-center gap-3 p-3 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800/50 border border-transparent hover:border-slate-100 dark:hover:border-slate-800 transition-all">
                                <div class="w-8 h-8 rounded-lg bg-emerald-50 dark:bg-emerald-900/20 text-emerald-500 flex items-center justify-center"><i class="ph ph-activity text-lg"></i></div>
                                <div class="text-sm font-medium text-gray-700 dark:text-slate-300"><?= __('common.activity_logs') ?></div>
                            </a>
                            <a href="/settings" class="flex items-center gap-3 p-3 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800/50 border border-transparent hover:border-slate-100 dark:hover:border-slate-800 transition-all">
                                <div class="w-8 h-8 rounded-lg bg-amber-50 dark:bg-amber-900/20 text-amber-500 flex items-center justify-center"><i class="ph ph-gear text-lg"></i></div>
                                <div class="text-sm font-medium text-gray-700 dark:text-slate-300"><?= __('common.system_settings') ?></div>
                            </a>
                        </div>
                    </div>

                    <!-- Results Container -->
                    <div id="searchResultsContainer" class="hidden space-y-4">
                        <!-- Category items will be dynamically injected here -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
