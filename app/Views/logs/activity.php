<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white"><?= __('logs.activity_title') ?></h1>
        <p class="text-sm text-gray-500 dark:text-slate-400 mt-1"><?= __('logs.activity_subtitle') ?></p>
    </div>
</div>

<style>
    /* Hide default DataTables search */
    div.dataTables_filter {
        display: none !important;
    }
</style>

<div class="bg-white dark:bg-black/20 dark:backdrop-blur-md rounded-2xl shadow-sm border border-gray-100 dark:border-slate-800 overflow-hidden transition-colors">
    <div class="p-6 border-b border-gray-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/30">
        <div class="flex flex-col sm:flex-row gap-4">
            <div class="w-full sm:w-64">
                <label class="block text-xs font-medium text-gray-500 dark:text-slate-400 mb-1"><?= __('common.search') ?></label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="ph ph-magnifying-glass text-gray-400"></i>
                    </div>
                    <input type="text" id="custom_search" class="w-full pl-10 pr-3 py-2 border border-gray-200 dark:border-slate-700 rounded-lg text-sm bg-white dark:bg-slate-800 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-slate-500 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors h-[38px]" placeholder="<?= __('common.search') ?>...">
                </div>
            </div>
        </div>
    </div>
    <div class="p-6 overflow-x-auto">
        <table id="activityTable" class="w-full text-left border-collapse whitespace-nowrap">
            <thead>
                <tr class="bg-slate-50 dark:bg-slate-800 text-slate-500 dark:text-slate-400 text-xs uppercase tracking-wider">
                    <th class="p-4 font-medium rounded-tl-lg w-20"><?= __('common.id') ?></th>
                    <th class="p-4 font-medium w-48"><?= __('logs.timestamp') ?></th>
                    <th class="p-4 font-medium w-48"><?= __('logs.user') ?></th>
                    <th class="p-4 font-medium w-32"><?= __('logs.ip_address') ?></th>
                    <th class="p-4 font-medium rounded-tr-lg"><?= __('logs.action') ?></th>
                </tr>
            </thead>
            <tbody class="text-sm text-gray-700 dark:text-slate-300 divide-y divide-gray-100 dark:divide-slate-800">
                <?php foreach ($logs ?? [] as $log): ?>
                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                    <td class="p-4"><span class="font-mono text-xs text-slate-500 bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded">#<?= \App\Core\Security::encodeId((int)$log['id']) ?></span></td>
                    <td class="p-4">
                        <span class="text-gray-500 dark:text-slate-400">
                            <i class="ph ph-clock mr-1"></i> 
                            <?php 
                                $date = new DateTime($log['created_at']);
                                echo $date->format('d/m/Y H:i');
                            ?>
                        </span>
                    </td>
                    <td class="p-4">
                        <?php if ($log['username']): ?>
                            <div class="font-medium text-blue-600 dark:text-blue-400 flex items-center">
                                <i class="ph-fill ph-user-circle text-lg mr-1"></i> 
                                <?= htmlspecialchars($log['username']) ?>
                            </div>
                        <?php else: ?>
                            <span class="text-gray-400 dark:text-slate-500"><?= __('common.system') ?? 'System' ?></span>
                        <?php endif; ?>
                    </td>
                    <td class="p-4">
                        <span class="font-mono text-xs bg-gray-100 dark:bg-slate-800 px-2 py-1 rounded text-gray-600 dark:text-slate-400">
                            <?= htmlspecialchars($log['ip_address'] ?? '') ?>
                        </span>
                    </td>
                    <td class="p-4">
                        <span class="text-gray-800 dark:text-slate-200"><?= htmlspecialchars($log['action']) ?></span>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
$(document).ready(function() {
    const table = $('#activityTable').DataTable({
        dom: 'rt<"flex flex-col md:flex-row justify-between items-center p-4 border-t border-gray-100 dark:border-slate-800"<"flex items-center text-sm text-gray-500 dark:text-slate-400 gap-4"li>p><"clear">',
        pageLength: 10,
        order: [[0, 'desc']]
    });

    // Custom Search
    $('#custom_search').on('keyup', function() {
        table.search(this.value).draw();
    });
});
</script>
