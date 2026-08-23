<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white"><?= __('users.title') ?></h1>
        <p class="text-sm text-gray-500 dark:text-slate-400 mt-1"><?= __('users.subtitle') ?></p>
    </div>
    <a href="/users/create" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors shadow-sm flex items-center">
        <i class="ph ph-plus mr-2"></i> <?= __('users.add_user') ?>
    </a>
</div>

<!-- Select2 CSS/JS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    /* Premium Select2 Styling for Tailwind */
    .select2-container--default .select2-selection--single {
        border-radius: 0.5rem;
        border: 1px solid #e2e8f0;
        height: 38px;
        outline: none;
        transition: all 0.2s ease;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 36px;
        padding-left: 1rem;
        color: #1e293b;
        font-size: 0.875rem;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px;
    }
    .select2-container--default.select2-container--open .select2-selection--single {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
    .select2-dropdown {
        border-color: #e2e8f0;
        border-radius: 0.5rem;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }
    
    /* Dark Mode Select2 */
    .dark .select2-container--default .select2-selection--single {
        background-color: #1e293b;
        border-color: #334155;
    }
    .dark .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #f1f5f9;
    }
    .dark .select2-dropdown {
        background-color: #1e293b;
        border-color: #334155;
    }
    .dark .select2-container--default .select2-search--dropdown .select2-search__field {
        background-color: #0f172a;
        border-color: #334155;
        color: #f1f5f9;
    }
    .dark .select2-container--default .select2-results__option[aria-selected=true] {
        background-color: #334155;
    }
    .dark .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #3b82f6;
    }
    
    /* Hide default DataTables search */
    div.dataTables_filter {
        display: none !important;
    }
