<?php
use App\Core\Settings;
use App\Core\Session;
?>
<!DOCTYPE html>
<html lang="<?= \App\Core\Lang::locale() ?>">
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
    <style>
        body { font-family: 'Prompt', sans-serif; }

        /* ── Custom Scrollbar ── */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 9999px;
        }
        ::-webkit-scrollbar-thumb:hover { background: #3b82f6; }
        .dark ::-webkit-scrollbar-thumb { background: #334155; }
        .dark ::-webkit-scrollbar-thumb:hover { background: #3b82f6; }
        * { scrollbar-width: thin; scrollbar-color: #cbd5e1 transparent; }
        .dark * { scrollbar-color: #334155 transparent; }
    </style>
</head>
<?php 
$bgImage = Settings::get('login_background'); 
$bgClass = $bgImage ? "bg-cover bg-center bg-no-repeat" : "bg-slate-50 dark:bg-slate-950";
$bgStyle = $bgImage ? "background-image: url('" . htmlspecialchars($bgImage) . "');" : "";
?>
<body class="<?= $bgClass ?> text-gray-800 dark:text-slate-200 antialiased min-h-screen flex items-center justify-center p-4 transition-colors duration-300 relative" style="<?= $bgStyle ?>">

    <!-- Top Floating Tools: Language + Dark Mode -->
    <?php $currentLocale = \App\Core\Lang::locale(); ?>
    <div class="absolute top-4 right-4 z-20 flex items-center space-x-2">
        <div class="flex items-center bg-white/80 dark:bg-slate-900/80 backdrop-blur-md rounded-lg p-1 text-xs font-medium border border-gray-200 dark:border-slate-800 shadow-sm">
            <a href="/lang/th" class="px-2 py-1 rounded-md <?= $currentLocale === 'th' ? 'bg-blue-600 text-white font-bold shadow-sm' : 'text-gray-600 dark:text-slate-400 hover:text-gray-900 dark:hover:text-white' ?> transition-all flex items-center">
                TH
            </a>
            <a href="/lang/en" class="px-2 py-1 rounded-md <?= $currentLocale === 'en' ? 'bg-blue-600 text-white font-bold shadow-sm' : 'text-gray-600 dark:text-slate-400 hover:text-gray-900 dark:hover:text-white' ?> transition-all flex items-center">
                EN
            </a>
        </div>
    </div>

    <?php if ($bgImage): ?>
        <!-- Overlay if there is a background image to ensure form readability -->
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm z-0"></div>
    <?php endif; ?>

    <div class="w-full max-w-md relative z-10">
        <!-- Logo / Branding -->
        <div class="text-center mb-8">
            <?php $logo = Settings::get('site_logo'); if ($logo): ?>
                <img src="<?= htmlspecialchars($logo) ?>" alt="Logo" class="h-20 w-auto mx-auto mb-4 drop-shadow-md">
            <?php else: ?>
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-blue-600 dark:bg-blue-500 text-white shadow-lg shadow-blue-500/30 dark:shadow-blue-500/20 mb-4">
                    <i class="ph-fill ph-cube text-4xl"></i>
                </div>
            <?php endif; ?>
            
            <?php $titleColor = $bgImage ? 'text-white drop-shadow' : 'text-gray-900 dark:text-white'; ?>
            <h1 class="text-2xl font-bold <?= $titleColor ?>">
                <?= htmlspecialchars(Settings::get('login_title', Settings::get('site_name', 'Standard Core System'))) ?>
            </h1>
            
            <?php $subtitleColor = $bgImage ? 'text-slate-200' : 'text-slate-500 dark:text-slate-400'; ?>
            <p class="<?= $subtitleColor ?> mt-2">
                <?= htmlspecialchars(Settings::get('login_subtitle', 'Sign in to your account')) ?>
            </p>
        </div>

        <!-- Flash Messages -->
        <?php if ($flashSuccess = Session::getFlash('success')): ?>
            <div class="mb-6 bg-emerald-50 border border-emerald-200 rounded-xl p-4 flex items-start shadow-sm">
                <i class="ph-fill ph-check-circle text-emerald-500 text-xl mr-3 mt-0.5"></i>
                <p class="text-sm text-emerald-800 font-medium"><?= htmlspecialchars($flashSuccess) ?></p>
            </div>
        <?php endif; ?>
        
        <?php if ($flashError = Session::getFlash('error')): ?>
            <div class="mb-6 bg-red-50 border border-red-200 rounded-xl p-4 flex items-start shadow-sm">
                <i class="ph-fill ph-warning-circle text-red-500 text-xl mr-3 mt-0.5"></i>
                <p class="text-sm text-red-800 font-medium"><?= htmlspecialchars($flashError) ?></p>
            </div>
        <?php endif; ?>

        <!-- Content -->
        <?= $content ?? '' ?>

        <!-- Footer -->
        <?php $footerColor = $bgImage ? 'text-white drop-shadow' : 'text-slate-500 dark:text-slate-400'; ?>
        <p class="text-center text-sm <?= $footerColor ?> mt-8 transition-colors">
            <?= Settings::get('site_footer', '&copy; ' . date('Y') . ' Standard Core System. All rights reserved.') ?>
        </p>
    </div>

</body>
</html>
