<form action="/roles/create" method="POST">
    <input type="hidden" name="csrf_token" value="<?php echo \App\Core\Session::getCsrfToken() ?>">

    <div class="mb-6 flex items-center justify-between sticky -top-6 md:-top-8 z-40 bg-slate-50/90 dark:bg-[#0f172a]/90 backdrop-blur-md py-4 -mx-6 md:-mx-8 px-6 md:px-8 border-b border-gray-200 dark:border-slate-800">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white"><?php echo __('roles.add_role') ?></h1>
            <div class="text-sm text-gray-500 dark:text-slate-400 mt-1 flex items-center">
                <a href="/roles" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors"><?php echo __('roles.title') ?></a>
                <i class="ph ph-caret-right mx-2 text-xs"></i>
                <span class="text-gray-700 dark:text-slate-300"><?php echo __('common.create') ?></span>
            </div>
        </div>
        <div class="flex items-center space-x-3">
            <a href="/roles" class="px-5 py-2.5 text-sm font-medium text-gray-700 dark:text-slate-300 bg-white dark:bg-slate-800 border border-gray-300 dark:border-slate-700 rounded-md hover:bg-slate-50 dark:hover:bg-slate-700 transition-all duration-200 shadow-sm">
                <?php echo __('common.cancel') ?>
            </a>
            <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-sidebar rounded-md hover:opacity-90 transition-colors shadow-sm flex items-center">
                <i class="ph ph-floppy-disk mr-2 text-lg"></i> <?php echo __('roles.save_role') ?>
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

                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5"><?php echo __('roles.role_name') ?> <span class="text-red-500">*</span></label>
                    <input type="text" id="name" name="name" required
                        class="w-full px-4 py-2.5 rounded-md border border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-800 text-gray-900 dark:text-white focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-primary-500/40 focus:border-primary-500 hover:border-slate-400 dark:hover:border-slate-500 transition-all duration-200 shadow-sm outline-none"
                        placeholder="<?php echo __('roles.ph_role_name') ?? 'e.g. Editor' ?>">
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5"><?php echo __('roles.description') ?></label>
                    <textarea id="description" name="description" rows="3"
                        class="w-full px-4 py-2.5 rounded-md border border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-800 text-gray-900 dark:text-white focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-primary-500/40 focus:border-primary-500 hover:border-slate-400 dark:hover:border-slate-500 transition-all duration-200 shadow-sm outline-none resize-y"
                        placeholder="Describe what this role can do..."></textarea>
                </div>
            </div>

            <div class="bg-primary-50/50 dark:bg-primary-900/10 rounded-md border border-primary-200/60 dark:border-primary-800/40 p-5 transition-all shadow-sm">
                <div class="flex items-start space-x-3 text-sm text-primary-800 dark:text-primary-200">
                    <i class="ph-fill ph-info text-primary-500 dark:text-primary-400 text-xl mt-0.5 flex-shrink-0 animate-pulse"></i>
                    <p class="leading-relaxed"><?php echo __('roles.permission_hint') ?></p>
                </div>
            </div>
        </div>

        <!-- Right Column: Permission Matrix -->
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-slate-900/40 dark:backdrop-blur-xl rounded-md shadow-sm border border-slate-200 dark:border-slate-700/50 p-6 transition-all hover:shadow-md">
                <div class="flex justify-between items-center mb-6 border-b border-gray-100 dark:border-slate-800 pb-3">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white flex items-center">
                        <i class="ph ph-shield-check text-primary-500 mr-2 text-xl"></i> <?php echo __('roles.permission_matrix') ?>
                    </h3>
                    <button type="button" id="selectAll" class="text-sm font-medium text-primary-600 dark:text-primary-400 hover:text-primary-800 dark:hover:text-primary-300 transition-colors bg-primary-50 dark:bg-primary-900/20 px-3 py-1.5 rounded-md border border-primary-100 dark:border-primary-800/50">
                        <?php echo __('common.select_all') ?>
                    </button>
                </div>

                <div class="space-y-5">
                    <?php foreach ($permissions as $module => $perms): ?>
                        <div class="border border-slate-200 dark:border-slate-700/60 rounded-md overflow-hidden bg-white dark:bg-slate-800/30 transition-all hover:border-primary-300 dark:hover:border-primary-700">
                            <div class="bg-slate-50 dark:bg-slate-800/80 px-5 py-3.5 border-b border-slate-200 dark:border-slate-700/60 flex items-center justify-between">
                                <h4 class="font-semibold text-gray-800 dark:text-slate-100 capitalize flex items-center">
                                    <i class="ph ph-folder text-slate-400 mr-2"></i> <?php echo htmlspecialchars($module) ?> <?php echo __('roles.module') ?>
                                </h4>
                                <label class="inline-flex items-center cursor-pointer group">
                                    <input type="checkbox" class="module-checkbox w-4 h-4 text-primary-600 border-slate-300 dark:border-slate-500 rounded-sm focus:ring-primary-500 transition-all" data-module="<?php echo $module ?>">
                                    <span class="ml-2 text-xs font-medium text-slate-500 dark:text-slate-400 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors"><?php echo __('roles.all_in_module') ?></span>
                                </label>
                            </div>
                            <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-4">
                                <?php foreach ($perms as $perm): ?>
                                    <label class="inline-flex items-start cursor-pointer group p-2 -m-2 rounded-md hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                                        <div class="pt-0.5">
                                            <input type="checkbox" name="permissions[]" value="<?php echo $perm['id'] ?>" class="perm-checkbox w-4 h-4 text-primary-600 border-slate-300 dark:border-slate-500 rounded-sm focus:ring-primary-500 transition-all shadow-sm" data-module="<?php echo $module ?>">
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

<script>
document.addEventListener('DOMContentLoaded', () => {
    const selectAllBtn = document.getElementById('selectAll');
    let isAllSelected = false;
    selectAllBtn.addEventListener('click', () => {
        isAllSelected = !isAllSelected;
        document.querySelectorAll('.perm-checkbox, .module-checkbox').forEach(cb => cb.checked = isAllSelected);
        selectAllBtn.textContent = isAllSelected ? '<?php echo __('common.deselect_all') ?>' : '<?php echo __('common.select_all') ?>';
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
