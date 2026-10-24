<div class="bg-white dark:bg-black/20 dark:backdrop-blur-md rounded-2xl shadow-xl border border-gray-100 dark:border-slate-800 overflow-hidden transition-colors">
    <div class="p-8">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2"><?= __('auth.reset_password_title') ?></h2>
        <p class="text-gray-600 dark:text-slate-400 text-sm mb-6"><?= __('auth.reset_password_subtitle') ?></p>

        <form action="/reset-password" method="POST" class="space-y-5">
            <input type="hidden" name="csrf_token" value="<?= \App\Core\Session::getCsrfToken() ?>">
            <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

            <div>
                <label for="new_password" class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1"><?= __('auth.new_password') ?></label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400 dark:text-slate-500">
                        <i class="ph ph-lock-key text-lg"></i>
                    </div>
                    <input type="password" id="new_password" name="new_password" required autofocus
                        class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition-colors"
                        placeholder="••••••••">
                </div>
            </div>

            <div>
                <label for="confirm_password" class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1"><?= __('auth.confirm_password') ?></label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400 dark:text-slate-500">
                        <i class="ph ph-lock-key text-lg"></i>
                    </div>
                    <input type="password" id="confirm_password" name="confirm_password" required
                        class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition-colors"
                        placeholder="••••••••">
                </div>
            </div>

            <div class="pt-2">
                <button type="submit" class="w-full flex justify-center items-center px-4 py-2.5 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-sidebar hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                    <?= __('auth.reset_password_btn') ?> <i class="ph ph-check-circle ml-2"></i>
                </button>
            </div>
        </form>
    </div>
</div>
