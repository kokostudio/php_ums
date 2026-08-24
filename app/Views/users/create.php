<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white"><?= __('users.add_user') ?></h1>
        <div class="text-sm text-gray-500 dark:text-slate-400 mt-1 flex items-center">
            <a href="/users" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors"><?= __('common.user_management') ?></a>
            <i class="ph ph-caret-right mx-2 text-xs"></i>
            <span class="text-gray-700 dark:text-slate-300"><?= __('users.create_crumb') ?></span>
        </div>
    </div>
</div>

<div class="bg-white dark:bg-black/20 dark:backdrop-blur-md rounded-2xl shadow-sm border border-gray-100 dark:border-slate-800 p-6 md:p-8 max-w-3xl transition-colors">
    <form action="/users/create" method="POST">
        <input type="hidden" name="csrf_token" value="<?= \App\Core\Session::getCsrfToken() ?>">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- Username -->
            <div>
                <label for="username" class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1"><?= __('users.username') ?> <span class="text-red-500">*</span></label>
                <input type="text" id="username" name="username" required 
                    class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-colors"
                    placeholder="john_doe">
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1"><?= __('users.email') ?> <span class="text-red-500">*</span></label>
                <input type="email" id="email" name="email" required 
                    class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-colors"
                    placeholder="john@example.com">
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1"><?= __('users.password') ?> <span class="text-red-500">*</span></label>
                <input type="password" id="password" name="password" required 
                    class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-colors"
                    placeholder="••••••••">
            </div>

            <!-- Role -->
            <div>
                <label for="role_id" class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1"><?= __('users.assign_role') ?></label>
                <select id="role_id" name="role_id" 
                    class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-colors">
                    <option value=""><?= __('users.no_role') ?></option>
                    <?php foreach ($roles as $role): ?>
                        <option value="<?= \App\Core\Security::encodeId($role['id']) ?>"><?= htmlspecialchars($role['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Status -->
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1"><?= __('users.status') ?></label>
                <select id="status" name="status" 
                    class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-colors">
                    <option value="active"><?= __('common.active') ?></option>
                    <option value="inactive"><?= __('common.inactive') ?></option>
                    <option value="banned"><?= __('common.banned') ?></option>
                </select>
            </div>
        </div>

        <div class="flex items-center justify-end border-t border-gray-100 dark:border-slate-800 pt-6 mt-6 space-x-3">
            <a href="/users" class="px-5 py-2.5 text-sm font-medium text-gray-700 dark:text-slate-300 bg-white dark:bg-slate-800 border border-gray-300 dark:border-slate-700 rounded-lg hover:bg-primary-50 dark:hover:bg-primary-900/20 hover:text-primary-600 dark:hover:text-primary-300 hover:border-primary-200 dark:hover:border-primary-800 transition-colors">
                <?= __('common.cancel') ?>
            </a>
            <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-sidebar rounded-lg hover:opacity-90 transition-colors shadow-sm flex items-center">
                <i class="ph ph-floppy-disk mr-2 text-lg"></i> <?= __('users.save_user') ?>
            </button>
        </div>
    </form>
</div>
