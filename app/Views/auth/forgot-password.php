<div class="bg-white dark:bg-slate-900 rounded-2xl shadow-xl border border-gray-100 dark:border-slate-800 overflow-hidden transition-colors">
    <div class="p-8">
        <div class="mb-6">
            <a href="/login" class="inline-flex items-center text-sm font-medium text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 transition-colors">
                <i class="ph ph-arrow-left mr-1"></i> <?= __('auth.back_to_login') ?>
            </a>
        </div>
        
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2"><?= __('auth.forgot_password_title') ?></h2>
        <p class="text-gray-600 dark:text-slate-400 text-sm mb-6"><?= __('auth.forgot_password_subtitle') ?></p>

        <form action="/forgot-password" method="POST" class="space-y-5">
            <input type="hidden" name="csrf_token" value="<?= \App\Core\Session::getCsrfToken() ?>">

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1"><?= __('auth.email_address') ?></label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="ph ph-envelope text-gray-400 dark:text-slate-500 text-lg"></i>
                    </div>
                    <input type="email" id="email" name="email" required autofocus autocomplete="email"
                        class="w-full pl-10 pr-4 py-2.5 bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all"
                        placeholder="<?= __('auth.email_placeholder') ?>">
                </div>
            </div>

            <div class="pt-2">
                <button type="submit" class="w-full flex justify-center items-center px-4 py-2.5 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                    <?= __('auth.send_reset_link') ?> <i class="ph ph-paper-plane-right ml-2"></i>
                </button>
            </div>
        </form>
    </div>
</div>
