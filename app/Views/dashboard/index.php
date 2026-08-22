<!-- Stat Cards -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    
    <!-- Card 1 -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-800 p-6 flex items-center hover:shadow-md transition-shadow">
        <div class="w-14 h-14 rounded-full bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-blue-400">
            <i class="ph-fill ph-users text-2xl"></i>
        </div>
        <div class="ml-5">
            <p class="text-sm font-medium text-gray-500 dark:text-slate-400 mb-1"><?= __('dashboard.total_users') ?></p>
            <h3 class="text-2xl font-bold text-gray-800 dark:text-white"><?= number_format($stats['total_users'] ?? 0) ?></h3>
        </div>
    </div>

    <!-- Card 2 -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-800 p-6 flex items-center hover:shadow-md transition-shadow">
        <div class="w-14 h-14 rounded-full bg-emerald-50 dark:bg-emerald-900/30 flex items-center justify-center text-emerald-600 dark:text-emerald-400">
            <i class="ph-fill ph-user-check text-2xl"></i>
        </div>
        <div class="ml-5">
            <p class="text-sm font-medium text-gray-500 dark:text-slate-400 mb-1"><?= __('dashboard.active_users') ?></p>
            <h3 class="text-2xl font-bold text-gray-800 dark:text-white"><?= number_format($stats['active_users'] ?? 0) ?></h3>
        </div>
    </div>

    <!-- Card 3 -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-800 p-6 flex items-center hover:shadow-md transition-shadow">
        <div class="w-14 h-14 rounded-full bg-purple-50 dark:bg-purple-900/30 flex items-center justify-center text-purple-600 dark:text-purple-400">
            <i class="ph-fill ph-shield-star text-2xl"></i>
        </div>
        <div class="ml-5">
            <p class="text-sm font-medium text-gray-500 dark:text-slate-400 mb-1"><?= __('dashboard.total_roles') ?></p>
            <h3 class="text-2xl font-bold text-gray-800 dark:text-white"><?= number_format($stats['total_roles'] ?? 0) ?></h3>
        </div>
    </div>

</div>

<!-- Welcome Section -->
<div class="bg-gradient-to-r from-blue-600 to-indigo-600 dark:from-blue-700 dark:to-indigo-800 rounded-2xl shadow-lg p-8 text-white relative overflow-hidden">
    <div class="relative z-10 w-full md:w-2/3">
        <h2 class="text-3xl font-bold mb-3"><?= __('dashboard.welcome_title') ?></h2>
        <p class="text-blue-100 dark:text-blue-200 text-lg mb-6">
            <?= __('dashboard.welcome_desc') ?>
        </p>
        <button class="bg-white dark:bg-slate-800 text-blue-600 dark:text-blue-400 font-semibold px-6 py-2.5 rounded-lg hover:bg-blue-50 dark:hover:bg-slate-700 transition-colors shadow">
            <?= __('dashboard.read_docs') ?>
        </button>
    </div>
    
    <!-- Decorative background element -->
    <div class="absolute right-0 top-0 h-full w-1/3 opacity-20 pointer-events-none">
        <svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg" class="absolute -right-20 -top-20 w-96 h-96 fill-white">
            <path d="M44.7,-76.4C58.8,-69.2,71.8,-59.1,81.3,-46.3C90.8,-33.5,96.8,-18.1,95.5,-3.2C94.2,11.7,85.6,26.1,74.9,37.7C64.2,49.3,51.4,58.1,37.8,63.6C24.2,69.1,9.8,71.3,-5.4,72.4C-20.6,73.5,-36.6,73.5,-49.6,67.6C-62.6,61.7,-72.6,49.9,-81.4,36.4C-90.2,22.9,-97.8,7.7,-96.2,-6.7C-94.6,-21.1,-83.8,-34.7,-71.4,-45.5C-59,-56.3,-45,-64.3,-31.2,-71.2C-17.4,-78.1,-3.8,-83.9,10.6,-82.7C25,-81.5,44.7,-76.4,44.7,-76.4Z" transform="translate(100 100)" />
        </svg>
    </div>
</div>
