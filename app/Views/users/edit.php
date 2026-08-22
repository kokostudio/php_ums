<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white"><?= __('users.edit_user') ?></h1>
        <div class="text-sm text-gray-500 dark:text-slate-400 mt-1 flex items-center">
            <a href="/users" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors"><?= __('common.user_management') ?></a>
            <i class="ph ph-caret-right mx-2 text-xs"></i>
            <span class="text-gray-700 dark:text-slate-300"><?= __('users.edit_crumb') ?></span>
            <i class="ph ph-caret-right mx-2 text-xs"></i>
            <span class="text-gray-700 dark:text-slate-300"><?= htmlspecialchars($user['username']) ?></span>
        </div>
    </div>
</div>

<div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-800 p-6 md:p-8 max-w-3xl transition-colors">
    <form action="/users/<?= \App\Core\Security::encodeId($user['id']) ?>/edit" method="POST">
        <input type="hidden" name="csrf_token" value="<?= \App\Core\Session::getCsrfToken() ?>">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- Username -->
            <div>
                <label for="username" class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1"><?= __('users.username') ?> <span class="text-red-500">*</span></label>
                <input type="text" id="username" name="username" required 
                    value="<?= htmlspecialchars($user['username']) ?>"
                    class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-colors">
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1"><?= __('users.email') ?> <span class="text-red-500">*</span></label>
                <input type="email" id="email" name="email" required 
                    value="<?= htmlspecialchars($user['email']) ?>"
                    class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-colors">
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1"><?= __('users.new_password') ?></label>
                <input type="password" id="password" name="password" 
                    class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-colors"
                    placeholder="<?= __('users.password_hint') ?>">
                <p class="text-xs text-gray-400 dark:text-slate-500 mt-1"><?= __('users.password_hint') ?></p>
            </div>

            <!-- Role -->
            <div>
                <label for="role_id" class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1"><?= __('users.assign_role') ?></label>
                <select id="role_id" name="role_id" 
                    class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-colors">
                    <option value=""><?= __('users.no_role') ?></option>
                    <?php foreach ($roles as $role): ?>
                        <option value="<?= \App\Core\Security::encodeId($role['id']) ?>" <?= $user['role_id'] == $role['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($role['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Status -->
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1"><?= __('users.status') ?></label>
                <select id="status" name="status" 
                    class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-colors">
                    <option value="active" <?= $user['status'] === 'active' ? 'selected' : '' ?>><?= __('common.active') ?></option>
                    <option value="inactive" <?= $user['status'] === 'inactive' ? 'selected' : '' ?>><?= __('common.inactive') ?></option>
                    <option value="banned" <?= $user['status'] === 'banned' ? 'selected' : '' ?>><?= __('common.banned') ?></option>
                </select>
            </div>
        </div>

        <div class="flex items-center justify-end border-t border-gray-100 dark:border-slate-800 pt-6 mt-6 space-x-3">
            <a href="/users" class="px-5 py-2.5 text-sm font-medium text-gray-700 dark:text-slate-300 bg-white dark:bg-slate-800 border border-gray-300 dark:border-slate-700 rounded-lg hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">
                <?= __('common.cancel') ?>
            </a>
            <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors shadow-sm flex items-center">
                <i class="ph ph-floppy-disk mr-2 text-lg"></i> <?= __('users.update_user') ?>
            </button>
        </div>
    </form>
</div>
