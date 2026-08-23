<!DOCTYPE html>
<html lang="<?= \App\Core\Lang::locale() ?>" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= __('common.maintenance') ?? 'System Maintenance' ?></title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Prompt', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    
    <!-- Phosphor Icons -->
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>
<body class="bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-200 min-h-screen flex items-center justify-center p-4 antialiased transition-colors duration-300">
    <div class="max-w-lg w-full text-center">
        <div class="mb-8 relative inline-block animate-bounce">
            <i class="ph-fill ph-wrench text-8xl text-blue-500 drop-shadow-lg"></i>
            <i class="ph-fill ph-gear text-4xl text-amber-500 absolute bottom-0 right-0 animate-spin-slow"></i>
        </div>
        
        <h1 class="text-3xl md:text-4xl font-bold mb-4 text-slate-900 dark:text-white">
            <?= __('common.maintenance_title') ?? 'System under maintenance' ?>
        </h1>
        
        <p class="text-slate-500 dark:text-slate-400 text-lg mb-8">
            <?= __('common.maintenance_desc') ?? 'We are currently updating the system to serve you better. Please check back later.' ?>
        </p>
        
        <div class="flex justify-center">
            <form action="/logout" method="POST" class="inline m-0 p-0">
                <input type="hidden" name="csrf_token" value="<?= \App\Core\Session::getCsrfToken() ?>">
                <button type="submit" class="px-6 py-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-600 dark:text-slate-300 font-medium hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors shadow-sm flex items-center">
                    <i class="ph ph-sign-out text-lg mr-2"></i> <?= __('common.logout') ?>
                </button>
            </form>
        </div>
    </div>
    
    <script>
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && prefersDark)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
</body>
</html>
