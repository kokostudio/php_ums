<form action="/roles/<?= \App\Core\Security::encodeId($role['id']) ?>/edit" method="POST">
    <input type="hidden" name="csrf_token" value="<?= \App\Core\Session::getCsrfToken() ?>">

    <div class="mb-6 flex items-center justify-between sticky -top-6 md:-top-8 z-40 bg-slate-50/90 dark:bg-[#0f172a]/90 backdrop-blur-md py-4 -mx-6 md:-mx-8 px-6 md:px-8 border-b border-gray-200 dark:border-slate-800">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white"><?= __('roles.edit_role') ?></h1>
            <div class="text-sm text-gray-500 dark:text-slate-400 mt-1 flex items-center">
                <a href="/roles" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors"><?= __('roles.title') ?></a>
                <i class="ph ph-caret-right mx-2 text-xs"></i>
                <span class="text-gray-700 dark:text-slate-300"><?= __('common.edit') ?></span>
                <i class="ph ph-caret-right mx-2 text-xs"></i>
                <span class="text-gray-700 dark:text-slate-300"><?= htmlspecialchars($role['name']) ?></span>
            </div>
        </div>
        <div class="flex items-center space-x-3">
            <a href="/roles" class="px-5 py-2.5 text-sm font-medium text-gray-700 dark:text-slate-300 bg-white dark:bg-slate-800 border border-gray-300 dark:border-slate-700 rounded-md hover:bg-slate-50 dark:hover:bg-slate-700 transition-all duration-200 shadow-sm">
                <?= __('common.cancel') ?>
            </a>
            <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-sidebar rounded-md hover:opacity-90 transition-colors shadow-sm flex items-center">
                <i class="ph ph-floppy-disk mr-2 text-lg"></i> <?= __('roles.update_role') ?>
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Left Column: Role Details -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white dark:bg-slate-900/40 dark:backdrop-blur-xl rounded-md shadow-sm border border-slate-200 dark:border-slate-700/50 p-6 transition-all hover:shadow-md">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4 border-b border-gray-100 dark:border-slate-800 pb-3 flex items-center">
                    <i class="ph ph-identification-card text-primary-500 mr-2 text-xl"></i> <?php echo __('roles.role_info') ?>
                </h3>
                
                <?php if ($role['id'] == 1): ?>
                    <div class="mb-4 bg-amber-50 dark:bg-amber-900/20 text-amber-800 dark:text-amber-300 p-4 rounded-md border border-amber-200 dark:border-amber-700/40 text-sm">
                        <i class="ph-fill ph-crown mr-2"></i> <?= __('roles.super_admin_notice') ?>
                    </div>
                <?php endif; ?>

                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5"><?php echo __('roles.role_name') ?> <span class="text-red-500">*</span></label>
                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($role['name']) ?>" required
                        class="w-full px-4 py-2.5 rounded-md border border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-800 text-gray-900 dark:text-white focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-primary-500/40 focus:border-primary-500 hover:border-slate-400 dark:hover:border-slate-500 transition-all duration-200 shadow-sm outline-none">
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5"><?php echo __('roles.description') ?></label>
                    <textarea id="description" name="description" rows="3"
                        class="w-full px-4 py-2.5 rounded-md border border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-800 text-gray-900 dark:text-white focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-primary-500/40 focus:border-primary-500 hover:border-slate-400 dark:hover:border-slate-500 transition-all duration-200 shadow-sm outline-none resize-y"><?php echo htmlspecialchars($role['description'] ?? '') ?></textarea>
                </div>
            </div>
        </div>

        <!-- Right Column: Permission Matrix -->
        <div class="lg:col-span-2">
            <div class="bg-primary-50/50 dark:bg-primary-900/10 rounded-md border border-primary-200/60 dark:border-primary-800/40 p-5 transition-all shadow-sm">
                <div class="flex items-start space-x-3 text-sm text-primary-800 dark:text-primary-200 mb-4">
                    <i class="ph-fill ph-info text-primary-500 dark:text-primary-400 text-xl mt-0.5 flex-shrink-0 animate-pulse"></i>
                    <p class="leading-relaxed"><?php echo __('roles.permission_hint') ?></p>
                    <?php if ($role['id'] != 1): ?>
                    <button type="button" id="selectAll" class="ml-auto text-sm font-medium text-primary-600 dark:text-primary-400 hover:text-primary-800 dark:hover:text-primary-300 transition-colors">
                        <?= __('common.select_all') ?>
                    </button>
                    <?php endif; ?>
                </div>
                
                <div class="space-y-4">
                    <?php foreach ($permissions as $module => $perms): ?>
                        <div class="border border-slate-200 dark:border-slate-700 rounded-md overflow-hidden bg-white dark:bg-slate-900">
                            <div class="bg-slate-50 dark:bg-slate-800/60 px-4 py-3 border-b border-slate-200 dark:border-slate-700 flex items-center justify-between">
                                <h4 class="font-semibold text-gray-700 dark:text-slate-200 capitalize"><?= htmlspecialchars($module) ?> <?= __('roles.module') ?></h4>
                                <?php if ($role['id'] != 1): ?>
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="checkbox" class="module-checkbox w-4 h-4 text-primary-600 border-slate-300 dark:border-slate-600 rounded-sm focus:ring-primary-500" data-module="<?= $module ?>">
                                    <span class="ml-2 text-xs font-medium text-gray-500 dark:text-slate-400"><?= __('roles.all_in_module') ?></span>
                                </label>
                                <?php endif; ?>
                            </div>
                            <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-4">
                                <?php foreach ($perms as $perm): ?>
                                    <label class="inline-flex items-start cursor-pointer group p-2 -m-2 rounded-md hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                                        <div class="pt-0.5">
                                            <?php $isChecked = in_array($perm['id'], $assignedPerms) || $role['id'] == 1; ?>
                                            <input type="checkbox" name="permissions[]" value="<?php echo \App\Core\Security::encodeId((int)$perm['id']) ?>" class="perm-checkbox w-4 h-4 text-primary-600 border-slate-300 dark:border-slate-500 rounded-sm focus:ring-primary-500 transition-all shadow-sm" data-module="<?php echo $module ?>" <?php echo $isChecked ? 'checked' : '' ?> <?= $role['id'] == 1 ? 'disabled' : '' ?>>
                                        </div>
                                        <div class="ml-3 flex-1">
                                            <span class="block text-sm font-medium text-gray-800 dark:text-slate-200 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors capitalize"><?php echo htmlspecialchars($perm['action']) ?></span>
                                            <span class="block text-xs text-slate-500 dark:text-slate-400 mt-0.5"><?php echo htmlspecialchars($perm['description']) ?></span>
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

</form>

<?php if ($role['id'] != 1): ?>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const checkModuleState = () => {
        document.querySelectorAll('.module-checkbox').forEach(moduleCb => {
            const moduleName = moduleCb.dataset.module;
            const allModuleCbs = document.querySelectorAll(`.perm-checkbox[data-module="${moduleName}"]`);
            if(allModuleCbs.length > 0) {
                moduleCb.checked = Array.from(allModuleCbs).every(c => c.checked);
            }
        });
    };
    
    checkModuleState();

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
<?php endif; ?>
