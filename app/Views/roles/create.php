<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white"><?= __('roles.add_role') ?></h1>
        <div class="text-sm text-gray-500 dark:text-slate-400 mt-1 flex items-center">
            <a href="/roles" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors"><?= __('roles.title') ?></a>
            <i class="ph ph-caret-right mx-2 text-xs"></i>
            <span class="text-gray-700 dark:text-slate-300"><?= __('common.create') ?></span>
        </div>
    </div>
</div>

<form action="/roles/create" method="POST">
    <input type="hidden" name="csrf_token" value="<?= \App\Core\Session::getCsrfToken() ?>">

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Left Column: Role Details -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white dark:bg-black/20 dark:backdrop-blur-md rounded-2xl shadow-sm border border-gray-100 dark:border-slate-800 p-6 transition-colors">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4 border-b border-gray-100 dark:border-slate-800 pb-2"><?= __('roles.role_info') ?></h3>
                
                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1"><?= __('roles.role_name') ?> <span class="text-red-500">*</span></label>
                    <input type="text" id="name" name="name" required 
                        class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-colors"
                        placeholder="e.g. Editor">
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1"><?= __('roles.description') ?></label>
                    <textarea id="description" name="description" rows="3"
                        class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-colors"
                        placeholder="Describe what this role can do..."></textarea>
                </div>
            </div>
            
            <div class="bg-blue-50 dark:bg-blue-900/20 rounded-2xl border border-blue-100 dark:border-blue-800/40 p-6 transition-colors">
                <div class="flex items-start space-x-3 text-sm text-blue-700 dark:text-blue-300">
                    <i class="ph-fill ph-info text-blue-500 dark:text-blue-400 text-xl mt-0.5 flex-shrink-0"></i>
                    <p><?= __('roles.permission_hint') ?></p>
                </div>
            </div>
        </div>

        <!-- Right Column: Permission Matrix -->
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-black/20 dark:backdrop-blur-md rounded-2xl shadow-sm border border-gray-100 dark:border-slate-800 p-6 transition-colors">
                <div class="flex justify-between items-center mb-4 border-b border-gray-100 dark:border-slate-800 pb-2">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white"><?= __('roles.permission_matrix') ?></h3>
                    <button type="button" id="selectAll" class="text-sm font-medium text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 transition-colors">
                        <?= __('common.select_all') ?>
                    </button>
                </div>
                
                <div class="space-y-4">
                    <?php foreach ($permissions as $module => $perms): ?>
                        <div class="border border-gray-100 dark:border-slate-800 rounded-xl overflow-hidden">
                            <div class="bg-slate-50 dark:bg-slate-800/60 px-4 py-3 border-b border-gray-100 dark:border-slate-700 flex items-center justify-between">
                                <h4 class="font-semibold text-gray-700 dark:text-slate-200 capitalize"><?= htmlspecialchars($module) ?> <?= __('roles.module') ?></h4>
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="checkbox" class="module-checkbox w-4 h-4 text-blue-600 border-gray-300 dark:border-slate-600 rounded focus:ring-blue-500" data-module="<?= $module ?>">
                                    <span class="ml-2 text-xs font-medium text-gray-500 dark:text-slate-400"><?= __('roles.all_in_module') ?></span>
                                </label>
                            </div>
                            <div class="p-4 grid grid-cols-1 md:grid-cols-2 gap-4 bg-white dark:bg-black/20 dark:backdrop-blur-md">
                                <?php foreach ($perms as $perm): ?>
                                    <label class="inline-flex items-center cursor-pointer group">
                                        <input type="checkbox" name="permissions[]" value="<?= $perm['id'] ?>" class="perm-checkbox w-4 h-4 text-blue-600 border-gray-300 dark:border-slate-600 rounded focus:ring-blue-500 transition-colors" data-module="<?= $module ?>">
                                        <div class="ml-3">
                                            <span class="block text-sm font-medium text-gray-800 dark:text-slate-200 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors capitalize"><?= htmlspecialchars($perm['action']) ?></span>
                                            <span class="block text-xs text-gray-500 dark:text-slate-500"><?= htmlspecialchars($perm['description']) ?></span>
                                        </div>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer Actions -->
    <div class="mt-6 flex items-center justify-end border-t border-gray-200 dark:border-slate-800 pt-6 space-x-3">
        <a href="/roles" class="px-5 py-2.5 text-sm font-medium text-gray-700 dark:text-slate-300 bg-white dark:bg-slate-800 border border-gray-300 dark:border-slate-700 rounded-lg hover:bg-primary-50 dark:hover:bg-primary-900/20 hover:text-primary-600 dark:hover:text-primary-300 hover:border-primary-200 dark:hover:border-primary-800 transition-colors">
            <?= __('common.cancel') ?>
        </a>
        <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-sidebar rounded-lg hover:opacity-90 transition-colors shadow-sm flex items-center">
            <i class="ph ph-floppy-disk mr-2 text-lg"></i> <?= __('roles.save_role') ?>
        </button>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const selectAllBtn = document.getElementById('selectAll');
    let isAllSelected = false;
    selectAllBtn.addEventListener('click', () => {
        isAllSelected = !isAllSelected;
        document.querySelectorAll('.perm-checkbox, .module-checkbox').forEach(cb => cb.checked = isAllSelected);
        selectAllBtn.textContent = isAllSelected ? '<?= __('common.deselect_all') ?>' : '<?= __('common.select_all') ?>';
    });

    document.querySelectorAll('.module-checkbox').forEach(moduleCb => {
        moduleCb.addEventListener('change', (e) => {
            const moduleName = e.target.dataset.module;
            document.querySelectorAll(`.perm-checkbox[data-module="${moduleName}"]`).forEach(cb => cb.checked = e.target.checked);
        });
    });

    document.querySelectorAll('.perm-checkbox').forEach(cb => {
        cb.addEventListener('change', (e) => {
            const moduleName = e.target.dataset.module;
            const allModuleCbs = document.querySelectorAll(`.perm-checkbox[data-module="${moduleName}"]`);
            const allChecked = Array.from(allModuleCbs).every(c => c.checked);
            document.querySelector(`.module-checkbox[data-module="${moduleName}"]`).checked = allChecked;
        });
    });
});
</script>
