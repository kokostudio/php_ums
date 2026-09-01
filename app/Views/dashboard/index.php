<?php
use App\Core\Session;
$username = Session::get('user_username') ?? 'Admin';
?>



<!-- Stat Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">
    <?php if (isset($cards) && is_array($cards)): ?>
        <?php foreach ($cards as $card): ?>
            <div class="group bg-white dark:bg-slate-900/50 dark:backdrop-blur-xl rounded-3xl shadow-sm hover:shadow-xl border border-slate-100 dark:border-slate-800/60 p-6 sm:p-8 flex items-center transition-all duration-300 hover:-translate-y-1 relative overflow-hidden">
                <!-- Hover Glow Effect -->
                <div class="absolute -inset-2 bg-gradient-to-r from-<?= $card['color'] ?>-50 to-<?= $card['color'] ?>-100 dark:from-<?= $card['color'] ?>-900/20 dark:to-<?= $card['color'] ?>-800/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none blur-lg"></div>
                
                <div class="relative z-10 w-16 h-16 rounded-2xl bg-<?= $card['color'] ?>-50 dark:bg-<?= $card['color'] ?>-500/10 flex items-center justify-center text-<?= $card['color'] ?>-600 dark:text-<?= $card['color'] ?>-400 group-hover:scale-110 transition-transform duration-300 shadow-sm">
                    <i class="ph-fill <?= htmlspecialchars($card['icon']) ?> text-3xl"></i>
                </div>
                <div class="relative z-10 ml-6">
                    <p class="text-sm font-semibold text-slate-500 dark:text-slate-400 mb-1 uppercase tracking-wider"><?= htmlspecialchars($card['title']) ?></p>
                    <h3 class="text-3xl font-black text-slate-800 dark:text-white tracking-tight"><?= htmlspecialchars($card['value']) ?></h3>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Charts Section -->
