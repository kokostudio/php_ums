<!-- Stat Cards -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <?php if (isset($cards) && is_array($cards)): ?>
        <?php foreach ($cards as $card): ?>
            <div class="bg-white dark:bg-black/20 dark:backdrop-blur-md rounded-2xl shadow-sm border border-gray-100 dark:border-slate-800 p-6 flex items-center hover:shadow-md transition-shadow">
                <div class="w-14 h-14 rounded-full bg-<?= $card['color'] ?>-50 dark:bg-<?= $card['color'] ?>-900/30 flex items-center justify-center text-<?= $card['color'] ?>-600 dark:text-<?= $card['color'] ?>-400">
                    <i class="ph-fill <?= htmlspecialchars($card['icon']) ?> text-2xl"></i>
                </div>
                <div class="ml-5">
                    <p class="text-sm font-medium text-gray-500 dark:text-slate-400 mb-1"><?= htmlspecialchars($card['title']) ?></p>
                    <h3 class="text-2xl font-bold text-gray-800 dark:text-white"><?= htmlspecialchars($card['value']) ?></h3>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- System Info and Recent Activities Section -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <!-- System Info Widget -->
    <div class="col-span-1 bg-white dark:bg-black/20 dark:backdrop-blur-md rounded-2xl shadow-sm border border-gray-100 dark:border-slate-800 p-6 flex flex-col">
        <div class="flex items-center mb-6 pb-4 border-b border-gray-100 dark:border-slate-800">
            <div class="w-10 h-10 rounded-full bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-blue-400 mr-4">
                <i class="ph-fill ph-hard-drives text-xl"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-800 dark:text-white"><?= __('dashboard.system_info') ?? 'System Information' ?></h3>
        </div>
        
        <div class="flex-1 space-y-4">
            <div class="flex justify-between items-center">
                <span class="text-sm text-gray-500 dark:text-slate-400 flex items-center"><i class="ph ph-file-code mr-2"></i> <?= __('dashboard.php_version') ?? 'PHP Version' ?></span>
                <span class="text-sm font-semibold text-gray-800 dark:text-slate-200"><?= htmlspecialchars($systemInfo['php_version'] ?? 'N/A') ?></span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-sm text-gray-500 dark:text-slate-400 flex items-center"><i class="ph ph-database mr-2"></i> <?= __('dashboard.db_version') ?? 'DB Version' ?></span>
                <span class="text-sm font-semibold text-gray-800 dark:text-slate-200"><?= htmlspecialchars($systemInfo['db_version'] ?? 'N/A') ?></span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-sm text-gray-500 dark:text-slate-400 flex items-center"><i class="ph ph-desktop mr-2"></i> <?= __('dashboard.os') ?? 'OS' ?></span>
                <span class="text-sm font-semibold text-gray-800 dark:text-slate-200"><?= htmlspecialchars($systemInfo['os'] ?? 'N/A') ?></span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-sm text-gray-500 dark:text-slate-400 flex items-center"><i class="ph ph-hard-drive mr-2"></i> <?= __('dashboard.server') ?? 'Server' ?></span>
                <span class="text-sm font-semibold text-gray-800 dark:text-slate-200 truncate max-w-[120px]" title="<?= htmlspecialchars($systemInfo['server'] ?? '') ?>"><?= htmlspecialchars($systemInfo['server'] ?? 'N/A') ?></span>
            </div>
        </div>
        
        <div class="mt-6 pt-4 border-t border-gray-100 dark:border-slate-800 flex justify-between items-center">
            <span class="text-xs text-gray-400 dark:text-slate-500"><?= __('dashboard.last_updated') ?? 'Last updated:' ?></span>
            <span class="text-xs font-mono text-gray-500 dark:text-slate-400"><?= formatDate($systemInfo['time'] ?? '', true, true) ?></span>
        </div>
    </div>

    <!-- Recent Activities Widget -->
    <div class="col-span-1 lg:col-span-2 bg-white dark:bg-black/20 dark:backdrop-blur-md rounded-2xl shadow-sm border border-gray-100 dark:border-slate-800 p-6 flex flex-col">
        <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-100 dark:border-slate-800">
            <div class="flex items-center">
                <div class="w-10 h-10 rounded-full bg-emerald-50 dark:bg-emerald-900/30 flex items-center justify-center text-emerald-600 dark:text-emerald-400 mr-4">
                    <i class="ph-fill ph-activity text-xl"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-800 dark:text-white"><?= __('dashboard.recent_activity') ?? 'Recent Activity' ?></h3>
            </div>
            <a href="/logs/activity" class="text-sm text-blue-600 dark:text-blue-400 hover:underline"><?= __('common.view_all') ?? 'View All' ?></a>
        </div>
        
        <div class="flex-1 overflow-y-auto pr-2" style="max-height: 250px;">
            <?php if (!empty($recentActivities)): ?>
                <div class="space-y-4">
                    <?php foreach ($recentActivities as $activity): ?>
                        <div class="flex items-start">
                            <div class="flex-shrink-0 mt-1">
                                <?php if (!empty($activity['profile_image'])): ?>
                                    <img src="<?= htmlspecialchars($activity['profile_image']) ?>" class="w-8 h-8 rounded-full border border-gray-200 dark:border-slate-700 object-cover">
                                <?php else: ?>
                                    <div class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-500 font-bold text-xs">
                                        <?= strtoupper(substr($activity['username'] ?? 'U', 0, 1)) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="ml-3 flex-1">
                                <p class="text-sm font-medium text-gray-800 dark:text-slate-200">
                                    <?= htmlspecialchars($activity['username'] ?? __('common.system') ?? 'System') ?>
                                </p>
                                <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">
                                    <?= htmlspecialchars($activity['action'] ?? '') ?>
                                </p>
                            </div>
                            <div class="ml-3 flex-shrink-0 text-right">
                                <span class="text-[10px] text-gray-400 dark:text-slate-500 bg-gray-50 dark:bg-slate-800/50 px-2 py-1 rounded">
                                    <?= formatDate($activity['created_at'], true) ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="h-full flex flex-col items-center justify-center text-gray-400 dark:text-slate-500">
                    <i class="ph ph-mask-sad text-4xl mb-2 opacity-50"></i>
                    <p class="text-sm"><?= __('dashboard.no_recent_activity') ?? 'No recent activities found.' ?></p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Charts Section -->
