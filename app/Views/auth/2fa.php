<div class="bg-white dark:bg-slate-900 rounded-2xl shadow-xl border border-gray-100 dark:border-slate-800 overflow-hidden transition-colors">
    <div class="p-8">
        
        <div class="text-center mb-6">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-blue-50 dark:bg-blue-900/30 mb-4">
                <i class="ph ph-shield-check text-3xl text-blue-600 dark:text-blue-400"></i>
            </div>
            <p class="text-sm text-gray-600 dark:text-slate-400">
                <?= __('auth.2fa_desc') ?>
            </p>
        </div>

        <form action="/login/2fa" method="POST" class="space-y-6">
            <input type="hidden" name="csrf_token" value="<?= \App\Core\Session::getCsrfToken() ?>">

            <div>
                <label for="totp_code" class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2 text-center"><?= __('auth.verification_code') ?></label>
                <input type="text" id="totp_code" name="totp_code" required autofocus autocomplete="one-time-code" maxlength="6" pattern="[0-9]{6}"
                    class="w-full px-4 py-3 text-center text-2xl tracking-[0.5em] font-mono rounded-lg border border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-colors"
                    placeholder="------">
            </div>

            <div class="pt-2">
                <button type="submit" class="w-full flex justify-center items-center px-4 py-3 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                    <?= __('auth.verify_and_login') ?> <i class="ph ph-check-circle ml-2"></i>
                </button>
            </div>
            
            <div class="text-center mt-4">
                <a href="/login" class="text-sm text-gray-500 dark:text-slate-400 hover:text-gray-700 dark:hover:text-slate-200 transition-colors">
                    <?= __('auth.back_to_login') ?>
                </a>
            </div>
        </form>
    </div>
</div>
