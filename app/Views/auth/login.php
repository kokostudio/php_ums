<div class="bg-white dark:bg-black/20 dark:backdrop-blur-md rounded-2xl shadow-xl border border-gray-100 dark:border-slate-800 overflow-hidden transition-colors">
    <div class="p-8">
        <form action="/login" method="POST" class="space-y-5">
            <input type="hidden" name="csrf_token" value="<?= \App\Core\Session::getCsrfToken() ?>">

            <div>
                <label for="username" class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1"><?= __('auth.username') ?></label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400 dark:text-slate-500">
                        <i class="ph ph-user text-lg"></i>
                    </div>
                    <input type="text" id="username" name="username" required autofocus autocomplete="username"
                        class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-colors"
                        placeholder="<?= __('auth.username_placeholder') ?>">
                </div>
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1"><?= __('auth.password') ?></label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400 dark:text-slate-500">
                        <i class="ph ph-lock-key text-lg"></i>
                    </div>
                    <input type="password" id="password" name="password" required autocomplete="current-password"
                        class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-colors"
                        placeholder="<?= __('auth.password_placeholder') ?>">
                </div>
            </div>

            <div class="flex items-center justify-between pt-2">
                <div class="flex items-center">
                    <input id="remember" name="remember" type="checkbox" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 dark:border-slate-700 rounded dark:bg-slate-800">
                    <label for="remember" class="ml-2 block text-sm text-gray-700 dark:text-slate-300"><?= __('auth.remember_me') ?></label>
                </div>
                <div class="text-sm">
                    <a href="/forgot-password" class="font-medium text-blue-600 hover:text-blue-500 dark:text-blue-400"><?= __('auth.forgot_password') ?></a>
                </div>
            </div>

            <div class="pt-2">
                <button type="submit" class="w-full flex justify-center items-center px-4 py-2.5 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-sidebar hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                    <?= __('auth.sign_in') ?> <i class="ph ph-arrow-right ml-2"></i>
                </button>
            </div>
        </form>
    </div>
</div>
