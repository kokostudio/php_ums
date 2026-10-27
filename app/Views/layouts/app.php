<?php
    use App\Core\Session;
    use App\Core\Settings;
    use App\Core\Theme;
    use App\Core\ThemeColor;

    // ── Dynamic Theme (Preset หรือ Custom Color Picker) ──
    $theme       = Theme::resolve();
    $sidebarHex  = $theme['background'];
    $paletteJson = json_encode(Theme::paletteForTailwind($theme), JSON_UNESCAPED_SLASHES);
?>
<!DOCTYPE html>
<html lang="<?php echo \App\Core\Lang::locale() ?>" class="<?php echo htmlspecialchars(Settings::get('base_font_size', 'text-[16px]')) ?> bg-slate-50 dark:bg-slate-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars(Settings::get('site_name', 'System')) ?></title>

    <?php $favicon = Settings::get('site_favicon');if ($favicon): ?>
        <link rel="icon" href="<?php echo htmlspecialchars($favicon) ?>">
    <?php endif; ?>
    <!-- Dark Mode Init Script (Prevents FOUC) -->
    <script>
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark')
        } else {
            document.documentElement.classList.remove('dark')
        }
    </script>
    <style>
        /* Instant Background & FOUC Prevention (prevents white flash before Tailwind CDN compiles) */
        html.dark, html.dark body { background-color: #0f172a !important; color: #f8fafc; }
        html:not(.dark), html:not(.dark) body { background-color: #f8fafc !important; color: #0f172a; }

        /* ── Dynamic Theme Variables (Custom Color Picker) ── */
        <?= \App\Core\Theme::cssVariables($theme) ?>

        /* Sidebar surfaces driven by the active theme (รองรับสีอิสระจาก Color Picker) */
        .sidebar-surface { background-color: var(--sidebar-bg); }
        .sidebar-surface .sidebar-header { border-color: var(--sidebar-border); }
        .sidebar-surface .sidebar-footer { border-color: var(--sidebar-border); background-color: var(--sidebar-footer); }
    </style>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        sidebar: '<?php echo $sidebarHex ?>',
                        primary: <?php echo $paletteJson ?>,
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
        
        .submenu-item-link .pin-menu-btn { opacity: 0; transition: opacity 0.2s; }
        .submenu-item-link:hover .pin-menu-btn { opacity: 1; }

        /* ── Sidebar Collapsed State ── */
        html.sidebar-collapsed #sidebar { width: 5rem; }
        html.sidebar-collapsed #sidebar .truncate, 
        html.sidebar-collapsed #sidebar .sidebar-item span,
        html.sidebar-collapsed #sidebar .caret-icon,
        html.sidebar-collapsed #sidebar a span:not(.pin-menu-btn):not(.unpin-menu-btn),
        html.sidebar-collapsed #sidebar .pin-menu-btn,
        html.sidebar-collapsed #sidebar .unpin-menu-btn,
        html.sidebar-collapsed #pinned-menus-container .uppercase { display: none !important; }
        
        html.sidebar-collapsed #sidebar .sidebar-item { justify-content: center; padding-left: 0; padding-right: 0; }
        html.sidebar-collapsed #sidebar .sidebar-item i { margin-right: 0; font-size: 1.5rem; }
        
        html.sidebar-collapsed #sidebar .submenu { margin-left: 0 !important; padding-left: 0 !important; border-left: none !important; }
        html.sidebar-collapsed #sidebar .submenu a { justify-content: center; padding-left: 0; padding-right: 0; }
        html.sidebar-collapsed #sidebar .submenu a i { margin-right: 0; font-size: 1.5rem; }
        html.sidebar-collapsed #sidebar #pinned-menus a { justify-content: center; padding-left: 0; padding-right: 0; }
        html.sidebar-collapsed #sidebar #pinned-menus a i { margin-right: 0; font-size: 1.5rem; }
        
        html.sidebar-collapsed #sidebar .h-16 { padding: 0; justify-content: center; }
        html.sidebar-collapsed #sidebar .h-16 .truncate { display: none; }
        html.sidebar-collapsed #sidebar .h-16 img { margin-right: 0 !important; margin-left: auto; margin-right: auto; }
        html.sidebar-collapsed #sidebar .h-16 i.ph-cube { margin-right: 0 !important; font-size: 1.75rem !important; }
        html.sidebar-collapsed #pinSidebarBtn { display: none !important; }
        
        html.sidebar-collapsed #sidebar .p-4 { padding: 1rem 0; display: flex; justify-content: center; }
        html.sidebar-collapsed #sidebar .p-4 img, html.sidebar-collapsed #sidebar .p-4 .bg-gradient-to-tr { margin: 0 auto; }
        html.sidebar-collapsed #sidebar .p-4 .ml-3, html.sidebar-collapsed #sidebar .p-4 > i { display: none; }

        /* ── Custom Scrollbar (สีตามธีม Sidebar) ── */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: var(--scrollbar-track, transparent);
        }
        ::-webkit-scrollbar-thumb {
            background: var(--scrollbar-thumb);
            border-radius: 9999px;
            transition: background 0.2s;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: var(--scrollbar-thumb-hover);
        }
        ::-webkit-scrollbar-corner {
            background: transparent;
        }

        /* Scrollbar ภายใน Sidebar → ใช้สีเดียวกับพื้นหลัง Sidebar ที่ผู้ใช้เลือก */
        #sidebar ::-webkit-scrollbar-thumb {
            background: var(--scrollbar-sidebar-thumb);
        }
        #sidebar ::-webkit-scrollbar-thumb:hover {
            background: var(--scrollbar-sidebar-hover);
        }

        /* Dark mode scrollbar */
        .dark ::-webkit-scrollbar-thumb {
            background: var(--scrollbar-thumb-dark);
        }
        .dark ::-webkit-scrollbar-thumb:hover {
            background: var(--scrollbar-thumb-dark-hover);
        }
        .dark #sidebar ::-webkit-scrollbar-thumb {
            background: var(--scrollbar-sidebar-thumb);
        }
        .dark #sidebar ::-webkit-scrollbar-thumb:hover {
            background: var(--scrollbar-sidebar-hover);
        }

        /* Firefox scrollbar */
        * {
            scrollbar-width: thin;
            scrollbar-color: var(--scrollbar-thumb) var(--scrollbar-track, transparent);
        }
        .dark * {
            scrollbar-color: var(--scrollbar-thumb-dark) var(--scrollbar-track, transparent);
        }
        #sidebar {
            scrollbar-color: var(--scrollbar-sidebar-thumb) var(--scrollbar-track, transparent);
        }
        #sidebar * {
            scrollbar-color: var(--scrollbar-sidebar-thumb) var(--scrollbar-track, transparent);
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

        /* ── Fix DataTables Loading FOUC (แว็บ ๆ ตอนโหลดหน้า) ── */
        table[id$="Table"]:not(.dataTable) {
            opacity: 0;
            visibility: hidden;
        }
        table.dataTable {
            opacity: 1;
            visibility: visible;
            transition: opacity 0.3s ease-in;
        }

        /* ── Fix DataTables Processing Flicker (แว็บ ๆ) ── */
        div.dataTables_wrapper { position: relative; min-height: 450px; }
        div.dataTables_wrapper div.dataTables_processing {
            position: absolute !important;
            top: 45px !important; /* Below table header */
            left: 0 !important;
            width: 100% !important;
            height: calc(100% - 45px) !important;
            z-index: 20 !important;
            background: rgba(255, 255, 255, 0.7) !important;
            backdrop-filter: blur(2px) !important;
            margin: 0 !important;
            padding: 2rem !important;
            display: none; /* Will be set to flex by DataTables when active but we override display */
        }
        div.dataTables_wrapper div.dataTables_processing[style*="display: block"] {
            display: flex !important;
            flex-direction: column !important;
            justify-content: flex-start !important;
        }
        .dark div.dataTables_wrapper div.dataTables_processing {
            background: rgba(15, 23, 42, 0.7) !important;
        }

        /* ── Premium DataTables Styling (Replaced by Official Tailwind CSS) ── */
    </style>

    <!-- jQuery & DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.tailwindcss.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.tailwindcss.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
    <script>
        // Global DataTables Defaults (Premium UI + i18n + Skeleton Loader)
        $.extend(true, $.fn.dataTable.defaults, {
            dom: '<"flex flex-col md:flex-row justify-between items-center mb-4"<"flex items-center gap-2"l><"flex items-center gap-2"f>>rt<"flex flex-col md:flex-row justify-between items-center mt-4"ip><"clear">',
            language: {
                <?php echo \App\Core\Lang::locale() === 'th' ? '
                "emptyTable": "<div class=\'flex flex-col items-center justify-center py-10 text-center\'><div class=\'w-16 h-16 bg-gray-100 dark:bg-slate-800/80 rounded-full flex items-center justify-center mb-3\'><i class=\'ph ph-folder-open text-2xl text-gray-400 dark:text-slate-500\'></i></div><p class=\'text-sm font-medium text-gray-500 dark:text-slate-400\'>ไม่มีข้อมูลในตาราง</p></div>",
                "info": "แสดง _START_ ถึง _END_ จาก _TOTAL_ รายการ",
                "infoEmpty": "แสดง 0 ถึง 0 จาก 0 รายการ",
                "infoFiltered": "(กรองข้อมูล _MAX_ ทุกรายการ)",
                "lengthMenu": "แสดง _MENU_ รายการ",
                "loadingRecords": "",
                "processing": "<div class=\'animate-pulse space-y-4\'><div class=\'h-8 bg-slate-200 dark:bg-slate-700 rounded w-full\'></div><div class=\'h-8 bg-slate-200 dark:bg-slate-700 rounded w-full\'></div><div class=\'h-8 bg-slate-200 dark:bg-slate-700 rounded w-full\'></div></div>",
                "search": "ค้นหา:",
                "zeroRecords": "<div class=\'flex flex-col items-center justify-center py-10 text-center\'><div class=\'w-16 h-16 bg-gray-100 dark:bg-slate-800/80 rounded-full flex items-center justify-center mb-3\'><i class=\'ph ph-magnifying-glass text-2xl text-gray-400 dark:text-slate-500\'></i></div><p class=\'text-sm font-medium text-gray-500 dark:text-slate-400\'>ไม่พบข้อมูล</p></div>",
                "paginate": {
                    "first": "หน้าแรก",
                    "last": "หน้าสุดท้าย",
                    "next": "ถัดไป",
                    "previous": "ก่อนหน้า"
                }
                ' : '
                "search": "Search:",
                "lengthMenu": "Show _MENU_ entries",
                "emptyTable": "<div class=\'flex flex-col items-center justify-center py-10 text-center\'><div class=\'w-16 h-16 bg-gray-100 dark:bg-slate-800/80 rounded-full flex items-center justify-center mb-3\'><i class=\'ph ph-folder-open text-2xl text-gray-400 dark:text-slate-500\'></i></div><p class=\'text-sm font-medium text-gray-500 dark:text-slate-400\'>No data available in table</p></div>",
                "zeroRecords": "<div class=\'flex flex-col items-center justify-center py-10 text-center\'><div class=\'w-16 h-16 bg-gray-100 dark:bg-slate-800/80 rounded-full flex items-center justify-center mb-3\'><i class=\'ph ph-magnifying-glass text-2xl text-gray-400 dark:text-slate-500\'></i></div><p class=\'text-sm font-medium text-gray-500 dark:text-slate-400\'>No matching records found</p></div>",
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
    <aside id="sidebar" class="w-64 sidebar-surface text-white flex-col fixed inset-y-0 left-0 transform -translate-x-full md:relative md:translate-x-0 z-50 transition-all duration-300 shadow-xl flex">
        <div class="h-16 sidebar-header flex items-center px-6 border-b font-bold text-lg tracking-wider justify-between">
            <div class="flex items-center overflow-hidden">
                <?php $logo = Settings::get('site_logo');if ($logo): ?>
                    <img src="<?php echo htmlspecialchars($logo) ?>" alt="Logo" class="h-8 w-auto mr-3">
                <?php else: ?>
                    <i class="ph-fill ph-cube text-blue-400 text-2xl mr-2"></i>
                <?php endif; ?>
                <span class="truncate"><?php echo htmlspecialchars(Settings::get('site_name', 'CORE SYSTEM')) ?></span>
            </div>
        </div>

        <div class="flex-1 overflow-y-auto py-6">
            <div id="pinned-menus-container" class="hidden mb-4 border-b border-white/10 pb-4">
                <div class="px-6 mb-2 text-[10px] font-bold text-slate-400 uppercase tracking-wider"><?= __('common.pinned') ?? 'Pinned' ?></div>
                <nav id="pinned-menus" class="space-y-1 px-3"></nav>
            </div>
            <nav id="main-sidebar-nav" class="space-y-1 px-3">
                <!-- Group: System Management -->
                <?php
                    $systemRoutes   = ['/dashboard', '/users', '/roles', '/logs/activity', '/logs/audit', '/settings'];
                    $isSystemActive = false;
                    $currentUri     = $_SERVER['REQUEST_URI'] ?? '';
                    foreach ($systemRoutes as $route) {
                        if (strpos($currentUri, $route) === 0) {
                            $isSystemActive = true;
                            break;
                        }
                    }
                ?>
                <div class="sidebar-group">
                    <button type="button" class="sidebar-item w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium <?php echo $isSystemActive ? 'text-white bg-primary-900/50' : 'text-slate-200 hover:text-white hover:bg-primary-900/30' ?>" onclick="$(this).next('.submenu').slideToggle(200); $(this).find('.caret-icon').toggleClass('rotate-90');">
                        <div class="flex items-center">
                            <i class="ph ph-desktop text-lg mr-3"></i>
                            <span><?php echo __('common.administration') ?></span>
                        </div>
                        <i class="ph ph-caret-right text-xs caret-icon transition-transform duration-200 <?php echo $isSystemActive ? 'rotate-90' : '' ?>"></i>
                    </button>
                    <div class="submenu overflow-hidden <?php echo $isSystemActive ? '' : 'hidden' ?> mt-1 space-y-1 pl-4 border-l-2 border-primary-800/50 ml-5">
                        <a href="/dashboard" class="flex items-center px-3 py-2 text-sm font-medium transition-colors <?php echo strpos($currentUri, '/dashboard') === 0 ? 'text-white bg-primary-900/40 border-l-[3px] border-primary-400' : 'text-slate-300 hover:text-white hover:bg-primary-900/30 border-l-[3px] border-transparent' ?>">
                            <i class="ph ph-squares-four text-lg mr-3 <?php echo strpos($currentUri, '/dashboard') === 0 ? 'opacity-100 text-primary-400' : 'opacity-80' ?>"></i> <span><?php echo __('common.dashboard') ?></span>
                        </a>
                        <a href="/users" class="flex items-center px-3 py-2 text-sm font-medium transition-colors <?php echo strpos($currentUri, '/users') === 0 ? 'text-white bg-primary-900/40 border-l-[3px] border-primary-400' : 'text-slate-300 hover:text-white hover:bg-primary-900/30 border-l-[3px] border-transparent' ?>">
                            <i class="ph ph-users text-lg mr-3 <?php echo strpos($currentUri, '/users') === 0 ? 'opacity-100 text-primary-400' : 'opacity-80' ?>"></i> <span><?php echo __('common.user_management') ?></span>
                        </a>
                        <a href="/roles" class="flex items-center px-3 py-2 text-sm font-medium transition-colors <?php echo strpos($currentUri, '/roles') === 0 ? 'text-white bg-primary-900/40 border-l-[3px] border-primary-400' : 'text-slate-300 hover:text-white hover:bg-primary-900/30 border-l-[3px] border-transparent' ?>">
                            <i class="ph ph-shield-check text-lg mr-3 <?php echo strpos($currentUri, '/roles') === 0 ? 'opacity-100 text-primary-400' : 'opacity-80' ?>"></i> <span><?php echo __('common.roles_permissions') ?></span>
                        </a>
                        <a href="/logs/activity" class="flex items-center px-3 py-2 text-sm font-medium transition-colors <?php echo strpos($currentUri, '/logs/activity') === 0 ? 'text-white bg-primary-900/40 border-l-[3px] border-primary-400' : 'text-slate-300 hover:text-white hover:bg-primary-900/30 border-l-[3px] border-transparent' ?>">
                            <i class="ph ph-activity text-lg mr-3 <?php echo strpos($currentUri, '/logs/activity') === 0 ? 'opacity-100 text-primary-400' : 'opacity-80' ?>"></i> <span><?php echo __('common.activity_logs') ?></span>
                        </a>
                        <a href="/logs/audit" class="flex items-center px-3 py-2 text-sm font-medium transition-colors <?php echo strpos($currentUri, '/logs/audit') === 0 ? 'text-white bg-primary-900/40 border-l-[3px] border-primary-400' : 'text-slate-300 hover:text-white hover:bg-primary-900/30 border-l-[3px] border-transparent' ?>">
                            <i class="ph ph-file-search text-lg mr-3 <?php echo strpos($currentUri, '/logs/audit') === 0 ? 'opacity-100 text-primary-400' : 'opacity-80' ?>"></i> <span><?php echo __('common.audit_logs') ?></span>
                        </a>
                        <a href="/settings" class="flex items-center px-3 py-2 text-sm font-medium transition-colors <?php echo strpos($currentUri, '/settings') === 0 ? 'text-white bg-primary-900/40 border-l-[3px] border-primary-400' : 'text-slate-300 hover:text-white hover:bg-primary-900/30 border-l-[3px] border-transparent' ?>">
                            <i class="ph ph-gear text-lg mr-3 <?php echo strpos($currentUri, '/settings') === 0 ? 'opacity-100 text-primary-400' : 'opacity-80' ?>"></i> <span><?php echo __('common.system_settings') ?></span>
                        </a>
                    </div>
                </div>
            </nav>
        </div>

        <div class="p-4 sidebar-footer border-t relative group cursor-pointer">
            <a href="/profile" class="flex items-center hover:opacity-80 transition-opacity">
                <?php $profileImg = Session::get('profile_image');if ($profileImg): ?>
                    <img src="<?php echo htmlspecialchars($profileImg) ?>" alt="Profile" class="w-9 h-9 rounded-full object-cover shadow-inner border border-slate-700">
                <?php else: ?>
                    <div class="w-9 h-9 rounded-full bg-gradient-to-tr from-primary-500 to-primary-700 flex items-center justify-center font-bold text-sm shadow-inner text-white">
                        <?php echo htmlspecialchars(strtoupper(substr(Session::get('username', 'U'), 0, 1))) ?>
                    </div>
                <?php endif; ?>
                <div class="ml-3 overflow-hidden flex-1 text-white group-hover:text-primary-100 transition-colors">
                    <p class="text-sm font-medium truncate"><?php echo htmlspecialchars(Session::get('username', 'Guest')) ?></p>
                    <p class="text-xs text-primary-300/70 group-hover:text-primary-300 transition-colors truncate"><?php echo __('common.view_profile') ?></p>
                </div>
                <i class="ph ph-caret-right text-primary-400/50 group-hover:text-primary-400 transition-colors text-sm"></i>
            </a>
        </div>
    </aside>

    <!-- Main Content Wrapper -->
    <div class="flex-1 flex flex-col overflow-hidden relative">
        <!-- Topbar -->
        <header class="h-16 bg-white/80 dark:bg-slate-900/80 backdrop-blur-md border-b border-gray-200 dark:border-slate-800 flex items-center justify-between px-6 z-10 sticky top-0 transition-colors duration-300">
            <div class="flex items-center space-x-3">
                <!-- Mobile Menu Toggle (Drawer) -->
                <button id="sidebarToggle" class="md:hidden text-gray-500 dark:text-slate-400 hover:text-gray-700 dark:hover:text-slate-200 transition-colors mr-1">
                    <i class="ph ph-list text-2xl"></i>
                </button>
                <!-- Desktop Sidebar Collapse Toggle -->
                <button id="desktopSidebarToggle" class="hidden md:block text-gray-500 dark:text-slate-400 hover:text-gray-700 dark:hover:text-slate-200 transition-colors mr-1" title="Toggle Sidebar">
                    <i class="ph ph-list text-2xl"></i>
                </button>

                <!-- Desktop Global Search Trigger -->
                <div class="hidden md:flex items-center bg-slate-100/80 hover:bg-slate-200/50 dark:bg-slate-800/80 dark:hover:bg-slate-700/50 border border-transparent dark:border-slate-800 rounded-full px-4 py-1.5 w-64 text-slate-400 dark:text-slate-500 cursor-pointer transition-all duration-200" onclick="openSearchPalette()">
                    <i class="ph ph-magnifying-glass text-base mr-2.5"></i>
                    <span class="text-xs font-medium"><?php echo __('common.search') ?>...</span>
                    <kbd class="ml-auto bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 px-1.5 py-0.5 rounded text-[9px] text-slate-400 dark:text-slate-500 shadow-sm font-sans font-medium uppercase">Ctrl K</kbd>
                </div>

                <!-- Mobile Global Search Trigger -->
                <button class="md:hidden text-slate-400 hover:text-slate-600 dark:text-slate-500 dark:hover:text-slate-300 transition-colors p-2" onclick="openSearchPalette()" title="<?php echo __('common.search') ?>">
                    <i class="ph ph-magnifying-glass text-xl"></i>
                </button>
            </div>
            <div class="flex items-center space-x-4">
                <!-- Theme Toggle -->
                <button id="themeToggle" class="text-gray-400 hover:text-primary dark:text-slate-400 dark:hover:text-yellow-400 transition-colors relative" title="<?php echo __('common.toggle_theme') ?>">
                    <i id="themeToggleIcon" class="ph ph-moon text-xl"></i>
                </button>

                <!-- Notifications Dropdown -->
                <div class="relative">
                    <button id="notificationToggle" class="text-gray-400 hover:text-primary dark:text-slate-400 dark:hover:text-primary transition-colors relative" title="<?php echo __('common.notifications') ?>">
                        <i class="ph ph-bell text-xl"></i>
                        <span id="notificationBadge" class="hidden absolute top-0 right-0 -mt-1 -mr-1 h-3 w-3">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
                        </span>
                    </button>

                    <div id="notificationDropdown" class="hidden absolute right-0 mt-2 w-80 bg-white dark:bg-slate-800 rounded-xl shadow-lg border border-gray-100 dark:border-slate-700 z-50 overflow-hidden transition-all transform origin-top-right">
                        <div class="px-4 py-3 border-b border-gray-100 dark:border-slate-700 flex justify-between items-center bg-gray-50/50 dark:bg-slate-800/50">
                            <h3 class="text-sm font-semibold text-gray-800 dark:text-white"><?php echo __('common.notifications') ?></h3>
                            <button id="markAllReadBtn" class="text-xs text-blue-600 dark:text-blue-400 hover:underline"><?php echo __('common.mark_all_read') ?></button>
                        </div>
                        <div id="notificationList" class="max-h-96 overflow-y-auto">
                            <!-- Injected by JS -->
                            <div class="p-4 text-center text-sm text-gray-500 dark:text-slate-400"><?php echo __('common.loading') ?></div>
                        </div>
                    </div>
                </div>

                <div class="h-6 w-px bg-gray-200 dark:bg-slate-700"></div>

                <form action="/logout" method="POST" id="logoutForm" class="inline m-0 p-0">
                    <input type="hidden" name="csrf_token" value="<?php echo Session::getCsrfToken() ?>">
                    <button type="button" onclick="confirmLogout()" class="text-slate-500 dark:text-slate-400 hover:text-red-600 dark:hover:text-red-400 text-sm font-medium flex items-center transition-colors">
                        <i class="ph ph-sign-out text-lg md:mr-1.5"></i>
                        <span class="hidden md:inline"><?php echo __('common.logout') ?></span>
                    </button>
                </form>
            </div>
        </header>

        <!-- Dynamic Content Area -->
        <main class="flex-1 overflow-x-hidden overflow-y-auto p-6 md:p-8 pb-0 md:pb-0">
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
                                title: <?php echo json_encode(htmlspecialchars($flashSuccess)) ?>
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
                                title: <?php echo json_encode(htmlspecialchars($flashError)) ?>
                            });
                        });
                    </script>
                <?php endif; ?>

                <!-- View Content Injected Here -->
                <?php echo $content ?? '' ?>

                <!-- Footer -->
                <footer class="mt-6 py-4 border-t border-gray-200/60 dark:border-slate-800/60">
                    <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                        <div class="text-sm font-medium text-gray-500 dark:text-slate-400">
                            <?php echo Settings::get('site_footer', '&copy; ' . date('Y') . ' Standard Core System. All rights reserved.') ?>
                        </div>
                        <div class="flex items-center space-x-4 text-xs font-semibold text-gray-400 dark:text-slate-500">
                            <span class="flex items-center gap-1.5 hover:text-primary transition-colors cursor-pointer" title="Secure System">
                                <i class="ph-fill ph-shield-check text-sm text-emerald-500"></i> Secure Core
                            </span>
                            <span class="w-1 h-1 rounded-full bg-gray-300 dark:bg-slate-700"></span>
                            <span class="flex items-center gap-1.5 hover:text-primary transition-colors cursor-pointer">
                                <i class="ph-fill ph-code text-sm text-blue-500"></i> v1.0.0
                            </span>
                        </div>
                    </div>
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
                    notifList.innerHTML = '<div class="p-4 text-center text-sm text-gray-500 dark:text-slate-400"><?php echo __('common.no_notifications') ?></div>';
                    return;
                }

                notifList.innerHTML = '';
                notifications.forEach(n => {
                    const isReadClass = n.is_read == 1 ? 'opacity-60' : 'bg-blue-50/50 dark:bg-blue-900/10';
                    const icon = n.type === 'alert' ? 'ph-warning text-yellow-500' : 'ph-info text-blue-500';

                    const item = document.createElement('div');
                    item.className = `p-3 border-b border-gray-100 dark:border-primary-800/50 hover:bg-gray-50 dark:hover:bg-slate-700/50 transition-colors cursor-pointer flex items-start gap-3 ${isReadClass}`;
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

        // Desktop Sidebar Collapse Toggle (from Navbar)
        const desktopSidebarToggle = document.getElementById('desktopSidebarToggle');
        if (desktopSidebarToggle) {
            desktopSidebarToggle.addEventListener('click', function() {
                const html = document.documentElement;
                if (html.classList.contains('sidebar-collapsed')) {
                    html.classList.remove('sidebar-collapsed');
                    localStorage.sidebarCollapsed = 'false';
                } else {
                    html.classList.add('sidebar-collapsed');
                    localStorage.sidebarCollapsed = 'true';
                    // Close any open submenus
                    $('.submenu').slideUp(200);
                    $('.caret-icon').removeClass('rotate-90');
                }
            });
        }

        // Individual Menu Pinning Logic (Favorites)
        $(document).ready(function() {
            $('#main-sidebar-nav .submenu a').each(function() {
                $(this).addClass('relative pr-8 submenu-item-link');
                let url = $(this).attr('href');
                $(this).append(`
                    <span class="pin-menu-btn absolute right-2 top-1/2 transform -translate-y-1/2 text-slate-400 hover:text-white cursor-pointer z-10" data-url="${url}" title="Pin Menu" onclick="togglePinMenu(event, '${url}')">
                        <i class="ph ph-push-pin text-sm"></i>
                    </span>
                `);
            });
            renderPinnedMenus();
        });

        window.togglePinMenu = function(e, url) {
            if (e) {
                e.preventDefault();
                e.stopPropagation();
            }
            let pinned = JSON.parse(localStorage.getItem('pinnedMenus') || '[]');
            if (pinned.includes(url)) {
                pinned = pinned.filter(p => p !== url);
            } else {
                pinned.push(url);
            }
            localStorage.setItem('pinnedMenus', JSON.stringify(pinned));
            renderPinnedMenus();
        };

        window.renderPinnedMenus = function() {
            let pinned = JSON.parse(localStorage.getItem('pinnedMenus') || '[]');
            let $container = $('#pinned-menus-container');
            let $nav = $('#pinned-menus');
            $nav.empty();

            if (pinned.length === 0) {
                $container.addClass('hidden');
            } else {
                $container.removeClass('hidden');
                pinned.forEach(url => {
                    let $original = $(`#main-sidebar-nav a[href="${url}"]`);
                    if ($original.length) {
                        let $clone = $original.clone(true);
                        $clone.find('.pin-menu-btn').remove(); 
                        $clone.removeClass('submenu-item-link');
                        $clone.append(`
                            <span class="unpin-menu-btn absolute right-2 top-1/2 transform -translate-y-1/2 text-primary-400 hover:text-red-400 transition-colors cursor-pointer z-10" data-url="${url}" title="Unpin Menu" onclick="togglePinMenu(event, '${url}')">
                                <i class="ph-fill ph-push-pin text-sm"></i>
                            </span>
                        `);
                        $nav.append($clone);
                        
                        $original.find('.pin-menu-btn i').removeClass('ph').addClass('ph-fill text-primary-400');
                        $original.find('.pin-menu-btn').removeClass('text-slate-400 hover:text-white').addClass('hover:text-red-400').css('opacity', '1');
                    }
                });

                $('#main-sidebar-nav .submenu a').each(function() {
                    let u = $(this).attr('href');
                    if (!pinned.includes(u)) {
                        $(this).find('.pin-menu-btn i').removeClass('ph-fill text-primary-400').addClass('ph');
                        $(this).find('.pin-menu-btn').addClass('text-slate-400 hover:text-white').removeClass('hover:text-red-400').css('opacity', '');
                    }
                });
            }
        };

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
                title: '<?php echo __('common.logout_confirm_title') ?>',
                text: '<?php echo __('common.logout_confirm_text') ?>',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: '<?php echo __('common.yes_logout') ?>',
                cancelButtonText: '<?php echo __('common.cancel') ?>'
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
                    menus: {
                        title: '<?php echo __('common.main_menu') ?? 'เมนูระบบ / Navigation' ?>',
                        icon: 'ph-compass',
                        color: 'bg-indigo-50 text-indigo-500 dark:bg-indigo-900/20 dark:text-indigo-400',
                        getUrl: item => item.url,
                        formatName: item => highlight(item.title, query),
                        formatSub: item => highlight(item.description, query),
                        getIconHtml: item => `<div class="w-8 h-8 rounded-lg ${item.color || 'bg-indigo-50 text-indigo-500'} flex items-center justify-center flex-shrink-0"><i class="ph-fill ${item.icon || 'ph-compass'} text-lg"></i></div>`
                    },
                    users: {
                        title: '<?php echo __('common.user_management') ?>',
                        icon: 'ph-users-three',
                        color: 'bg-blue-50 text-blue-500 dark:bg-blue-900/20 dark:text-blue-400',
                        getUrl: item => `/users?search=${encodeURIComponent(item.username)}`,
                        formatName: item => `${highlight(item.username, query)} <span class="text-xs text-slate-400 dark:text-slate-500">(${highlight(item.email, query)})</span>`,
                        formatSub: item => `<span class="px-1.5 py-0.5 rounded text-[10px] uppercase font-bold tracking-wider ${item.status === 'active' ? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-950/30 dark:text-emerald-400' : 'bg-rose-50 text-rose-600 dark:bg-rose-950/30 dark:text-rose-400'}">${item.status}</span>`
                    },
                    roles: {
                        title: '<?php echo __('common.roles_permissions') ?>',
                        icon: 'ph-shield-check',
                        color: 'bg-purple-50 text-purple-500 dark:bg-purple-900/20 dark:text-purple-400',
                        getUrl: item => `/roles`,
                        formatName: item => highlight(item.name, query),
                        formatSub: item => highlight(item.description || '', query)
                    },
                    logs: {
                        title: '<?php echo __('common.activity_logs') ?>',
                        icon: 'ph-activity',
                        color: 'bg-emerald-50 text-emerald-500 dark:bg-emerald-900/20 dark:text-emerald-400',
                        getUrl: item => `/logs/activity`,
                        formatName: item => highlight(item.action, query),
                        formatSub: item => `<span class="text-slate-400">by</span> ${highlight(item.username || 'System', query)} <span class="text-[10px] text-slate-400 ml-2">${formatDate(item.created_at, true)}</span>`
                    },
                    translations: {
                        title: '<?php echo __('settings.trans_title') ?? 'Translations' ?>',
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
                        const iconBox = category.getIconHtml ? category.getIconHtml(item) : `
                            <div class="w-8 h-8 rounded-lg ${category.color} flex items-center justify-center flex-shrink-0">
                                <i class="ph-fill ${category.icon} text-lg"></i>
                            </div>
                        `;
                        link.innerHTML = `
                            ${iconBox}
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

        // Global Form Submit Loading State
        document.addEventListener('submit', function(e) {
            const form = e.target;
            if (form.hasAttribute('data-no-loading')) return;
            
            const submitBtn = form.querySelector('button[type="submit"], input[type="submit"]');
            if (submitBtn) {
                if (submitBtn.disabled) {
                    e.preventDefault();
                    return;
                }
                
                submitBtn.disabled = true;
                
                // Keep original text
                if (!submitBtn.dataset.originalContent) {
                    submitBtn.dataset.originalContent = submitBtn.innerHTML;
                }
                
                // Avoid messing with layout if it's not flex
                if (!submitBtn.classList.contains('flex')) {
                    submitBtn.classList.add('inline-flex', 'items-center', 'justify-center');
                }
                
                submitBtn.classList.add('opacity-80', 'cursor-not-allowed', 'pointer-events-none');
                submitBtn.innerHTML = `<i class="ph ph-circle-notch animate-spin mr-2 text-lg"></i> ${submitBtn.dataset.originalContent.replace(/(<([^>]+)>)/gi, '').trim()}`;
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
                <div class="relative flex items-center border-b border-gray-100 dark:border-slate-800 px-4 flex-shrink-0 bg-white dark:bg-slate-900">
                    <i class="ph ph-magnifying-glass text-slate-400 dark:text-slate-500 text-xl mr-3"></i>
                    <input type="text" id="globalSearchInput" placeholder="<?php echo __('common.search') ?>..." class="w-full bg-transparent border-0 py-4 text-gray-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:ring-0 text-base" autocomplete="off">
                    <button onclick="closeSearchPalette()" class="text-slate-400 dark:text-slate-500 hover:text-gray-600 dark:hover:text-gray-300 text-xs font-medium px-2 py-1 rounded-md border border-gray-200 dark:border-slate-800">ESC</button>
                </div>

                <!-- Search Results Area -->
                <div id="globalSearchResults" class="flex-1 overflow-y-auto p-4 space-y-4">
                    <!-- Quick Links / Default State -->
                    <div id="searchDefaultState" class="space-y-4">
                        <div class="flex items-center justify-between px-2">
                            <span class="text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider"><?php echo __('common.main_menu') ?></span>
                            <span class="text-[11px] text-slate-400 dark:text-slate-500">พิมพ์คำค้นหาหรือคลิกเลือกเมนูด้านล่าง</span>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                            <a href="/dashboard" class="flex items-center gap-3 p-2.5 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800/50 border border-slate-100 dark:border-slate-800/50 hover:border-slate-200 dark:hover:border-slate-700 transition-all">
                                <div class="w-8 h-8 rounded-lg bg-blue-50 dark:bg-blue-900/20 text-blue-500 flex items-center justify-center flex-shrink-0"><i class="ph ph-squares-four text-lg"></i></div>
                                <div>
                                    <div class="text-sm font-medium text-gray-800 dark:text-slate-200"><?php echo __('common.dashboard') ?></div>
                                    <div class="text-xs text-slate-400 dark:text-slate-500">ภาพรวมระบบและสถิติ</div>
                                </div>
                            </a>
                            <a href="/users" class="flex items-center gap-3 p-2.5 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800/50 border border-slate-100 dark:border-slate-800/50 hover:border-slate-200 dark:hover:border-slate-700 transition-all">
                                <div class="w-8 h-8 rounded-lg bg-sky-50 dark:bg-sky-900/20 text-sky-500 flex items-center justify-center flex-shrink-0"><i class="ph ph-users text-lg"></i></div>
                                <div>
                                    <div class="text-sm font-medium text-gray-800 dark:text-slate-200"><?php echo __('common.user_management') ?></div>
                                    <div class="text-xs text-slate-400 dark:text-slate-500">จัดการรายชื่อผู้ใช้และสถานะ</div>
                                </div>
                            </a>
                            <a href="/roles" class="flex items-center gap-3 p-2.5 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800/50 border border-slate-100 dark:border-slate-800/50 hover:border-slate-200 dark:hover:border-slate-700 transition-all">
                                <div class="w-8 h-8 rounded-lg bg-purple-50 dark:bg-purple-900/20 text-purple-500 flex items-center justify-center flex-shrink-0"><i class="ph ph-shield-check text-lg"></i></div>
                                <div>
                                    <div class="text-sm font-medium text-gray-800 dark:text-slate-200"><?php echo __('common.roles_permissions') ?></div>
                                    <div class="text-xs text-slate-400 dark:text-slate-500">กำหนดกลุ่มสิทธิ์และ RBAC</div>
                                </div>
                            </a>
                            <a href="/logs/activity" class="flex items-center gap-3 p-2.5 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800/50 border border-slate-100 dark:border-slate-800/50 hover:border-slate-200 dark:hover:border-slate-700 transition-all">
                                <div class="w-8 h-8 rounded-lg bg-teal-50 dark:bg-teal-900/20 text-teal-500 flex items-center justify-center flex-shrink-0"><i class="ph ph-activity text-lg"></i></div>
                                <div>
                                    <div class="text-sm font-medium text-gray-800 dark:text-slate-200"><?php echo __('common.activity_logs') ?></div>
                                    <div class="text-xs text-slate-400 dark:text-slate-500">ประวัติกิจกรรมและการเข้าใช้</div>
                                </div>
                            </a>
                            <a href="/logs/audit" class="flex items-center gap-3 p-2.5 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800/50 border border-slate-100 dark:border-slate-800/50 hover:border-slate-200 dark:hover:border-slate-700 transition-all">
                                <div class="w-8 h-8 rounded-lg bg-cyan-50 dark:bg-cyan-900/20 text-cyan-500 flex items-center justify-center flex-shrink-0"><i class="ph ph-file-search text-lg"></i></div>
                                <div>
                                    <div class="text-sm font-medium text-gray-800 dark:text-slate-200"><?php echo __('common.audit_logs') ?></div>
                                    <div class="text-xs text-slate-400 dark:text-slate-500">ตรวจสอบประวัติการแก้ไขข้อมูล</div>
                                </div>
                            </a>
                            <a href="/settings" class="flex items-center gap-3 p-2.5 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800/50 border border-slate-100 dark:border-slate-800/50 hover:border-slate-200 dark:hover:border-slate-700 transition-all">
                                <div class="w-8 h-8 rounded-lg bg-amber-50 dark:bg-amber-900/20 text-amber-500 flex items-center justify-center flex-shrink-0"><i class="ph ph-gear text-lg"></i></div>
                                <div>
                                    <div class="text-sm font-medium text-gray-800 dark:text-slate-200"><?php echo __('common.system_settings') ?></div>
                                    <div class="text-xs text-slate-400 dark:text-slate-500">การตั้งค่าระบบหลักและเว็บไซต์</div>
                                </div>
                            </a>
                            <a href="/profile" class="flex items-center gap-3 p-2.5 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800/50 border border-slate-100 dark:border-slate-800/50 hover:border-slate-200 dark:hover:border-slate-700 transition-all">
                                <div class="w-8 h-8 rounded-lg bg-violet-50 dark:bg-violet-900/20 text-violet-500 flex items-center justify-center flex-shrink-0"><i class="ph ph-user-circle text-lg"></i></div>
                                <div>
                                    <div class="text-sm font-medium text-gray-800 dark:text-slate-200"><?php echo __('profile.title') ?? 'โปรไฟล์ของฉัน' ?></div>
                                    <div class="text-xs text-slate-400 dark:text-slate-500">ข้อมูลส่วนตัวและรหัสผ่าน</div>
                                </div>
                            </a>
                            <a href="/profile/sessions" class="flex items-center gap-3 p-2.5 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800/50 border border-slate-100 dark:border-slate-800/50 hover:border-slate-200 dark:hover:border-slate-700 transition-all">
                                <div class="w-8 h-8 rounded-lg bg-indigo-50 dark:bg-indigo-900/20 text-indigo-500 flex items-center justify-center flex-shrink-0"><i class="ph ph-devices text-lg"></i></div>
                                <div>
                                    <div class="text-sm font-medium text-gray-800 dark:text-slate-200"><?php echo __('profile.active_sessions') ?? 'เซสชันที่ใช้งาน' ?></div>
                                    <div class="text-xs text-slate-400 dark:text-slate-500">จัดการอุปกรณ์ที่เข้าสู่ระบบ</div>
                                </div>
                            </a>
                        </div>

                        <!-- Quick Settings Badges -->
                        <div class="pt-2 border-t border-gray-100 dark:border-slate-800">
                            <div class="text-[11px] font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-2 px-1">ตั้งค่าด่วน (Settings Tabs)</div>
                            <div class="flex flex-wrap gap-1.5">
                                <a href="/settings/general" class="px-2.5 py-1 text-xs rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-primary-50 dark:hover:bg-primary-900/30 text-gray-600 dark:text-slate-300 hover:text-primary-600 dark:hover:text-primary-400 transition-colors inline-flex items-center">
                                    <i class="ph ph-sliders mr-1.5"></i> ทั่วไป
                                </a>
                                <a href="/settings/security" class="px-2.5 py-1 text-xs rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-primary-50 dark:hover:bg-primary-900/30 text-gray-600 dark:text-slate-300 hover:text-primary-600 dark:hover:text-primary-400 transition-colors inline-flex items-center">
                                    <i class="ph ph-lock-key mr-1.5"></i> ความปลอดภัย & 2FA
                                </a>
                                <a href="/settings/login" class="px-2.5 py-1 text-xs rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-primary-50 dark:hover:bg-primary-900/30 text-gray-600 dark:text-slate-300 hover:text-primary-600 dark:hover:text-primary-400 transition-colors inline-flex items-center">
                                    <i class="ph ph-paint-brush mr-1.5"></i> หน้าล็อกอิน
                                </a>
                                <a href="/settings/social" class="px-2.5 py-1 text-xs rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-primary-50 dark:hover:bg-primary-900/30 text-gray-600 dark:text-slate-300 hover:text-primary-600 dark:hover:text-primary-400 transition-colors inline-flex items-center">
                                    <i class="ph ph-share-network mr-1.5"></i> Google & LINE
                                </a>
                                <a href="/settings/translations" class="px-2.5 py-1 text-xs rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-primary-50 dark:hover:bg-primary-900/30 text-gray-600 dark:text-slate-300 hover:text-primary-600 dark:hover:text-primary-400 transition-colors inline-flex items-center">
                                    <i class="ph ph-translate mr-1.5"></i> แปลภาษา
                                </a>
                            </div>
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
