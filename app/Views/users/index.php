<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white"><?= __('users.title') ?></h1>
        <p class="text-sm text-gray-500 dark:text-slate-400 mt-1"><?= __('users.subtitle') ?></p>
    </div>
    <a href="/users/create" class="bg-sidebar hover:opacity-90 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors shadow-sm flex items-center">
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
            <tbody class="text-sm text-gray-700 dark:text-slate-300 divide-y divide-gray-100 dark:divide-slate-800">
                <?php foreach ($users ?? [] as $user): ?>
                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                    <td class="p-4">
                        <span class="text-xs font-mono text-gray-500 dark:text-slate-400"><?= $user['hash_id'] ?></span>
                    </td>
                    <td class="p-4">
                        <?php 
                            $avatarUrl = $user['profile_image'] ? '/uploads/avatars/' . htmlspecialchars($user['profile_image']) : 'https://ui-avatars.com/api/?name=' . urlencode($user['username']) . '&background=random';
                        ?>
                        <div class="flex items-center space-x-3">
                            <div class="h-8 w-8 rounded-full overflow-hidden flex-shrink-0 border border-slate-200 dark:border-slate-700 bg-slate-100 dark:bg-slate-800">
                                <img src="<?= $avatarUrl ?>" alt="<?= htmlspecialchars($user['username']) ?>" class="h-full w-full object-cover">
                            </div>
                            <div class="font-medium text-gray-900 dark:text-white"><?= htmlspecialchars($user['username']) ?></div>
                        </div>
                    </td>
                    <td class="p-4"><?= htmlspecialchars($user['email']) ?></td>
                    <td class="p-4 role-cell">
                        <?php if ($user['role_name']): ?>
                            <span class="px-2.5 py-1 bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-400 rounded-full text-xs font-medium"><?= htmlspecialchars($user['role_name']) ?></span>
                        <?php else: ?>
                            <span class="text-gray-400 dark:text-slate-500">-</span>
                        <?php endif; ?>
                    </td>
                    <td class="p-4 status-cell">
                        <?php 
                            $status = $user['status'];
                            $color = $status === 'active' ? 'emerald' : ($status === 'banned' ? 'red' : 'gray');
                        ?>
                        <span class="px-2.5 py-1 bg-<?= $color ?>-100 dark:bg-<?= $color ?>-900/30 text-<?= $color ?>-700 dark:text-<?= $color ?>-400 rounded-full text-xs font-medium capitalize" data-status="<?= htmlspecialchars($status) ?>"><?= htmlspecialchars($status) ?></span>
                    </td>
                    <td class="p-4">
                        <span class="text-gray-600 dark:text-slate-400 text-sm">
                            <?php 
                                $date = new DateTime($user['created_at']);
                                echo $date->format('d/m/Y H:i');
                            ?>
                        </span>
                    </td>
                    <td class="p-4 text-right">
                        <div class="flex justify-end gap-2">
                            <?php if (in_array(strtolower($user['username']), ['admin', 'superadmin']) && \App\Core\Session::get('role_id') != 1 && \App\Core\Session::get('user_id') != $user['id']): ?>
                                <button disabled class="text-gray-300 dark:text-slate-600 cursor-not-allowed p-1 rounded" title="Cannot edit system administrator">
                                    <i class="ph ph-pencil-simple text-lg"></i>
                                </button>
                            <?php else: ?>
                                <a href="/users/<?= $user['hash_id'] ?>/edit" class="text-blue-500 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 transition p-1 rounded hover:bg-blue-50 dark:hover:bg-slate-800" title="<?= __('common.edit') ?>">
                                    <i class="ph ph-pencil-simple text-lg"></i>
                                </a>
                            <?php endif; ?>
                            <?php if (in_array(strtolower($user['username']), ['admin', 'superadmin'])): ?>
                                <button disabled class="text-gray-300 dark:text-slate-600 cursor-not-allowed p-1 rounded" title="Cannot delete system administrator">
                                    <i class="ph ph-trash text-lg"></i>
                                </button>
                            <?php else: ?>
                                <button onclick="deleteUser('<?= $user['hash_id'] ?>')" class="text-red-500 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 transition p-1 rounded hover:bg-red-50 dark:hover:bg-slate-800" title="<?= __('common.delete') ?>">
                                    <i class="ph ph-trash text-lg"></i>
                                </button>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                
                <?php if (empty($users)): ?>
                <tr>
                    <td colspan="7" class="p-8 text-center text-gray-400"><?= __('common.no_data') ?? 'No data available' ?></td>
                </tr>
                <?php endif; ?>
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
        dom: 'rt<"flex flex-col md:flex-row justify-between items-center mt-4"lip><"clear">',
        pageLength: 10,
        order: [[0, 'desc']], // Default sorting
        columnDefs: [
            { orderable: false, targets: 6 } // Disable sorting on actions
        ]
    });

    // Custom Search
    $('#custom_search').on('keyup', function() {
        table.search(this.value).draw();
    });

    // Custom Client-Side Filtering
    $.fn.dataTable.ext.search.push(
        function(settings, data, dataIndex, rowData, counter) {
            if (settings.nTable.id !== 'usersTable') { return true; }
            
            let roleFilter = $('#filter_role option:selected').text();
            let statusFilter = $('#filter_status').val();

            // data[3] is Role string (from TD text)
            let rowRole = data[3] ? data[3].trim() : "";
            // We use a custom data attribute on the status span to get the exact value reliably
            let rowStatus = $(table.cell(dataIndex, 4).node()).find('span').data('status');
            
            // Handle fallback if data attribute is missing
            if (!rowStatus) {
                rowStatus = data[4] ? data[4].trim().toLowerCase() : "";
            }

            if ($('#filter_role').val() !== "" && rowRole !== roleFilter && rowRole !== '-') {
                return false;
            }
            if (statusFilter !== "" && rowStatus !== statusFilter) {
                return false;
            }
            
            return true;
        }
    );

    // Trigger reload when filters change
    $('#filter_role, #filter_status').on('change', function() {
        table.draw();
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
                    Swal.fire('<?= __('common.success') ?>!', response.data.message, 'success')
                    .then(() => {
                        window.location.reload();
                    });
                })
                .catch(error => {
                    Swal.fire('<?= __('common.error') ?>!', error.response?.data?.message || '<?= __('users.failed_delete') ?>', 'error');
                });
            }
        });
    };
});
</script>
