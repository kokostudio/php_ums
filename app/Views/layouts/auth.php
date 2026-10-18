<?php
use App\Core\Settings;
use App\Core\Session;

$sidebarTheme = Settings::get('sidebar_theme', 'slate');
$primaryHex = [
    'slate'   => '#64748b',
    'black'   => '#737373',
    'indigo'  => '#6366f1',
    'emerald' => '#10b981',
    'rose'    => '#f43f5e',
][$sidebarTheme] ?? '#64748b';
?>
<!DOCTYPE html>
<html lang="<?= \App\Core\Lang::locale() ?>" class="dark bg-slate-50 dark:bg-[#0f172a] <?= htmlspecialchars(Settings::get('base_font_size', 'text-[16px]')) ?>"> <!-- Added dark as default fallback if needed, but script will override -->
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars(Settings::get('site_name', 'System')) ?> - <?= $pageTitle ?? __('auth.login_title') ?></title>
    
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
                        sidebar: '#0f172a',
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
    <style>
        body { font-family: 'Prompt', sans-serif; }

        /* ── Custom Scrollbar ── */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb {
            background: <?= $primaryHex ?>80;
            border-radius: 9999px;
            transition: background 0.2s;
        }
        ::-webkit-scrollbar-thumb:hover { background: <?= $primaryHex ?>; }
        .dark ::-webkit-scrollbar-thumb { background: <?= $primaryHex ?>66; }
        .dark ::-webkit-scrollbar-thumb:hover { background: <?= $primaryHex ?>; }
        * { scrollbar-width: thin; scrollbar-color: <?= $primaryHex ?>80 transparent; }
        .dark * { scrollbar-color: <?= $primaryHex ?>66 transparent; }
    </style>
