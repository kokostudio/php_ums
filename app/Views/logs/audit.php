<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white"><?= __('logs.audit_title') ?></h1>
        <p class="text-sm text-gray-500 dark:text-slate-400 mt-1"><?= __('logs.audit_subtitle') ?></p>
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
        <table id="auditTable" class="w-full text-left border-collapse whitespace-nowrap">
            <thead>
                <tr class="bg-slate-50 dark:bg-slate-800 text-slate-500 dark:text-slate-400 text-xs uppercase tracking-wider">
                    <th class="p-4 font-medium rounded-tl-lg w-16"><?= __('common.id') ?></th>
                    <th class="p-4 font-medium w-40"><?= __('logs.timestamp') ?></th>
                    <th class="p-4 font-medium w-32"><?= __('logs.user') ?></th>
                    <th class="p-4 font-medium w-32"><?= __('logs.table_name') ?></th>
                    <th class="p-4 font-medium w-32"><?= __('logs.action') ?></th>
                    <th class="p-4 font-medium text-right rounded-tr-lg"><?= __('logs.changes') ?></th>
                </tr>
            </thead>
            <tbody class="text-sm text-gray-700 dark:text-slate-300 divide-y divide-gray-100 dark:divide-slate-800">
                <?php 
                    $auditDataMap = [];
                    foreach ($logs ?? [] as $log): 
                        $hashId = \App\Core\Security::encodeId((int)$log['id']);
                        $recordHashId = \App\Core\Security::encodeId((int)$log['record_id']);
                        $auditDataMap[$hashId] = [
                            'old' => $log['old_values'],
                            'new' => $log['new_values']
                        ];
                ?>
                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                    <td class="p-4"><span class="font-mono text-xs text-slate-500 bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded">#<?= $hashId ?></span></td>
                    <td class="p-4">
                        <span class="text-gray-500 dark:text-slate-400 text-xs">
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
                            #<?= $recordHashId ?> in <?= htmlspecialchars($log['table_name']) ?>
                        </span>
                    </td>
                    <td class="p-4">
                        <?php 
                            $action = $log['action'];
                            $color = 'gray';
                            if ($action === 'insert') $color = 'emerald';
                            if ($action === 'update') $color = 'amber';
                            if ($action === 'delete') $color = 'red';
                        ?>
                        <span class="px-2 py-1 bg-<?= $color ?>-50 dark:bg-<?= $color ?>-900/20 text-<?= $color ?>-600 dark:text-<?= $color ?>-400 rounded-lg text-xs font-bold uppercase tracking-wider">
                            <?= htmlspecialchars($action) ?>
                        </span>
                    </td>
                    <td class="p-4 text-right">
                        <button onclick="viewChanges('<?= $hashId ?>')" class="inline-flex items-center text-xs font-medium bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-700 dark:text-slate-300 px-3 py-1.5 rounded-lg hover:bg-gray-50 dark:hover:bg-slate-700 hover:text-blue-600 dark:hover:text-blue-400 transition-colors shadow-sm">
                            <i class="ph ph-eye mr-1.5 text-sm"></i> <?= __('logs.view_details') ?>
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal for JSON Diff Viewer -->
<div id="diffModal" class="fixed inset-0 z-50 hidden bg-gray-900/50 dark:bg-black/70 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white dark:bg-black/20 dark:backdrop-blur-md rounded-2xl shadow-xl w-full max-w-3xl overflow-hidden flex flex-col max-h-[85vh] md:max-h-[90vh] border border-gray-100 dark:border-slate-800 transition-colors">
        <!-- Fixed Modal Header -->
        <div class="flex justify-between items-center px-6 py-4 border-b border-gray-100 dark:border-slate-800 flex-shrink-0 bg-white dark:bg-slate-900/80">
            <h3 class="text-lg font-bold text-gray-800 dark:text-white flex items-center">
                <i class="ph ph-file-code text-blue-500 dark:text-blue-400 mr-2 text-xl"></i> <?= __('logs.change_details') ?>
            </h3>
            <button onclick="closeDiffModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-slate-200 transition-colors p-1">
                <i class="ph ph-x text-xl"></i>
            </button>
        </div>
        <!-- Scrollable Modal Body -->
        <div class="p-6 overflow-y-auto flex-1 bg-slate-50 dark:bg-slate-950/50">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Old Values -->
                <div>
                    <h4 class="text-xs font-bold text-red-500 dark:text-red-400 uppercase tracking-wider mb-2 flex items-center">
                        <i class="ph ph-minus-circle mr-1"></i> <?= __('logs.old_values') ?>
                    </h4>
                    <pre id="oldValues" class="bg-red-50 dark:bg-red-900/10 border border-red-100 dark:border-red-900/30 rounded-lg p-4 text-xs font-mono text-red-800 dark:text-red-300 overflow-x-auto whitespace-pre-wrap"></pre>
                </div>
                <!-- New Values -->
                <div>
                    <h4 class="text-xs font-bold text-emerald-500 dark:text-emerald-400 uppercase tracking-wider mb-2 flex items-center">
                        <i class="ph ph-plus-circle mr-1"></i> <?= __('logs.new_values') ?>
                    </h4>
                    <pre id="newValues" class="bg-emerald-50 dark:bg-emerald-900/10 border border-emerald-100 dark:border-emerald-900/30 rounded-lg p-4 text-xs font-mono text-emerald-800 dark:text-emerald-300 overflow-x-auto whitespace-pre-wrap"></pre>
                </div>
            </div>
        </div>
        <!-- Fixed Modal Footer -->
        <div class="px-6 py-4 border-t border-gray-100 dark:border-slate-800 bg-white dark:bg-slate-900/80 flex justify-end flex-shrink-0">
            <button onclick="closeDiffModal()" class="px-5 py-2 text-sm font-medium text-gray-700 dark:text-slate-300 bg-white dark:bg-slate-800 border border-gray-300 dark:border-slate-700 rounded-lg hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">
                <?= __('common.close') ?>
            </button>
        </div>
    </div>
</div>



<script>
let currentAuditData = <?= json_encode($auditDataMap ?? new stdClass()) ?>;

$(document).ready(function() {
    const table = $('#auditTable').DataTable({
        dom: 'rt<"flex flex-col md:flex-row justify-between items-center p-4 border-t border-gray-100 dark:border-slate-800"<"flex items-center text-sm text-gray-500 dark:text-slate-400 gap-4"li>p><"clear">',
        pageLength: 10,
        order: [[0, 'desc']],
        columnDefs: [
            { orderable: false, targets: 5 }
        ]
    });

    // Custom Search
    $('#custom_search').on('keyup', function() {
        table.search(this.value).draw();
    });
});

function viewChanges(id) {
    const data = currentAuditData[id];
    let oldJson = data.old ? (typeof data.old === 'string' ? JSON.parse(data.old) : data.old) : null;
    let newJson = data.new ? (typeof data.new === 'string' ? JSON.parse(data.new) : data.new) : null;
    
    document.getElementById('oldValues').textContent = oldJson ? JSON.stringify(oldJson, null, 2) : '<?= __('common.no_data') ?>';
    document.getElementById('newValues').textContent = newJson ? JSON.stringify(newJson, null, 2) : '<?= __('common.no_data') ?>';
    
    document.getElementById('diffModal').classList.remove('hidden');
}

function closeDiffModal() {
    document.getElementById('diffModal').classList.add('hidden');
}
</script>
