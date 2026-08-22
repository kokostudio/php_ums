<?php $tab = $tab ?? 'general'; ?>
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white"><?= __('settings.title') ?></h1>
        <p class="text-sm text-gray-500 dark:text-slate-400 mt-1"><?= __('settings.manage_desc') ?></p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
    <!-- Sidebar Navigation -->
    <div class="lg:col-span-1">
        <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-800 overflow-hidden transition-colors">
            <ul class="flex flex-col py-2">
                <li>
                    <a href="/settings/general" class="flex items-center px-6 py-3 transition-colors <?= $tab === 'general' ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400 font-medium border-r-2 border-blue-600 dark:border-blue-500' : 'text-gray-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800' ?>">
                        <i class="ph ph-sliders mr-3 text-lg"></i> <?= __('settings.general') ?>
                    </a>
                </li>
                <li>
                    <a href="/settings/login" class="flex items-center px-6 py-3 transition-colors <?= $tab === 'login' ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400 font-medium border-r-2 border-blue-600 dark:border-blue-500' : 'text-gray-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800' ?>">
                        <i class="ph ph-sign-in mr-3 text-lg"></i> <?= __('settings.login_page') ?>
                    </a>
                </li>
                <li>
                    <a href="#" class="flex items-center px-6 py-3 text-gray-400 dark:text-slate-600 opacity-60 cursor-not-allowed" title="<?= __('settings.coming_soon') ?>">
                        <i class="ph ph-envelope-simple mr-3 text-lg"></i> <?= __('settings.email_settings') ?>
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Setting Form -->
    <div class="lg:col-span-3">
        <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-800 p-6 md:p-8 transition-colors">

            <?php if ($tab === 'general'): ?>
            <!-- ===== GENERAL INFO TAB ===== -->
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-6 border-b border-gray-100 dark:border-slate-800 pb-3">
                <i class="ph ph-sliders mr-2"></i><?= __('settings.general_info') ?>
            </h3>
            
            <form action="/settings/general" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?= \App\Core\Session::getCsrfToken() ?>">

                <div class="space-y-6">
                    <div>
                        <label for="site_name" class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1"><?= __('settings.site_name') ?></label>
                        <p class="text-xs text-gray-500 dark:text-slate-400 mb-2"><?= __('settings.site_name_hint') ?></p>
                        <input type="text" id="site_name" name="site_name" required 
                            value="<?= htmlspecialchars($settings['site_name'] ?? 'Standard Core System') ?>"
                            class="w-full max-w-lg px-4 py-2.5 bg-white dark:bg-slate-800 rounded-lg border border-gray-300 dark:border-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-colors"
                            placeholder="e.g. My Awesome App">
                    </div>

                    <div class="pt-4 border-t border-gray-50 dark:border-slate-800">
                        <label for="site_footer" class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1"><?= __('settings.footer_text') ?></label>
                        <p class="text-xs text-gray-500 dark:text-slate-400 mb-2"><?= __('settings.footer_hint') ?></p>
                        <input type="text" id="site_footer" name="site_footer" 
                            value="<?= htmlspecialchars($settings['site_footer'] ?? '&copy; ' . date('Y') . ' Standard Core System. All rights reserved.') ?>"
                            class="w-full max-w-lg px-4 py-2.5 bg-white dark:bg-slate-800 rounded-lg border border-gray-300 dark:border-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-colors"
                            placeholder="e.g. &copy; 2026 My Company">
                    </div>

                    <div class="pt-4 border-t border-gray-50 dark:border-slate-800 grid grid-cols-1 md:grid-cols-2 gap-6 max-w-2xl">
                        <div>
                            <label for="site_logo" class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1"><?= __('settings.site_logo') ?></label>
                            <p class="text-xs text-gray-500 dark:text-slate-400 mb-2"><?= __('settings.site_logo_hint') ?></p>
                            <?php if (!empty($settings['site_logo'])): ?>
                                <div class="mb-3">
                                    <img src="<?= htmlspecialchars($settings['site_logo']) ?>" alt="Logo" class="h-12 object-contain bg-slate-100 dark:bg-slate-800 p-1 rounded">
                                </div>
                            <?php endif; ?>
                            <input type="file" id="site_logo" name="site_logo" accept="image/*" class="block w-full text-sm text-gray-500 dark:text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 dark:file:bg-slate-800 file:text-blue-700 dark:file:text-blue-400 hover:file:bg-blue-100 dark:hover:file:bg-slate-700">
                        </div>

                        <div>
                            <label for="site_favicon" class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1"><?= __('settings.site_favicon') ?></label>
                            <p class="text-xs text-gray-500 dark:text-slate-400 mb-2"><?= __('settings.site_favicon_hint') ?></p>
                            <?php if (!empty($settings['site_favicon'])): ?>
                                <div class="mb-3">
                                    <img src="<?= htmlspecialchars($settings['site_favicon']) ?>" alt="Favicon" class="h-8 w-8 object-contain bg-slate-100 dark:bg-slate-800 p-1 rounded">
                                </div>
                            <?php endif; ?>
                            <input type="file" id="site_favicon" name="site_favicon" accept="image/*" class="block w-full text-sm text-gray-500 dark:text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 dark:file:bg-slate-800 file:text-blue-700 dark:file:text-blue-400 hover:file:bg-blue-100 dark:hover:file:bg-slate-700">
                        </div>
                    </div>
                </div>

                <div class="mt-8 pt-6 border-t border-gray-100 dark:border-slate-800">
                    <button type="submit" class="px-6 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors shadow-sm flex items-center">
                        <i class="ph ph-check-circle mr-2 text-lg"></i> <?= __('settings.save_settings') ?>
                    </button>
                </div>
            </form>

            <?php elseif ($tab === 'login'): ?>
            <!-- ===== LOGIN PAGE TAB ===== -->
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-6 border-b border-gray-100 dark:border-slate-800 pb-3">
                <i class="ph ph-sign-in mr-2"></i><?= __('settings.login_page') ?>
            </h3>

            <form action="/settings/login" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?= \App\Core\Session::getCsrfToken() ?>">

                <div class="space-y-6">
                    <div>
                        <label for="login_title" class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1"><?= __('settings.login_title') ?></label>
                        <p class="text-xs text-gray-500 dark:text-slate-400 mb-2"><?= __('settings.login_title_hint') ?></p>
                        <input type="text" id="login_title" name="login_title" 
                            value="<?= htmlspecialchars($settings['login_title'] ?? 'Standard Core System') ?>"
                            class="w-full max-w-lg px-4 py-2.5 bg-white dark:bg-slate-800 rounded-lg border border-gray-300 dark:border-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-colors"
                            placeholder="e.g. Welcome Back!">
                    </div>

                    <div>
                        <label for="login_subtitle" class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1"><?= __('settings.login_subtitle') ?></label>
                        <p class="text-xs text-gray-500 dark:text-slate-400 mb-2"><?= __('settings.login_subtitle_hint') ?></p>
                        <input type="text" id="login_subtitle" name="login_subtitle" 
                            value="<?= htmlspecialchars($settings['login_subtitle'] ?? 'Sign in to your account') ?>"
                            class="w-full max-w-lg px-4 py-2.5 bg-white dark:bg-slate-800 rounded-lg border border-gray-300 dark:border-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-colors"
                            placeholder="e.g. Please enter your details.">
                    </div>

                    <div class="pt-4 border-t border-gray-50 dark:border-slate-800">
                        <label for="login_logo" class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1"><?= __('settings.login_logo') ?></label>
                        <p class="text-xs text-gray-500 dark:text-slate-400 mb-2"><?= __('settings.login_logo_hint') ?></p>
                        <?php if (!empty($settings['login_logo'])): ?>
                            <div class="mb-3 flex items-center gap-3">
                                <img src="<?= htmlspecialchars($settings['login_logo']) ?>" alt="Login Logo" class="h-16 object-contain bg-slate-100 dark:bg-slate-800 p-2 rounded shadow-sm">
                                <a href="/settings/remove-login-logo" onclick="return confirm('Remove login logo?')" class="text-xs text-red-500 hover:text-red-700 flex items-center gap-1">
                                    <i class="ph ph-trash"></i> <?= __('settings.remove') ?>
                                </a>
                            </div>
                        <?php endif; ?>
                        <input type="file" id="login_logo" name="login_logo" accept="image/*" class="block w-full text-sm text-gray-500 dark:text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 dark:file:bg-slate-800 file:text-blue-700 dark:file:text-blue-400 hover:file:bg-blue-100 dark:hover:file:bg-slate-700">
                    </div>

                    <div class="pt-4 border-t border-gray-50 dark:border-slate-800">
                        <label for="login_background" class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1"><?= __('settings.login_bg') ?></label>
                        <p class="text-xs text-gray-500 dark:text-slate-400 mb-2"><?= __('settings.login_bg_hint') ?></p>
                        <?php if (!empty($settings['login_background'])): ?>
                            <div class="mb-3">
                                <img src="<?= htmlspecialchars($settings['login_background']) ?>" alt="Background" class="h-24 w-full max-w-sm object-cover rounded shadow-sm border border-gray-200 dark:border-slate-700">
                            </div>
                        <?php endif; ?>
                        <input type="file" id="login_background" name="login_background" accept="image/*" class="block w-full text-sm text-gray-500 dark:text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 dark:file:bg-slate-800 file:text-blue-700 dark:file:text-blue-400 hover:file:bg-blue-100 dark:hover:file:bg-slate-700">
                    </div>
                </div>

                <div class="mt-8 pt-6 border-t border-gray-100 dark:border-slate-800">
                    <button type="submit" class="px-6 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors shadow-sm flex items-center">
                        <i class="ph ph-check-circle mr-2 text-lg"></i> <?= __('settings.save_settings') ?>
                    </button>
                </div>
            </form>

            <?php endif; ?>

        </div>
    </div>
</div>