</style>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-800 overflow-hidden transition-colors">
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
            <div class="w-full sm:w-64">
                <label class="block text-xs font-medium text-gray-500 dark:text-slate-400 mb-1"><?= __('users.role') ?></label>
                <select id="filter_role" class="select2 w-full">
                    <option value=""><?= __('common.all') ?></option>
                    <?php foreach ($roles ?? [] as $role): ?>
                        <option value="<?= $role['id'] ?>"><?= htmlspecialchars($role['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="w-full sm:w-64">
                <label class="block text-xs font-medium text-gray-500 dark:text-slate-400 mb-1"><?= __('users.status') ?></label>
                <select id="filter_status" class="select2 w-full">
                    <option value=""><?= __('common.all') ?></option>
                    <option value="active"><?= __('common.active') ?? 'Active' ?></option>
                    <option value="inactive"><?= __('common.inactive') ?? 'Inactive' ?></option>
                    <option value="banned"><?= __('common.banned') ?? 'Banned' ?></option>
                </select>
            </div>
        </div>
    </div>
    
    <div class="p-6 overflow-x-auto">
        <table id="usersTable" class="w-full text-left border-collapse whitespace-nowrap">
            <thead>
                <tr class="bg-slate-50 dark:bg-slate-800 text-slate-500 dark:text-slate-400 text-xs uppercase tracking-wider">
                    <th class="p-4 font-medium rounded-tl-lg"><?= __('common.id') ?></th>
                    <th class="p-4 font-medium"><?= __('users.username') ?></th>
                    <th class="p-4 font-medium"><?= __('users.email') ?></th>
                    <th class="p-4 font-medium"><?= __('users.role') ?></th>
                    <th class="p-4 font-medium"><?= __('users.status') ?></th>
                    <th class="p-4 font-medium"><?= __('common.created_at') ?></th>
                    <th class="p-4 font-medium text-right rounded-tr-lg"><?= __('common.actions') ?></th>
                </tr>
            </thead>
            <tbody class="text-sm text-gray-700 dark:text-slate-300">
                <!-- DataTables will populate this -->
            </tbody>
        </table>
    </div>
</div>



<script>
$(document).ready(function() {
    // Initialize Select2
    $('.select2').select2({
        minimumResultsForSearch: -1 // hide search box for these small lists
    });

    const table = $('#usersTable').DataTable({
        dom: '<"flex flex-col md:flex-row justify-between items-center mb-4"<"flex items-center gap-2"B>f>rt<"flex flex-col md:flex-row justify-between items-center mt-4"lip><"clear">',
        buttons: [
            {
                extend: 'excelHtml5',
                text: '<i class="ph-fill ph-file-xls mr-1"></i> Excel',
                className: 'bg-green-500 hover:bg-green-600 text-white font-medium py-1 px-3 rounded shadow-sm text-sm transition-colors border-0',
                exportOptions: { columns: [0, 1, 2, 3, 4, 5] }
            },
            {
                extend: 'pdfHtml5',
                text: '<i class="ph-fill ph-file-pdf mr-1"></i> PDF',
                className: 'bg-red-500 hover:bg-red-600 text-white font-medium py-1 px-3 rounded shadow-sm text-sm transition-colors border-0 ml-2',
                exportOptions: { columns: [0, 1, 2, 3, 4, 5] }
            }
        ],
        processing: true,
        serverSide: true,
        ajax: {
            url: '/users/data',
            data: function (d) {
                d.role_filter = $('#filter_role').val();
                d.status_filter = $('#filter_status').val();
            }
        },
        order: [[0, 'desc']], // Default sorting
        columns: [
            { 
                data: 'hash_id',
                render: function(data) {
                    return `<span class="text-xs font-mono text-gray-500 dark:text-slate-400">${data}</span>`;
                }
            },
            { 
                data: 'username',
                render: function(data, type, row) {
                    return `<div class="font-medium text-gray-900 dark:text-white">${data}</div>`;
                }
            },
            { data: 'email' },
            { 
                data: 'role_name',
                render: function(data) {
                    return data ? `<span class="px-2.5 py-1 bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-400 rounded-full text-xs font-medium">${data}</span>` : '<span class="text-gray-400 dark:text-slate-500">-</span>';
                }
            },
            { 
                data: 'status',
                render: function(data) {
                    let color = data === 'active' ? 'emerald' : (data === 'banned' ? 'red' : 'gray');
                    return `<span class="px-2.5 py-1 bg-${color}-100 dark:bg-${color}-900/30 text-${color}-700 dark:text-${color}-400 rounded-full text-xs font-medium capitalize">${data}</span>`;
                }
            },
            { 
                data: 'created_at',
                render: function(data) {
                    return `<span class="text-gray-600 dark:text-slate-400 text-sm">${formatDate(data, true)}</span>`;
                }
            },
            { 
                data: 'hash_id',
                orderable: false,
                className: 'text-right',
                render: function(data, type, row) {
                    return `
                        <div class="flex justify-end gap-2">
                            <a href="/users/${data}/edit" class="text-blue-500 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 transition p-1" title="<?= __('common.edit') ?>">
                                <i class="ph ph-pencil-simple text-lg"></i>
                            </a>
                            <button onclick="deleteUser('${data}')" class="text-red-500 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 transition p-1" title="<?= __('common.delete') ?>">
                                <i class="ph ph-trash text-lg"></i>
                            </button>
                        </div>
                    `;
                }
            }
        ]
    });

    // Custom Search
    $('#custom_search').on('keyup', function() {
        table.search(this.value).draw();
    });

    // Trigger reload when filters change
    $('#filter_role, #filter_status').on('change', function() {
        table.ajax.reload();
    });

    window.deleteUser = function(hash) {
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
                axios.post(`/users/${hash}/delete`, {
                    csrf_token: '<?= \App\Core\Session::getCsrfToken() ?>'
                }, {
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
                })
                .then(response => {
                    Swal.fire('<?= __('common.success') ?>!', response.data.message, 'success');
                    table.ajax.reload();
                })
                .catch(error => {
                    Swal.fire('<?= __('common.error') ?>!', error.response?.data?.message || 'Failed to delete user.', 'error');
                });
            }
        });
    };
});
</script>