<div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-6">
    <?php if (isset($charts) && is_array($charts)): ?>
        <?php foreach ($charts as $chart): ?>
            <div class="bg-white dark:bg-black/20 dark:backdrop-blur-md rounded-2xl shadow-sm border border-gray-100 dark:border-slate-800 p-6">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4"><?= htmlspecialchars($chart['title']) ?></h3>
                <div class="w-full h-64 relative">
                    <canvas id="<?= htmlspecialchars($chart['id']) ?>"></canvas>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const isDarkMode = document.documentElement.classList.contains('dark');
        const textColor = isDarkMode ? '#94a3b8' : '#64748b';
        const gridColor = isDarkMode ? '#1e293b' : '#f1f5f9';

        <?php if (isset($charts) && is_array($charts)): ?>
            <?php foreach ($charts as $chart): ?>
                (function() {
                    const ctx = document.getElementById('<?= htmlspecialchars($chart['id']) ?>');
                    if (!ctx) return;
                    
                    const datasets = <?= json_encode($chart['datasets']) ?>.map(ds => {
                        // Tailwind blue-500 equivalent as default
                        let colorHex = '#3b82f6';
                        let bgColorHex = 'rgba(59, 130, 246, 0.1)';
                        
                        if (ds.color === 'emerald') { colorHex = '#10b981'; bgColorHex = 'rgba(16, 185, 129, 0.1)'; }
                        else if (ds.color === 'purple') { colorHex = '#a855f7'; bgColorHex = 'rgba(168, 85, 247, 0.1)'; }
                        else if (ds.color === 'multi') {
                            // Provide an array of colors for pie/doughnut charts
                            colorHex = ['#3b82f6', '#10b981', '#a855f7', '#f43f5e', '#f59e0b', '#06b6d4'];
                            bgColorHex = ['#3b82f680', '#10b98180', '#a855f780', '#f43f5e80', '#f59e0b80', '#06b6d480'];
                        }
                        
                        return {
                            label: ds.label,
                            data: ds.data,
                            borderColor: ds.color !== 'multi' ? colorHex : '#ffffff00', // transparent border for pie
                            backgroundColor: ds.color !== 'multi' ? bgColorHex : colorHex, // solid colors for pie
                            borderWidth: 2,
                            tension: 0.4,
                            fill: true,
                            pointBackgroundColor: colorHex,
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            pointRadius: 4,
                            pointHoverRadius: 6
                        };
                    });

                        const isPieOrDoughnut = ['pie', 'doughnut'].includes('<?= htmlspecialchars($chart['type'] ?? 'line') ?>');
                        
                        const chartOptions = {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: true,
                                    position: isPieOrDoughnut ? 'right' : 'top'
                                }
                            }
                        };
                        
                        if (!isPieOrDoughnut) {
                            chartOptions.scales = {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        color: textColor,
                                        precision: 0
                                    },
                                    grid: {
                                        color: gridColor,
                                        drawBorder: false
                                    }
                                },
                                x: {
                                    ticks: {
                                        color: textColor
                                    },
                                    grid: {
                                        display: false,
                                        drawBorder: false
                                    }
                                }
                            };
                        }

                        new Chart(ctx.getContext('2d'), {
                            type: '<?= htmlspecialchars($chart['type'] ?? 'line') ?>',
                            data: {
                                labels: <?= json_encode($chart['labels'] ?? []) ?>,
                                datasets: datasets
                            },
                            options: chartOptions
                        });
                })();
            <?php endforeach; ?>
        <?php endif; ?>
    });
</script>
