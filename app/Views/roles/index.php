<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white"><?= __('roles.title') ?></h1>
        <p class="text-sm text-gray-500 dark:text-slate-400 mt-1"><?= __('roles.subtitle') ?></p>
    </div>
    <a href="/roles/create" class="bg-sidebar hover:opacity-90 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors shadow-sm flex items-center">
        <i class="ph ph-plus mr-2"></i> <?= __('roles.add_role') ?>
    </a>
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
        <table id="rolesTable" class="w-full text-left border-collapse whitespace-nowrap">
            <thead>
                <tr class="bg-slate-50 dark:bg-slate-800 text-slate-500 dark:text-slate-400 text-xs uppercase tracking-wider">
                    <th class="p-4 font-medium rounded-tl-lg w-16"><?= __('common.id') ?></th>
                    <th class="p-4 font-medium"><?= __('roles.role_name') ?></th>
                    <th class="p-4 font-medium"><?= __('roles.description') ?></th>
                    <th class="p-4 font-medium w-32 text-center"><?= __('roles.total_roles') ?></th>
                    <th class="p-4 font-medium text-right rounded-tr-lg w-32"><?= __('common.actions') ?></th>
                </tr>
            </thead>
            <tbody class="text-sm text-gray-700 dark:text-slate-300 divide-y divide-gray-100 dark:divide-slate-800">
                <?php foreach ($roles as $role): ?>
                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                    <td class="p-4">
                        <span class="text-xs font-mono text-gray-500 dark:text-slate-400"><?= $role['hash_id'] ?></span>
                    </td>
                    <td class="p-4">
                        <div class="font-medium text-gray-900 dark:text-white flex items-center">
                            <?php if ($role['id'] == 1): ?>
                                <i class="ph-fill ph-crown text-amber-500 mr-2 text-lg" title="Super Admin"></i>
                            <?php endif; ?>
                            <?= htmlspecialchars($role['name']) ?>
                        </div>
                    </td>
                    <td class="p-4 text-gray-500 dark:text-slate-400"><?= htmlspecialchars($role['description']) ?: '-' ?></td>
                    <td class="p-4 text-center">
                        <span class="inline-flex items-center justify-center px-2.5 py-1 bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 rounded-full text-xs font-semibold">
                            <?= $role['users_count'] ?> <?= __('roles.users_in_role') ?>
                        </span>
                    </td>
                    <td class="p-4 text-right">
                        <div class="flex justify-end gap-2">
                            <a href="/roles/<?= $role['hash_id'] ?>/edit" class="text-blue-500 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 transition p-1 rounded hover:bg-blue-50 dark:hover:bg-slate-800" title="<?= __('roles.edit_role') ?>">
                                <i class="ph ph-shield-check text-xl"></i>
                            </a>
                            <?php if ($role['id'] != 1): ?>
                            <button onclick="deleteRole('<?= $role['hash_id'] ?>')" class="text-red-500 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 transition p-1 rounded hover:bg-red-50 dark:hover:bg-slate-800" title="<?= __('common.delete') ?>">
                                <i class="ph ph-trash text-xl"></i>
                            </button>
                            <?php else: ?>
                            <span class="p-1 w-7 h-7 inline-block"></span>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                
                <?php if (empty($roles)): ?>
                <tr>
                    <td colspan="5" class="p-8 text-center text-gray-400"><?= __('common.no_data') ?></td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
$(document).ready(function() {
    const table = $('#rolesTable').DataTable({
        dom: '<"flex flex-col md:flex-row justify-between items-center mb-4"<"flex items-center gap-2"B>f>rt<"flex flex-col md:flex-row justify-between items-center mt-4"lip><"clear">',
        buttons: [
            {
                extend: 'excelHtml5',
                text: '<i class="ph-fill ph-file-xls mr-1"></i> Excel',
                className: 'bg-green-500 hover:bg-green-600 text-white font-medium py-1 px-3 rounded shadow-sm text-sm transition-colors border-0',
                exportOptions: { columns: [0, 1, 2, 3] }
            },
            {
                extend: 'pdfHtml5',
                text: '<i class="ph-fill ph-file-pdf mr-1"></i> PDF',
                className: 'bg-red-500 hover:bg-red-600 text-white font-medium py-1 px-3 rounded shadow-sm text-sm transition-colors border-0 ml-2',
                exportOptions: { columns: [0, 1, 2, 3] }
            }
        ],
        pageLength: 10,
        order: [[0, 'asc']],
        columnDefs: [
            { orderable: false, targets: 4 } // Disable sorting on actions
        ]
    });

    // Custom Search
    $('#custom_search').on('keyup', function() {
        table.search(this.value).draw();
    });
});

function deleteRole(id) {
    Swal.fire({
        title: '<?= __('common.delete_confirm_title') ?>',
        text: "<?= __('common.delete_confirm_text') ?>",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#64748b',
        confirmButtonText: '<?= __('common.confirm') ?>',
        cancelButtonText: '<?= __('common.cancel') ?>'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '/roles/' + id + '/delete';
            
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = 'csrf_token';
            csrfInput.value = '<?= \App\Core\Session::getCsrfToken() ?>';
            
            form.appendChild(csrfInput);
            document.body.appendChild(form);
            form.submit();
        }
    });
}
</script>
