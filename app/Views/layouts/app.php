<?php
use App\Core\Settings;
use App\Core\Session;
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
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Axios -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
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
        .swal2-popup {
            border-radius: 1rem !important;
            padding: 2rem !important;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04) !important;
            font-family: 'Prompt', sans-serif !important;
        }
        .swal2-title { font-weight: 600 !important; font-size: 1.5rem !important; }
        .swal2-confirm { border-radius: 0.5rem !important; font-weight: 500 !important; padding: 0.625rem 1.5rem !important; }
        .swal2-cancel { border-radius: 0.5rem !important; font-weight: 500 !important; padding: 0.625rem 1.5rem !important; }

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
    </style>

    <!-- jQuery & DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script>
        // Global DataTables Defaults (Premium UI + i18n)
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
                "processing": "กำลังดำเนินการ...",
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
                "lengthMenu": "Show _MENU_ entries"
                ' ?>
            }
        });
    </script>
</head>
<body class="text-gray-800 dark:text-slate-200 antialiased h-screen flex overflow-hidden bg-slate-50 dark:bg-slate-950 transition-colors duration-300">

    <!-- Mobile Sidebar Overlay -->
    <div id="sidebarOverlay" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-40 hidden md:hidden transition-opacity opacity-0"></div>

    <!-- Sidebar -->
    <aside id="sidebar" class="w-64 bg-slate-900 text-white flex-col fixed inset-y-0 left-0 transform -translate-x-full md:relative md:translate-x-0 z-50 transition-transform duration-300 shadow-xl flex">
        <div class="h-16 flex items-center px-6 border-b border-slate-800 font-bold text-lg tracking-wider">
            <?php $logo = Settings::get('site_logo'); if ($logo): ?>
                <img src="<?= htmlspecialchars($logo) ?>" alt="Logo" class="h-8 w-auto mr-3">
            <?php else: ?>
                <i class="ph-fill ph-cube text-blue-400 text-2xl mr-2"></i>
            <?php endif; ?>
            <span class="truncate"><?= htmlspecialchars(Settings::get('site_name', 'CORE SYSTEM')) ?></span>
        </div>
        
        <div class="flex-1 overflow-y-auto py-6">
            <div class="px-4 mb-2 text-xs font-semibold text-slate-500 uppercase tracking-wider"><?= __('common.main_menu') ?></div>
            <nav class="space-y-1 px-3 mb-8">
                <a href="/dashboard" class="sidebar-item <?= strpos($_SERVER['REQUEST_URI'] ?? '', '/dashboard') === 0 ? 'active' : '' ?> flex items-center px-3 py-2.5 rounded-lg text-sm font-medium">
                    <i class="ph ph-squares-four text-lg mr-3"></i> <?= __('common.dashboard') ?>
                </a>
            </nav>

            <div class="px-4 mb-2 text-xs font-semibold text-slate-500 uppercase tracking-wider"><?= __('common.administration') ?></div>
            <nav class="space-y-1 px-3">
                <a href="/users" class="sidebar-item <?= strpos($_SERVER['REQUEST_URI'] ?? '', '/users') === 0 ? 'active' : '' ?> flex items-center px-3 py-2.5 rounded-lg text-sm font-medium <?= strpos($_SERVER['REQUEST_URI'] ?? '', '/users') === 0 ? 'text-white' : 'text-slate-300' ?>">
                    <i class="ph ph-users text-lg mr-3"></i> <?= __('common.user_management') ?>
                </a>
                <a href="/roles" class="sidebar-item <?= strpos($_SERVER['REQUEST_URI'] ?? '', '/roles') === 0 ? 'active' : '' ?> flex items-center px-3 py-2.5 rounded-lg text-sm font-medium <?= strpos($_SERVER['REQUEST_URI'] ?? '', '/roles') === 0 ? 'text-white' : 'text-slate-300' ?>">
                    <i class="ph ph-shield-check text-lg mr-3"></i> <?= __('common.roles_permissions') ?>
                </a>
                <a href="/logs/activity" class="sidebar-item <?= strpos($_SERVER['REQUEST_URI'] ?? '', '/logs/activity') === 0 ? 'active' : '' ?> flex items-center px-3 py-2.5 rounded-lg text-sm font-medium <?= strpos($_SERVER['REQUEST_URI'] ?? '', '/logs/activity') === 0 ? 'text-white' : 'text-slate-300' ?>">
                    <i class="ph ph-activity text-lg mr-3"></i> <?= __('common.activity_logs') ?>
                </a>
                <a href="/logs/audit" class="sidebar-item <?= strpos($_SERVER['REQUEST_URI'] ?? '', '/logs/audit') === 0 ? 'active' : '' ?> flex items-center px-3 py-2.5 rounded-lg text-sm font-medium <?= strpos($_SERVER['REQUEST_URI'] ?? '', '/logs/audit') === 0 ? 'text-white' : 'text-slate-300' ?>">
                    <i class="ph ph-file-search text-lg mr-3"></i> <?= __('common.audit_logs') ?>
                </a>
                <a href="/settings" class="sidebar-item <?= strpos($_SERVER['REQUEST_URI'] ?? '', '/settings') === 0 ? 'active' : '' ?> flex items-center px-3 py-2.5 rounded-lg text-sm font-medium <?= strpos($_SERVER['REQUEST_URI'] ?? '', '/settings') === 0 ? 'text-white' : 'text-slate-300' ?>">
                    <i class="ph ph-gear text-lg mr-3"></i> <?= __('common.system_settings') ?>
                </a>
            </nav>
        </div>
        
        <div class="p-4 border-t border-slate-800 bg-slate-950 relative group cursor-pointer">
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
            <div class="flex items-center">
                <button id="sidebarToggle" class="md:hidden text-gray-500 dark:text-slate-400 hover:text-gray-700 dark:hover:text-slate-200 transition-colors mr-3">
                    <i class="ph ph-list text-2xl"></i>
                </button>
                <div class="font-semibold text-gray-800 dark:text-slate-100 text-lg">
                    <?= $pageTitle ?? __('common.dashboard') ?>
                </div>
            </div>
            <div class="flex items-center space-x-4">
                <!-- Language Switcher -->
                <?php $currentLocale = \App\Core\Lang::locale(); ?>
                <div class="flex items-center bg-gray-100 dark:bg-slate-800 rounded-lg p-1 text-xs font-medium border border-gray-200 dark:border-slate-700">
                    <a href="/lang/th" class="px-2 py-1 rounded-md <?= $currentLocale === 'th' ? 'bg-white dark:bg-blue-600 text-blue-600 dark:text-white shadow-sm font-bold' : 'text-gray-500 dark:text-slate-400 hover:text-gray-800 dark:hover:text-white' ?> transition-all flex items-center">
                        TH
                    </a>
                    <a href="/lang/en" class="px-2 py-1 rounded-md <?= $currentLocale === 'en' ? 'bg-white dark:bg-blue-600 text-blue-600 dark:text-white shadow-sm font-bold' : 'text-gray-500 dark:text-slate-400 hover:text-gray-800 dark:hover:text-white' ?> transition-all flex items-center">
                        EN
                    </a>
                </div>

                <!-- Theme Toggle -->
                <button id="themeToggle" class="text-gray-400 hover:text-primary dark:text-slate-400 dark:hover:text-yellow-400 transition-colors relative" title="<?= __('common.toggle_theme') ?>">
                    <i id="themeToggleIcon" class="ph ph-moon text-xl"></i>
                </button>
                
                <button class="text-gray-400 hover:text-primary dark:text-slate-400 dark:hover:text-primary transition-colors relative" title="<?= __('common.notifications') ?>">
                    <i class="ph ph-bell text-xl"></i>
                    <span class="absolute top-0 right-0 -mt-1 -mr-1 flex h-3 w-3">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
                    </span>
                </button>
                
                <div class="h-6 w-px bg-gray-200 dark:bg-slate-700"></div>

                <form action="/logout" method="POST" id="logoutForm" class="inline m-0 p-0">
                    <input type="hidden" name="csrf_token" value="<?= Session::getCsrfToken() ?>">
                    <button type="button" onclick="confirmLogout()" class="text-slate-500 dark:text-slate-400 hover:text-red-600 dark:hover:text-red-400 text-sm font-medium flex items-center transition-colors">
                        <i class="ph ph-sign-out text-lg mr-1.5"></i> <?= __('common.logout') ?>
                    </button>
                </form>
            </div>
        </header>

        <!-- Dynamic Content Area -->
        <main class="flex-1 overflow-x-hidden overflow-y-auto p-6 md:p-8">
            <div class="w-full">
                <!-- Flash Messages -->
                <?php if ($flashSuccess = Session::getFlash('success')): ?>
                    <div class="mb-6 bg-emerald-50 border border-emerald-200 rounded-xl p-4 flex items-start shadow-sm animate-fade-in-down">
                        <i class="ph-fill ph-check-circle text-emerald-500 text-xl mr-3 mt-0.5"></i>
                        <p class="text-sm text-emerald-800 font-medium"><?= htmlspecialchars($flashSuccess) ?></p>
                    </div>
                <?php endif; ?>
                
                <?php if ($flashError = Session::getFlash('error')): ?>
                    <div class="mb-6 bg-red-50 border border-red-200 rounded-xl p-4 flex items-start shadow-sm animate-fade-in-down">
                        <i class="ph-fill ph-warning-circle text-red-500 text-xl mr-3 mt-0.5"></i>
                        <p class="text-sm text-red-800 font-medium"><?= htmlspecialchars($flashError) ?></p>
                    </div>
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
    </script>
</body>
</html>