</head>
<?php 
$bgImage = Settings::get('login_background'); 
$bgClass = $bgImage ? "bg-cover bg-center bg-no-repeat" : "bg-slate-900";
$bgStyle = $bgImage ? "background-image: url('" . htmlspecialchars($bgImage) . "');" : "";
$currentLocale = \App\Core\Lang::locale();
?>
<body class="antialiased min-h-screen flex flex-col md:flex-row transition-colors duration-300">

    <?php require __DIR__ . '/partials/progress_bar.php'; ?>

    <!-- LEFT PANEL (BRANDING) -->
    <div class="w-full md:w-5/12 lg:w-1/2 relative flex flex-col justify-between p-8 md:p-16 order-2 md:order-1 min-h-[50vh] md:min-h-screen <?= $bgClass ?>" style="<?= $bgStyle ?>">
        <!-- Background Overlay -->
        <div class="absolute inset-0 bg-slate-900/80 md:bg-blue-950/80 z-0"></div>
        
        <!-- Content -->
        <div class="relative z-10 flex-1 flex flex-col justify-center">
            <!-- Logo -->
            <div class="flex items-center space-x-3 mb-10">
                <?php 
                $logo = Settings::get('login_logo') ?: Settings::get('site_logo'); 
                $logoSizeClass = Settings::get('login_logo_size', 'h-12');
                if ($logo): ?>
                    <img src="<?= htmlspecialchars($logo) ?>" alt="Logo" class="<?= $logoSizeClass ?> w-auto drop-shadow-md transition-all duration-300">
                <?php else: ?>
                    <div class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-blue-600 text-white shadow-lg shadow-blue-900/20">
                        <i class="ph-fill ph-cube text-2xl"></i>
                    </div>
                <?php endif; ?>
                <div>
                    <h1 class="<?= htmlspecialchars(Settings::get('login_site_name_size', 'text-xl')) ?> font-bold text-white tracking-wide"><?= htmlspecialchars(Settings::get('site_name', 'WorkPOS')) ?></h1>
                    <p class="text-[10px] text-blue-300 uppercase font-bold tracking-widest"><?= htmlspecialchars(Settings::get('login_edition', 'ERP EDITION')) ?></p>
                </div>
            </div>
            
            <h2 class="<?= htmlspecialchars(Settings::get('login_title_size', 'text-4xl md:text-5xl')) ?> font-extrabold text-white mb-2 leading-tight"><?= htmlspecialchars(Settings::get('login_title', 'จัดการร้านค้า')) ?><br><span class="text-blue-400"><?= htmlspecialchars(Settings::get('site_name', 'WorkPOS')) ?></span></h2>
            <p class="text-blue-200/80 mb-12 text-sm"><?= htmlspecialchars(Settings::get('login_subtitle', 'เข้าสู่ระบบศูนย์กลาง ' . Settings::get('site_name', 'WorkPOS'))) ?></p>
            
            <?php if (Settings::get('login_features_enabled', '1') == '1'): ?>
            <!-- Features -->
            <div class="space-y-4 max-w-sm">
                <div class="flex items-center space-x-4 bg-white/5 backdrop-blur-md border border-white/10 rounded-xl p-4 text-white hover:bg-white/10 transition-colors">
                    <div class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center flex-shrink-0">
                        <i class="ph <?= htmlspecialchars(Settings::get('login_feature1_icon', 'ph-shield-check')) ?> text-xl"></i>
                    </div>
                    <span class="text-sm font-medium"><?= htmlspecialchars(Settings::get('login_feature1_text', 'เข้าสู่ระบบปลอดภัยด้วย SSO')) ?></span>
                </div>
                <div class="flex items-center space-x-4 bg-white/5 backdrop-blur-md border border-white/10 rounded-xl p-4 text-white hover:bg-white/10 transition-colors">
                    <div class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center flex-shrink-0">
                        <i class="ph <?= htmlspecialchars(Settings::get('login_feature2_icon', 'ph-package')) ?> text-xl"></i>
                    </div>
                    <span class="text-sm font-medium"><?= htmlspecialchars(Settings::get('login_feature2_text', 'จัดการสินค้า สต็อก และลูกค้า')) ?></span>
                </div>
                <div class="flex items-center space-x-4 bg-white/5 backdrop-blur-md border border-white/10 rounded-xl p-4 text-white hover:bg-white/10 transition-colors">
                    <div class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center flex-shrink-0">
                        <i class="ph <?= htmlspecialchars(Settings::get('login_feature3_icon', 'ph-chart-line-up')) ?> text-xl"></i>
                    </div>
                    <span class="text-sm font-medium"><?= htmlspecialchars(Settings::get('login_feature3_text', 'รายงานและแดชบอร์ดแบบเรียลไทม์')) ?></span>
                </div>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Footer -->
        <div class="relative z-10 mt-12 text-xs text-white/50">
            <?= Settings::get('site_footer', '&copy; ' . date('Y') . ' ' . Settings::get('site_name', 'WorkPOS') . ' ERP — ระบบจัดการร้านค้าครบวงจร') ?>
        </div>
    </div>

    <!-- RIGHT PANEL (LOGIN FORM) -->
    <div class="w-full md:w-7/12 lg:w-1/2 flex flex-col min-h-screen relative bg-slate-50 dark:bg-[#0f172a] order-1 md:order-2 z-10 shadow-2xl md:shadow-none overflow-y-auto overflow-x-hidden">
        
        <!-- Premium Background Patterns -->
        <?php $pattern = Settings::get('login_pattern', 'dots'); ?>
        
        <?php if ($pattern === 'dots'): ?>
        <!-- 1. Dots & Orbs -->
        <div class="absolute inset-0 pointer-events-none opacity-60 dark:opacity-40" style="background-image: radial-gradient(circle, rgba(59, 130, 246, 0.3) 1.5px, transparent 1.5px); background-size: 24px 24px;"></div>
        <div class="absolute -top-40 -right-40 w-[32rem] h-[32rem] bg-blue-500/20 dark:bg-blue-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-40 -left-40 w-[32rem] h-[32rem] bg-purple-500/20 dark:bg-purple-500/10 rounded-full blur-3xl pointer-events-none"></div>
        
        <?php elseif ($pattern === 'grid'): ?>
        <!-- 2. Minimal Grid -->
        <div class="absolute inset-0 pointer-events-none opacity-40 dark:opacity-30" style="background-image: linear-gradient(rgba(100, 116, 139, 0.3) 1px, transparent 1px), linear-gradient(90deg, rgba(100, 116, 139, 0.3) 1px, transparent 1px); background-size: 32px 32px;"></div>
        <!-- Fade out edges -->
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_center,_transparent_0%,_#f8fafc_100%)] dark:bg-[radial-gradient(circle_at_center,_transparent_0%,_#0f172a_100%)] pointer-events-none"></div>
        
        <?php elseif ($pattern === 'mesh'): ?>
        <!-- 3. Soft Mesh -->
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute top-[-10%] left-[-10%] w-[50%] h-[50%] bg-blue-400/20 dark:bg-blue-600/10 rounded-full blur-[100px]"></div>
            <div class="absolute top-[20%] right-[-10%] w-[60%] h-[60%] bg-cyan-300/20 dark:bg-cyan-600/10 rounded-full blur-[120px]"></div>
            <div class="absolute bottom-[-20%] left-[20%] w-[60%] h-[60%] bg-indigo-400/20 dark:bg-indigo-600/10 rounded-full blur-[100px]"></div>
        </div>

        <?php elseif ($pattern === 'lines'): ?>
        <!-- 4. Diagonal Lines -->
        <div class="absolute inset-0 pointer-events-none opacity-30 dark:opacity-20" style="background-image: repeating-linear-gradient(-45deg, rgba(100, 116, 139, 0.3) 0, rgba(100, 116, 139, 0.3) 1px, transparent 1px, transparent 16px);"></div>
        <div class="absolute inset-0 bg-gradient-to-t from-slate-50 via-slate-50/80 to-transparent dark:from-[#0f172a] dark:via-[#0f172a]/80 pointer-events-none"></div>

        <?php elseif ($pattern === 'glass'): ?>
        <!-- 5. Glassmorphism -->
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute top-1/4 left-1/4 w-64 h-64 bg-blue-500/20 dark:bg-blue-600/20 rounded-full mix-blend-multiply dark:mix-blend-screen filter blur-xl"></div>
            <div class="absolute top-1/3 right-1/4 w-72 h-72 bg-purple-500/20 dark:bg-purple-600/20 rounded-full mix-blend-multiply dark:mix-blend-screen filter blur-xl"></div>
            <div class="absolute bottom-1/4 left-1/3 w-80 h-80 bg-indigo-500/20 dark:bg-indigo-600/20 rounded-full mix-blend-multiply dark:mix-blend-screen filter blur-xl"></div>
            <div class="absolute inset-0 bg-white/40 dark:bg-[#0f172a]/40 backdrop-blur-[60px] border-l border-white/20 dark:border-white/5"></div>
        </div>
        <?php endif; ?>
        
        <!-- Top Toolbar -->
        <div class="flex items-center justify-end p-6 space-x-4 relative z-10">
            <!-- Language Toggle -->
            <?php 
                $enableLangSwitcher = Settings::get('enable_language_switcher', '1');
                $availLangsStr = Settings::get('available_languages', 'th,en');
                $availLangs = $availLangsStr !== '' ? explode(',', $availLangsStr) : [];
            ?>
            <?php if ($enableLangSwitcher == '1' && count($availLangs) > 0): ?>
            <div class="flex items-center bg-gray-200/50 dark:bg-slate-800/80 rounded-lg p-1 text-[11px] font-medium border border-gray-300/50 dark:border-slate-700 transition-colors">
                <?php if (in_array('th', $availLangs)): ?>
                <a href="/lang/th" class="px-3 py-1 rounded-md <?= $currentLocale === 'th' ? 'bg-white dark:bg-slate-600 text-gray-900 dark:text-white shadow-sm' : 'text-gray-500 dark:text-slate-400 hover:text-gray-700 dark:hover:text-slate-300' ?> transition-colors">ไทย</a>
                <?php endif; ?>
                <?php if (in_array('en', $availLangs)): ?>
                <a href="/lang/en" class="px-3 py-1 rounded-md <?= $currentLocale === 'en' ? 'bg-white dark:bg-slate-600 text-gray-900 dark:text-white shadow-sm' : 'text-gray-500 dark:text-slate-400 hover:text-gray-700 dark:hover:text-slate-300' ?> transition-colors">EN</a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            
            <!-- Theme Toggle -->
            <button onclick="toggleTheme()" class="text-gray-400 hover:text-gray-600 dark:text-slate-400 dark:hover:text-slate-200 transition-colors w-8 h-8 flex items-center justify-center rounded-md bg-white/50 dark:bg-slate-800/50 backdrop-blur-sm border border-gray-200/50 dark:border-slate-700/50 shadow-sm">
                <i id="themeIcon" class="ph ph-sun text-lg"></i>
            </button>
        </div>

        <!-- Form Container -->
        <div class="flex-1 flex flex-col justify-center items-center px-6 pb-12 w-full max-w-md mx-auto relative z-10">
            
            <!-- SweetAlert2 -->
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            
            <!-- Flash Messages -->
            <?php if ($flashSuccess = Session::getFlash('success')): ?>
                <script>
                    document.addEventListener("DOMContentLoaded", function() {
                        const Toast = Swal.mixin({
                            toast: true, position: 'top', showConfirmButton: false, timer: 3000, timerProgressBar: true,
                            customClass: { popup: 'dark:bg-slate-800 dark:text-white border border-gray-100 dark:border-slate-700 shadow-xl', title: 'text-[13px] font-medium font-sans whitespace-nowrap px-2' }
                        });
                        Toast.fire({ icon: 'success', title: <?= json_encode(htmlspecialchars($flashSuccess)) ?> });
                    });
                </script>
            <?php endif; ?>
            
            <?php if ($flashError = Session::getFlash('error')): ?>
                <script>
                    document.addEventListener("DOMContentLoaded", function() {
                        const Toast = Swal.mixin({
                            toast: true, position: 'top', showConfirmButton: false, timer: 4000, timerProgressBar: true,
                            customClass: { popup: 'dark:bg-slate-800 dark:text-white border border-gray-100 dark:border-slate-700 shadow-xl', title: 'text-[13px] font-medium font-sans whitespace-nowrap px-2' }
                        });
                        Toast.fire({ icon: 'error', title: <?= json_encode(htmlspecialchars($flashError)) ?> });
                    });
                </script>
            <?php endif; ?>

            <!-- Content (login.php) -->
            <div class="w-full">
                <?= $content ?? '' ?>
            </div>
        </div>
    </div>

    <!-- Theme Toggle Logic -->
    <script>
        const themeIcon = document.getElementById('themeIcon');
        
        function updateThemeIcon() {
            if (document.documentElement.classList.contains('dark')) {
                themeIcon.classList.remove('ph-moon');
                themeIcon.classList.add('ph-sun');
            } else {
                themeIcon.classList.remove('ph-sun');
                themeIcon.classList.add('ph-moon');
            }
        }
        
        function toggleTheme() {
            if (document.documentElement.classList.contains('dark')) {
                document.documentElement.classList.remove('dark');
                localStorage.theme = 'light';
            } else {
                document.documentElement.classList.add('dark');
                localStorage.theme = 'dark';
            }
            updateThemeIcon();
        }
        
        // Initial setup
        updateThemeIcon();
    </script>

    <!-- Global Form Submit Loading State -->
    <script>
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
                
                if (!submitBtn.dataset.originalContent) {
                    submitBtn.dataset.originalContent = submitBtn.innerHTML;
                }
                
                if (!submitBtn.classList.contains('flex')) {
                    submitBtn.classList.add('inline-flex', 'items-center', 'justify-center');
                }
                
                submitBtn.classList.add('opacity-80', 'cursor-not-allowed', 'pointer-events-none');
                submitBtn.innerHTML = `<i class="ph ph-circle-notch animate-spin mr-2 text-lg"></i> ${submitBtn.dataset.originalContent.replace(/(<([^>]+)>)/gi, '').trim()}`;
            }
        });
    </script>
</body>
</html>