<div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <?php if (isset($charts) && is_array($charts)): ?>
        <?php foreach ($charts as $chart): ?>
            <div class="bg-white dark:bg-slate-900/50 dark:backdrop-blur-xl rounded-3xl shadow-sm border border-slate-100 dark:border-slate-800/60 p-6 sm:p-8 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-slate-800 dark:text-white"><?= htmlspecialchars($chart['title']) ?></h3>
                    <div class="p-2 bg-slate-50 dark:bg-slate-800 rounded-lg">
                        <i class="ph-bold ph-chart-<?= $chart['type'] === 'line' ? 'line-up' : 'pie-slice' ?> text-slate-400"></i>
                    </div>
                </div>
                <div class="w-full h-72 relative">
                    <canvas id="<?= htmlspecialchars($chart['id']) ?>"></canvas>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- System Info and Recent Activities Section -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    
    <!-- System Info Widget -->
    <div class="col-span-1 bg-white dark:bg-slate-900/50 dark:backdrop-blur-xl rounded-3xl shadow-sm border border-slate-100 dark:border-slate-800/60 p-6 sm:p-8 flex flex-col hover:shadow-md transition-shadow relative overflow-hidden">
        <!-- Abstract shape -->
        <div class="absolute -right-10 -top-10 w-40 h-40 bg-blue-50 dark:bg-blue-900/10 rounded-full blur-2xl opacity-50"></div>
        
        <div class="flex items-center mb-8 relative z-10">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-blue-100 to-blue-50 dark:from-blue-900/40 dark:to-blue-800/20 flex items-center justify-center text-blue-600 dark:text-blue-400 mr-4 shadow-sm border border-blue-100 dark:border-blue-800/30">
                <i class="ph-fill ph-hard-drives text-2xl"></i>
            </div>
            <h3 class="text-xl font-bold text-slate-800 dark:text-white"><?= __('dashboard.system_info') ?? 'System Information' ?></h3>
        </div>
        
        <div class="flex-1 space-y-5 relative z-10">
            <div class="flex items-center p-4 rounded-2xl bg-white dark:bg-slate-800/80 border border-gray-100 dark:border-slate-700 hover:shadow-md transition-all group">
                <div class="flex-shrink-0 w-10 h-10 rounded-xl bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center mr-4 group-hover:scale-110 transition-transform">
                    <i class="ph-fill ph-monitor text-xl"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider mb-1"><?= __('dashboard.os') ?? 'OS' ?></p>
                    <p class="text-sm font-medium text-gray-800 dark:text-slate-200 truncate group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                        <?= htmlspecialchars($systemInfo['os']) ?>
                    </p>
                </div>
            </div>
            
            <div class="flex items-center p-4 rounded-2xl bg-white dark:bg-slate-800/80 border border-gray-100 dark:border-slate-700 hover:shadow-md transition-all group">
                <div class="flex-shrink-0 w-10 h-10 rounded-xl bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 flex items-center justify-center mr-4 group-hover:scale-110 transition-transform">
                    <i class="ph-fill ph-file-code text-xl"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider mb-1"><?= __('dashboard.php_version') ?? 'PHP Version' ?></p>
                    <p class="text-sm font-medium text-gray-800 dark:text-slate-200 truncate group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                        <?= htmlspecialchars($systemInfo['php_version']) ?>
                    </p>
                </div>
            </div>
            
            <div class="flex items-center p-4 rounded-2xl bg-white dark:bg-slate-800/80 border border-gray-100 dark:border-slate-700 hover:shadow-md transition-all group">
                <div class="flex-shrink-0 w-10 h-10 rounded-xl bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 flex items-center justify-center mr-4 group-hover:scale-110 transition-transform">
                    <i class="ph-fill ph-hard-drives text-xl"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider mb-1"><?= __('dashboard.server') ?? 'Server' ?></p>
                    <p class="text-sm font-medium text-gray-800 dark:text-slate-200 truncate group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">
                        <?= htmlspecialchars($systemInfo['server']) ?>
                    </p>
                </div>
            </div>
            
            <div class="flex items-center p-4 rounded-2xl bg-white dark:bg-slate-800/80 border border-gray-100 dark:border-slate-700 hover:shadow-md transition-all group">
                <div class="flex-shrink-0 w-10 h-10 rounded-xl bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400 flex items-center justify-center mr-4 group-hover:scale-110 transition-transform">
                    <i class="ph-fill ph-database text-xl"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider mb-1"><?= __('dashboard.db_driver') ?? 'DB Driver' ?></p>
                    <p class="text-sm font-medium text-gray-800 dark:text-slate-200 truncate group-hover:text-orange-600 dark:group-hover:text-orange-400 transition-colors">
                        <?= htmlspecialchars($systemInfo['db_version'] ?? 'MySQL') ?>
                    </p>
                </div>
            </div>
            
            <div class="flex items-center p-4 rounded-2xl bg-white dark:bg-slate-800/80 border border-gray-100 dark:border-slate-700 hover:shadow-md transition-all group">
                <div class="flex-shrink-0 w-10 h-10 rounded-xl bg-teal-100 dark:bg-teal-900/30 text-teal-600 dark:text-teal-400 flex items-center justify-center mr-4 group-hover:scale-110 transition-transform">
                    <i class="ph-fill ph-hard-drive text-xl"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider mb-1"><?= __('dashboard.disk_free') ?? 'Disk Free' ?></p>
                    <p class="text-sm font-medium text-gray-800 dark:text-slate-200 truncate group-hover:text-teal-600 dark:group-hover:text-teal-400 transition-colors">
                        <?= htmlspecialchars($systemInfo['disk_free']) ?>
                    </p>
                </div>
            </div>
            
            <div class="flex items-center p-4 rounded-2xl bg-white dark:bg-slate-800/80 border border-gray-100 dark:border-slate-700 hover:shadow-md transition-all group">
                <div class="flex-shrink-0 w-10 h-10 rounded-xl bg-pink-100 dark:bg-pink-900/30 text-pink-600 dark:text-pink-400 flex items-center justify-center mr-4 group-hover:scale-110 transition-transform">
                    <i class="ph-fill ph-memory text-xl"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider mb-1"><?= __('dashboard.memory_limit') ?? 'Memory Limit' ?></p>
                    <p class="text-sm font-medium text-gray-800 dark:text-slate-200 truncate group-hover:text-pink-600 dark:group-hover:text-pink-400 transition-colors">
                        <?= htmlspecialchars($systemInfo['memory_limit']) ?>
                    </p>
                </div>
            </div>
        </div>
        
        <div class="mt-8 pt-5 border-t border-slate-100 dark:border-slate-800/60 flex justify-between items-center relative z-10">
            <span class="text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500"><?= __('dashboard.last_updated') ?? 'Last updated' ?></span>
            <span class="text-xs font-bold font-mono text-blue-500 bg-blue-50 dark:bg-blue-900/30 px-2 py-1 rounded"><?= formatDate($systemInfo['time'] ?? '', true, true) ?></span>
        </div>
    </div>

    <!-- Recent Activities Widget -->
    <div class="col-span-1 lg:col-span-2 bg-white dark:bg-slate-900/50 dark:backdrop-blur-xl rounded-3xl shadow-sm border border-slate-100 dark:border-slate-800/60 p-6 sm:p-8 flex flex-col hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-8">
            <div class="flex items-center">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-emerald-100 to-emerald-50 dark:from-emerald-900/40 dark:to-emerald-800/20 flex items-center justify-center text-emerald-600 dark:text-emerald-400 mr-4 shadow-sm border border-emerald-100 dark:border-emerald-800/30">
                    <i class="ph-fill ph-activity text-2xl animate-pulse"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-800 dark:text-white"><?= __('dashboard.recent_activities') ?? 'Recent Activities' ?></h3>
            </div>
            <a href="/logs/activity" class="group flex items-center text-sm font-bold text-blue-600 dark:text-blue-400 hover:text-blue-700 bg-blue-50 dark:bg-blue-900/20 hover:bg-blue-100 dark:hover:bg-blue-900/40 px-4 py-2 rounded-xl transition-colors">
                <?= __('common.view_all') ?? 'View All' ?>
                <i class="ph-bold ph-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
            </a>
        </div>
        
        <div class="flex-1">
            <?php if (!empty($recentActivities)): ?>
                <div class="space-y-4">
                    <?php foreach ($recentActivities as $activity): ?>
                        <div class="flex items-center p-3 rounded-2xl hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors border border-transparent hover:border-slate-100 dark:hover:border-slate-700/50 group">
                            <div class="flex-shrink-0">
                                <?php if (!empty($activity['profile_image'])): ?>
                                    <img src="<?= htmlspecialchars($activity['profile_image']) ?>" class="w-12 h-12 rounded-full border-2 border-white dark:border-slate-800 shadow-sm object-cover group-hover:scale-105 transition-transform">
                                <?php else: ?>
                                    <div class="w-12 h-12 rounded-full bg-gradient-to-br from-slate-100 to-slate-200 dark:from-slate-700 dark:to-slate-800 flex items-center justify-center text-slate-600 dark:text-slate-300 font-black text-lg border-2 border-white dark:border-slate-800 shadow-sm group-hover:scale-105 transition-transform">
                                        <?= strtoupper(substr($activity['username'] ?? 'U', 0, 1)) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="ml-4 flex-1">
                                <p class="text-sm font-bold text-slate-800 dark:text-slate-200 flex items-center">
                                    <?= htmlspecialchars($activity['username'] ?? __('common.system') ?? 'System') ?>
                                    <span class="mx-2 w-1 h-1 rounded-full bg-slate-300 dark:bg-slate-600"></span>
                                    <span class="text-xs font-semibold text-blue-500 bg-blue-50 dark:bg-blue-900/30 px-2 py-0.5 rounded-md uppercase tracking-wide">
                                        <?= htmlspecialchars($activity['module'] ?? 'System') ?>
                                    </span>
                                </p>
                                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1 line-clamp-1">
                                    <?= htmlspecialchars($activity['action'] ?? '') ?>
                                </p>
                            </div>
                            <div class="ml-4 flex-shrink-0 text-right">
                                <div class="flex items-center text-xs font-medium text-slate-400 dark:text-slate-500 bg-white dark:bg-slate-800 px-3 py-1.5 rounded-lg border border-slate-100 dark:border-slate-700 shadow-sm">
                                    <i class="ph-bold ph-clock mr-1.5"></i>
                                    <?= formatDate($activity['created_at'], true) ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="h-full flex flex-col items-center justify-center text-slate-400 dark:text-slate-500 py-10">
                    <div class="w-20 h-20 bg-slate-50 dark:bg-slate-800/50 rounded-full flex items-center justify-center mb-4">
                        <i class="ph-fill ph-ghost text-4xl opacity-50 text-slate-400"></i>
                    </div>
                    <p class="text-base font-medium"><?= __('dashboard.no_recent_activity') ?? 'No recent activities found.' ?></p>
                    <p class="text-sm text-slate-400 mt-1">Things are pretty quiet right now.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Script for Charts -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const isDarkMode = document.documentElement.classList.contains('dark');
        const textColor = isDarkMode ? '#94a3b8' : '#64748b'; // slate-400 : slate-500
        const gridColor = isDarkMode ? '#1e293b' : '#f1f5f9'; // slate-800 : slate-100
        const tooltipBg = isDarkMode ? 'rgba(15, 23, 42, 0.9)' : 'rgba(255, 255, 255, 0.9)'; // slate-900 : white
        const tooltipText = isDarkMode ? '#f8fafc' : '#0f172a';

        // Set global defaults for a premium look
        Chart.defaults.font.family = "'Inter', 'Sarabun', sans-serif";
        Chart.defaults.color = textColor;
        Chart.defaults.scale.grid.color = gridColor;

        <?php if (isset($charts) && is_array($charts)): ?>
            <?php foreach ($charts as $chart): ?>
                (function() {
                    const ctx = document.getElementById('<?= htmlspecialchars($chart['id']) ?>');
                    if (!ctx) return;
                    
                    const datasets = <?= json_encode($chart['datasets']) ?>.map(ds => {
                        let colorHex = '#3b82f6';
                        let bgColorHex = 'rgba(59, 130, 246, 0.1)';
                        let hoverBgColorHex = 'rgba(59, 130, 246, 0.2)';
                        
                        if (ds.color === 'emerald') { 
                            colorHex = '#10b981'; 
                            bgColorHex = 'rgba(16, 185, 129, 0.1)'; 
                        } else if (ds.color === 'purple') { 
                            colorHex = '#a855f7'; 
                            bgColorHex = 'rgba(168, 85, 247, 0.1)'; 
                        } else if (ds.color === 'multi') {
                            // Premium color palette for multi-color charts (e.g. Doughnut)
                            colorHex = ['#3b82f6', '#8b5cf6', '#ec4899', '#f43f5e', '#f59e0b', '#10b981', '#06b6d4'];
                            bgColorHex = ['#3b82f6cc', '#8b5cf6cc', '#ec4899cc', '#f43f5ecc', '#f59e0bcc', '#10b981cc', '#06b6d4cc'];
                            hoverBgColorHex = colorHex;
                        }

                        // Create gradient for line chart fill if applicable
                        let finalBgColor = bgColorHex;
                        if (ds.color !== 'multi' && '<?= htmlspecialchars($chart['type'] ?? '') ?>' === 'line') {
                            const gradient = ctx.getContext('2d').createLinearGradient(0, 0, 0, 300);
                            gradient.addColorStop(0, bgColorHex.replace('0.1', '0.4'));
                            gradient.addColorStop(1, 'rgba(255,255,255,0)');
                            finalBgColor = gradient;
                        }
                        
                        return {
                            label: ds.label,
                            data: ds.data,
                            borderColor: ds.color !== 'multi' ? colorHex : 'transparent',
                            backgroundColor: ds.color !== 'multi' ? finalBgColor : bgColorHex,
                            hoverBackgroundColor: hoverBgColorHex,
                            borderWidth: 3,
                            tension: 0.4, // smooth curves
                            fill: true,
                            pointBackgroundColor: '#ffffff',
                            pointBorderColor: colorHex,
                            pointBorderWidth: 3,
                            pointRadius: 4,
                            pointHoverRadius: 7,
                            pointHoverBorderWidth: 3,
                            pointHoverBackgroundColor: colorHex,
                            pointHoverBorderColor: '#ffffff',
                            hoverOffset: 10 // popup effect for pie/doughnut
                        };
                    });

                    const isPieOrDoughnut = ['pie', 'doughnut'].includes('<?= htmlspecialchars($chart['type'] ?? 'line') ?>');
                    
                    const chartOptions = {
                        responsive: true,
                        maintainAspectRatio: false,
                        layout: {
                            padding: isPieOrDoughnut ? 10 : 0
                        },
                        plugins: {
                            legend: {
                                display: true,
                                position: isPieOrDoughnut ? 'right' : 'top',
                                labels: {
                                    usePointStyle: true,
                                    padding: 20,
                                    font: {
                                        weight: '600',
                                        size: 13
                                    }
                                }
                            },
                            tooltip: {
                                backgroundColor: tooltipBg,
                                titleColor: tooltipText,
                                bodyColor: tooltipText,
                                titleFont: { size: 14, weight: 'bold' },
                                bodyFont: { size: 14 },
                                padding: 12,
                                boxPadding: 6,
                                borderColor: gridColor,
                                borderWidth: 1,
                                cornerRadius: 8,
                                displayColors: true,
                                boxHeight: 8,
                                boxWidth: 8,
                                usePointStyle: true,
                                animation: {
                                    duration: 300
                                }
                            }
                        },
                        interaction: {
                            mode: 'index',
                            intersect: false,
                        }
                    };
                    
                    if (!isPieOrDoughnut) {
                        chartOptions.scales = {
                            y: {
                                beginAtZero: true,
                                border: { dash: [4, 4], display: false },
                                ticks: {
                                    color: textColor,
                                    precision: 0,
                                    padding: 10,
                                    font: { weight: '500' }
                                },
                                grid: {
                                    color: gridColor,
                                    drawTicks: false,
                                }
                            },
                            x: {
                                border: { display: false },
                                ticks: {
                                    color: textColor,
                                    padding: 10,
                                    font: { weight: '500' }
                                },
                                grid: {
                                    display: false
                                }
                            }
                        };
                    } else if (isPieOrDoughnut) {
                        chartOptions.cutout = '70%'; // makes doughnut thinner and sleeker
                        chartOptions.radius = '90%';
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
