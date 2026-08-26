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
        <div class="bg-white dark:bg-black/20 dark:backdrop-blur-md rounded-2xl shadow-sm border border-gray-100 dark:border-slate-800 overflow-hidden transition-colors">
            <ul class="flex flex-col py-2">
                <li>
                    <a href="/settings/general" class="flex items-center px-6 py-3 transition-colors <?= $tab === 'general' ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-400 font-medium border-r-2 border-primary-600 dark:border-primary-500' : 'text-gray-500 dark:text-slate-400 hover:bg-primary-50 dark:hover:bg-primary-900/20 hover:text-primary-700 dark:hover:text-primary-300' ?>">
                        <i class="ph ph-sliders mr-3 text-lg"></i> <?= __('settings.general') ?>
                    </a>
                </li>
                <li>
                    <a href="/settings/login" class="flex items-center px-6 py-3 transition-colors <?= $tab === 'login' ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-400 font-medium border-r-2 border-primary-600 dark:border-primary-500' : 'text-gray-500 dark:text-slate-400 hover:bg-primary-50 dark:hover:bg-primary-900/20 hover:text-primary-700 dark:hover:text-primary-300' ?>">
                        <i class="ph ph-sign-in mr-3 text-lg"></i> <?= __('settings.login_page') ?>
                    </a>
                </li>
                <li>
                    <a href="/settings/translations" class="flex items-center px-6 py-3 transition-colors <?= $tab === 'translations' ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-400 font-medium border-r-2 border-primary-600 dark:border-primary-500' : 'text-gray-500 dark:text-slate-400 hover:bg-primary-50 dark:hover:bg-primary-900/20 hover:text-primary-700 dark:hover:text-primary-300' ?>">
                        <i class="ph ph-translate mr-3 text-lg"></i> <?= __('settings.trans_title') ?>
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Setting Form -->
    <div class="lg:col-span-3">
        <div class="bg-white dark:bg-black/20 dark:backdrop-blur-md rounded-2xl shadow-sm border border-gray-100 dark:border-slate-800 p-6 md:p-8 transition-colors">

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
                            class="w-full max-w-lg px-4 py-2.5 bg-white dark:bg-slate-800 rounded-lg border border-gray-300 dark:border-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition-colors"
                            placeholder="<?= __('settings.ph_app_name') ?? 'e.g. My Awesome App' ?>">
                    </div>

                    <div class="pt-4 border-t border-gray-50 dark:border-slate-800">
                        <label for="site_footer" class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1"><?= __('settings.footer_text') ?></label>
                        <p class="text-xs text-gray-500 dark:text-slate-400 mb-2"><?= __('settings.footer_hint') ?></p>
                        <input type="text" id="site_footer" name="site_footer" 
                            value="<?= htmlspecialchars($settings['site_footer'] ?? '&copy; ' . date('Y') . ' Standard Core System. All rights reserved.') ?>"
                            class="w-full max-w-lg px-4 py-2.5 bg-white dark:bg-slate-800 rounded-lg border border-gray-300 dark:border-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition-colors"
                            placeholder="<?= __('settings.ph_footer') ?? 'e.g. &copy; 2026 My Company' ?>">
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
                            <input type="file" id="site_logo" name="site_logo" accept="image/*" class="block w-full text-sm text-gray-500 dark:text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary-50 dark:file:bg-slate-800 file:text-primary-700 dark:file:text-primary-400 hover:file:bg-primary-100 dark:hover:file:bg-slate-700">
                        </div>

                        <div>
                            <label for="site_favicon" class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1"><?= __('settings.site_favicon') ?></label>
                            <p class="text-xs text-gray-500 dark:text-slate-400 mb-2"><?= __('settings.site_favicon_hint') ?></p>
                            <?php if (!empty($settings['site_favicon'])): ?>
                                <div class="mb-3">
                                    <img src="<?= htmlspecialchars($settings['site_favicon']) ?>" alt="Favicon" class="h-8 w-8 object-contain bg-slate-100 dark:bg-slate-800 p-1 rounded">
                                </div>
                            <?php endif; ?>
                            <input type="file" id="site_favicon" name="site_favicon" accept="image/*" class="block w-full text-sm text-gray-500 dark:text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary-50 dark:file:bg-slate-800 file:text-primary-700 dark:file:text-primary-400 hover:file:bg-primary-100 dark:hover:file:bg-slate-700">
                        </div>
                    </div>
                    <div class="pt-4 border-t border-gray-50 dark:border-slate-800">
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1"><?= __('settings.lang_switch_title') ?></label>
                        <p class="text-xs text-gray-500 dark:text-slate-400 mb-3"><?= __('settings.lang_switch_desc') ?></p>
                        
                        <?php $currentLocale = \App\Core\Lang::locale(); ?>
                        <div class="inline-flex bg-gray-100 dark:bg-slate-800 rounded-lg p-1 text-sm font-medium border border-gray-200 dark:border-slate-700">
                            <a href="/lang/th" class="px-6 py-2 rounded-md <?= $currentLocale === 'th' ? 'bg-white dark:bg-slate-900 text-primary-600 dark:text-white shadow-sm font-bold' : 'text-gray-500 dark:text-slate-400 hover:text-gray-800 dark:hover:text-white' ?> transition-all flex items-center">
                                🇹🇭 ภาษาไทย
                            </a>
                            <a href="/lang/en" class="px-6 py-2 rounded-md <?= $currentLocale === 'en' ? 'bg-white dark:bg-slate-900 text-primary-600 dark:text-white shadow-sm font-bold' : 'text-gray-500 dark:text-slate-400 hover:text-gray-800 dark:hover:text-white' ?> transition-all flex items-center">
                                🇬🇧 English
                            </a>
                        </div>
                    </div>

                    <div class="pt-6 border-t border-gray-50 dark:border-slate-800">
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1"><?= __('settings.sidebar_theme') ?? 'ธีมสีของแถบข้าง (Sidebar Color Theme)' ?></label>
                        <p class="text-xs text-gray-500 dark:text-slate-400 mb-3"><?= __('settings.sidebar_theme_desc') ?? 'ปรับเปลี่ยนเฉดสีสำหรับแผงเมนูด้านซ้ายของระบบควบคุมหลัก' ?></p>
                        
                        <?php $currentTheme = $settings['sidebar_theme'] ?? 'slate'; ?>
                        <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 max-w-2xl">
                            <!-- Slate Dark -->
                            <label class="relative flex flex-col items-center p-3.5 rounded-xl border cursor-pointer transition-all hover:bg-primary-50 dark:hover:bg-primary-900/20 hover:text-primary-700 dark:hover:text-primary-300/40 <?= $currentTheme === 'slate' ? 'border-primary-600 dark:border-primary-500 bg-primary-50/20 dark:bg-primary-900/10 ring-2 ring-primary-500/10' : 'border-gray-200 dark:border-slate-800' ?>">
                                <input type="radio" name="sidebar_theme" value="slate" class="sr-only" <?= $currentTheme === 'slate' ? 'checked' : '' ?>>
                                <div class="w-8 h-8 rounded-full bg-slate-900 border border-slate-700 shadow-inner mb-2.5 flex items-center justify-center">
                                    <?php if ($currentTheme === 'slate'): ?><i class="ph ph-check text-white text-xs"></i><?php endif; ?>
                                </div>
                                <span class="text-xs font-semibold text-gray-800 dark:text-slate-200">Slate Dark</span>
                            </label>

                            <!-- Midnight Black -->
                            <label class="relative flex flex-col items-center p-3.5 rounded-xl border cursor-pointer transition-all hover:bg-primary-50 dark:hover:bg-primary-900/20 hover:text-primary-700 dark:hover:text-primary-300/40 <?= $currentTheme === 'black' ? 'border-primary-600 dark:border-primary-500 bg-primary-50/20 dark:bg-primary-900/10 ring-2 ring-primary-500/10' : 'border-gray-200 dark:border-slate-800' ?>">
                                <input type="radio" name="sidebar_theme" value="black" class="sr-only" <?= $currentTheme === 'black' ? 'checked' : '' ?>>
                                <div class="w-8 h-8 rounded-full bg-zinc-950 border border-zinc-800 shadow-inner mb-2.5 flex items-center justify-center">
                                    <?php if ($currentTheme === 'black'): ?><i class="ph ph-check text-white text-xs"></i><?php endif; ?>
                                </div>
                                <span class="text-xs font-semibold text-gray-800 dark:text-slate-200">Midnight</span>
                            </label>

                            <!-- Deep Indigo -->
                            <label class="relative flex flex-col items-center p-3.5 rounded-xl border cursor-pointer transition-all hover:bg-primary-50 dark:hover:bg-primary-900/20 hover:text-primary-700 dark:hover:text-primary-300/40 <?= $currentTheme === 'indigo' ? 'border-primary-600 dark:border-primary-500 bg-primary-50/20 dark:bg-primary-900/10 ring-2 ring-primary-500/10' : 'border-gray-200 dark:border-slate-800' ?>">
                                <input type="radio" name="sidebar_theme" value="indigo" class="sr-only" <?= $currentTheme === 'indigo' ? 'checked' : '' ?>>
                                <div class="w-8 h-8 rounded-full bg-indigo-950 border border-indigo-900/50 shadow-inner mb-2.5 flex items-center justify-center">
                                    <?php if ($currentTheme === 'indigo'): ?><i class="ph ph-check text-white text-xs"></i><?php endif; ?>
                                </div>
                                <span class="text-xs font-semibold text-gray-800 dark:text-slate-200">Deep Indigo</span>
                            </label>

                            <!-- Emerald Forest -->
                            <label class="relative flex flex-col items-center p-3.5 rounded-xl border cursor-pointer transition-all hover:bg-primary-50 dark:hover:bg-primary-900/20 hover:text-primary-700 dark:hover:text-primary-300/40 <?= $currentTheme === 'emerald' ? 'border-primary-600 dark:border-primary-500 bg-primary-50/20 dark:bg-primary-900/10 ring-2 ring-primary-500/10' : 'border-gray-200 dark:border-slate-800' ?>">
                                <input type="radio" name="sidebar_theme" value="emerald" class="sr-only" <?= $currentTheme === 'emerald' ? 'checked' : '' ?>>
                                <div class="w-8 h-8 rounded-full bg-emerald-950 border border-emerald-900/50 shadow-inner mb-2.5 flex items-center justify-center">
                                    <?php if ($currentTheme === 'emerald'): ?><i class="ph ph-check text-white text-xs"></i><?php endif; ?>
                                </div>
                                <span class="text-xs font-semibold text-gray-800 dark:text-slate-200">Emerald</span>
                            </label>

                            <!-- Rose Crimson -->
                            <label class="relative flex flex-col items-center p-3.5 rounded-xl border cursor-pointer transition-all hover:bg-primary-50 dark:hover:bg-primary-900/20 hover:text-primary-700 dark:hover:text-primary-300/40 <?= $currentTheme === 'rose' ? 'border-primary-600 dark:border-primary-500 bg-primary-50/20 dark:bg-primary-900/10 ring-2 ring-primary-500/10' : 'border-gray-200 dark:border-slate-800' ?>">
                                <input type="radio" name="sidebar_theme" value="rose" class="sr-only" <?= $currentTheme === 'rose' ? 'checked' : '' ?>>
                                <div class="w-8 h-8 rounded-full bg-rose-950 border border-rose-900/50 shadow-inner mb-2.5 flex items-center justify-center">
                                    <?php if ($currentTheme === 'rose'): ?><i class="ph ph-check text-white text-xs"></i><?php endif; ?>
                                </div>
                                <span class="text-xs font-semibold text-gray-800 dark:text-slate-200">Rose Crimson</span>
                            </label>
                        </div>
                    </div>

                    <!-- Base Font Size Settings -->
                    <div class="pt-6 border-t border-gray-50 dark:border-slate-800">
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1"><?= __('settings.base_font_size') ?? 'ขนาดตัวอักษรของระบบ (System Font Size)' ?></label>
                        <p class="text-xs text-gray-500 dark:text-slate-400 mb-3"><?= __('settings.base_font_size_desc') ?? 'ปรับขนาดตัวอักษรพื้นฐานสำหรับทุกหน้าจอ' ?></p>
                        
                        <?php $currentFontSize = $settings['base_font_size'] ?? 'text-[16px]'; ?>
                        <div class="grid grid-cols-3 gap-3 max-w-lg">
                            <!-- Small -->
                            <label class="relative flex items-center justify-center p-3 rounded-xl border cursor-pointer transition-all hover:bg-primary-50 dark:hover:bg-primary-900/20 <?= $currentFontSize === 'text-[14px]' ? 'border-primary-600 dark:border-primary-500 bg-primary-50/20 dark:bg-primary-900/10 ring-2 ring-primary-500/10' : 'border-gray-200 dark:border-slate-800' ?>">
                                <input type="radio" name="base_font_size" value="text-[14px]" class="sr-only" <?= $currentFontSize === 'text-[14px]' ? 'checked' : '' ?>>
                                <span class="text-sm font-semibold text-gray-800 dark:text-slate-200">เล็ก (Small)</span>
                            </label>

                            <!-- Medium -->
                            <label class="relative flex items-center justify-center p-3 rounded-xl border cursor-pointer transition-all hover:bg-primary-50 dark:hover:bg-primary-900/20 <?= $currentFontSize === 'text-[16px]' ? 'border-primary-600 dark:border-primary-500 bg-primary-50/20 dark:bg-primary-900/10 ring-2 ring-primary-500/10' : 'border-gray-200 dark:border-slate-800' ?>">
                                <input type="radio" name="base_font_size" value="text-[16px]" class="sr-only" <?= $currentFontSize === 'text-[16px]' ? 'checked' : '' ?>>
                                <span class="text-base font-semibold text-gray-800 dark:text-slate-200">ปกติ (Medium)</span>
                            </label>

                            <!-- Large -->
                            <label class="relative flex items-center justify-center p-3 rounded-xl border cursor-pointer transition-all hover:bg-primary-50 dark:hover:bg-primary-900/20 <?= $currentFontSize === 'text-[18px]' ? 'border-primary-600 dark:border-primary-500 bg-primary-50/20 dark:bg-primary-900/10 ring-2 ring-primary-500/10' : 'border-gray-200 dark:border-slate-800' ?>">
                                <input type="radio" name="base_font_size" value="text-[18px]" class="sr-only" <?= $currentFontSize === 'text-[18px]' ? 'checked' : '' ?>>
                                <span class="text-lg font-semibold text-gray-800 dark:text-slate-200">ใหญ่ (Large)</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="mt-6 pt-6 border-t border-gray-50 dark:border-slate-800">
                    <h4 class="text-sm font-semibold text-gray-800 dark:text-white mb-4"><i class="ph ph-shield-warning mr-2 text-amber-500"></i><?= __('settings.system_status') ?></h4>
                    <label class="flex items-center cursor-pointer">
                        <div class="relative">
                            <input type="checkbox" name="maintenance_mode" value="1" class="sr-only" <?= ($settings['maintenance_mode'] ?? '0') === '1' ? 'checked' : '' ?>>
                            <div class="block bg-gray-200 dark:bg-slate-700 w-10 h-6 rounded-full transition-colors"></div>
                            <div class="dot absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition-transform"></div>
                        </div>
                        <div class="ml-3 text-sm font-medium text-gray-700 dark:text-slate-300">
                            <?= __('settings.maintenance_mode') ?>
                            <p class="text-xs text-gray-500 dark:text-slate-400 font-normal mt-0.5"><?= __('settings.maintenance_mode_desc') ?></p>
                        </div>
                    </label>
                </div>
                
                <style>
                    input:checked ~ .block { background-color: #ef4444; }
                    input:checked ~ .dot { transform: translateX(100%); }
                </style>

                <div class="mt-8 pt-6 border-t border-gray-100 dark:border-slate-800">
                    <button type="submit" class="px-6 py-2.5 text-sm font-medium text-white bg-sidebar rounded-lg hover:opacity-90 transition-colors shadow-sm flex items-center">
                        <i class="ph ph-check-circle mr-2 text-lg"></i> <?= __('settings.save_settings') ?>
                    </button>
                </div>
            </form>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const themeRadios = document.querySelectorAll('input[name="sidebar_theme"]');
                    const activeClasses = ['border-primary-600', 'dark:border-primary-500', 'bg-primary-50/20', 'dark:bg-primary-900/10', 'ring-2', 'ring-primary-500/10'];
                    const inactiveClasses = ['border-gray-200', 'dark:border-slate-800'];
                    
                    themeRadios.forEach(radio => {
                        radio.addEventListener('change', function() {
                            // Reset all labels
                            themeRadios.forEach(r => {
                                const label = r.closest('label');
                                label.classList.remove(...activeClasses);
                                label.classList.add(...inactiveClasses);
                                const icon = label.querySelector('.ph-check');
                                if (icon) icon.remove();
                            });
                            
                            // Set active label
                            if (this.checked) {
                                const activeLabel = this.closest('label');
                                activeLabel.classList.remove(...inactiveClasses);
                                activeLabel.classList.add(...activeClasses);
                                
                                const iconContainer = activeLabel.querySelector('div.w-8.h-8');
                                if (iconContainer && !activeLabel.querySelector('.ph-check')) {
                                    iconContainer.insertAdjacentHTML('beforeend', '<i class="ph ph-check text-white text-xs"></i>');
                                }
                            }
                        });
                    });
                });
            </script>

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
                            class="w-full max-w-lg px-4 py-2.5 bg-white dark:bg-slate-800 rounded-lg border border-gray-300 dark:border-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition-colors"
                            placeholder="<?= __('settings.ph_welcome') ?? 'e.g. Welcome Back!' ?>">
                    </div>

                    <div>
                        <label for="login_subtitle" class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1"><?= __('settings.login_subtitle') ?></label>
                        <p class="text-xs text-gray-500 dark:text-slate-400 mb-2"><?= __('settings.login_subtitle_hint') ?></p>
                        <input type="text" id="login_subtitle" name="login_subtitle" 
                            value="<?= htmlspecialchars($settings['login_subtitle'] ?? 'Sign in to your account') ?>"
                            class="w-full max-w-lg px-4 py-2.5 bg-white dark:bg-slate-800 rounded-lg border border-gray-300 dark:border-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition-colors"
                            placeholder="<?= __('settings.ph_login_sub') ?? 'e.g. Please enter your details.' ?>">
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
                        <input type="file" id="login_logo" name="login_logo" accept="image/*" class="block w-full text-sm text-gray-500 dark:text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary-50 dark:file:bg-slate-800 file:text-primary-700 dark:file:text-primary-400 hover:file:bg-primary-100 dark:hover:file:bg-slate-700">
                    </div>

                    <div class="pt-4 border-t border-gray-50 dark:border-slate-800">
                        <label for="login_background" class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1"><?= __('settings.login_bg') ?></label>
                        <p class="text-xs text-gray-500 dark:text-slate-400 mb-2"><?= __('settings.login_bg_hint') ?></p>
                        <?php if (!empty($settings['login_background'])): ?>
                            <div class="mb-3">
                                <img src="<?= htmlspecialchars($settings['login_background']) ?>" alt="Background" class="h-24 w-full max-w-sm object-cover rounded shadow-sm border border-gray-200 dark:border-slate-700">
                            </div>
                        <?php endif; ?>
                        <input type="file" id="login_background" name="login_background" accept="image/*" class="block w-full text-sm text-gray-500 dark:text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary-50 dark:file:bg-slate-800 file:text-primary-700 dark:file:text-primary-400 hover:file:bg-primary-100 dark:hover:file:bg-slate-700">
                    </div>
                </div>

                <div class="mt-8 pt-6 border-t border-gray-100 dark:border-slate-800">
                    <button type="submit" class="px-6 py-2.5 text-sm font-medium text-white bg-sidebar rounded-lg hover:opacity-90 transition-colors shadow-sm flex items-center">
                        <i class="ph ph-check-circle mr-2 text-lg"></i> <?= __('settings.save_settings') ?>
                    </button>
                </div>
            </form>

            <?php elseif ($tab === 'translations'): ?>
            <!-- ===== TRANSLATIONS TAB ===== -->
            <?php 
                $namespaces = [];
                if (isset($translations)) {
                    $namespaces = array_unique(array_column($translations, 'namespace'));
                    sort($namespaces);
                }
            ?>
            <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-6 border-b border-gray-100 dark:border-slate-800 pb-3 gap-4">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-0">
                    <i class="ph ph-translate mr-2"></i><?= __('settings.trans_title') ?>
                </h3>
                <div class="flex items-center gap-3 w-full sm:w-auto">
                    <select id="namespaceFilter" class="w-full sm:w-48 pl-3 pr-8 py-2 bg-gray-50 dark:bg-slate-800/50 border border-gray-200 dark:border-slate-700 rounded-lg text-sm text-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500/50 focus:border-primary-500 transition-all appearance-none cursor-pointer">
                        <option value=""><?= __('common.all') ?? 'All Namespaces' ?></option>
                        <?php foreach ($namespaces as $ns): ?>
                            <option value="<?= htmlspecialchars($ns) ?>"><?= htmlspecialchars($ns) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <div class="relative w-full sm:w-auto">
                        <i class="ph ph-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <input type="text" id="customTranslationSearch" placeholder="<?= __('common.search') ?? 'Search...' ?>" class="w-full sm:w-72 pl-10 pr-4 py-2 bg-gray-50 dark:bg-slate-800/50 border border-gray-200 dark:border-slate-700 rounded-lg text-sm text-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500/50 focus:border-primary-500 transition-all placeholder-gray-400 dark:placeholder-slate-500">
                    </div>
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table id="translationsTable" class="w-full text-left border-collapse whitespace-nowrap">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-800 text-slate-500 dark:text-slate-400 text-xs uppercase tracking-wider">
                            <th class="p-4 font-medium rounded-tl-lg"><?= __('settings.trans_namespace') ?></th>
                            <th class="p-4 font-medium"><?= __('settings.trans_key') ?></th>
                            <th class="p-4 font-medium"><?= __('settings.trans_th_value') ?></th>
                            <th class="p-4 font-medium"><?= __('settings.trans_en_value') ?></th>
                            <th class="p-4 font-medium rounded-tr-lg text-right"><?= __('common.actions') ?></th>
                        </tr>
                    </thead>
                    <tbody class="text-sm text-gray-700 dark:text-slate-300">
                        <?php if (isset($translations)): ?>
                        <?php foreach ($translations as $groupKey => $row): ?>
                        <tr class="border-b border-gray-50 dark:border-slate-800 hover:bg-primary-50/50 dark:hover:bg-primary-900/20 transition-colors">
                            <td class="p-4"><span class="px-2 py-1 bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400 rounded-lg text-xs font-bold"><?= htmlspecialchars($row['namespace']) ?></span></td>
                            <td class="p-4 font-mono text-xs"><?= htmlspecialchars($row['key_name']) ?></td>
                            <td class="p-4 truncate max-w-[200px]" title="<?= htmlspecialchars($row['th'] ?? '') ?>"><?= htmlspecialchars($row['th'] ?? '') ?></td>
                            <td class="p-4 truncate max-w-[200px]" title="<?= htmlspecialchars($row['en'] ?? '') ?>"><?= htmlspecialchars($row['en'] ?? '') ?></td>
                            <td class="p-4 text-right">
                                <button type="button" onclick="editTranslation('<?= htmlspecialchars($row['namespace']) ?>', '<?= htmlspecialchars($row['key_name']) ?>', '<?= htmlspecialchars(addslashes($row['th'] ?? '')) ?>', '<?= htmlspecialchars(addslashes($row['en'] ?? '')) ?>')" class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-primary-600 dark:text-primary-400 hover:bg-primary-50 dark:hover:bg-primary-900/30 transition-colors tooltip" data-tippy-content="<?= __('common.edit') ?>">
                                    <i class="ph ph-pencil-simple text-lg"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Edit Translation Modal -->
            <div id="editTranslationModal" class="fixed inset-0 z-50 hidden bg-gray-900/50 dark:bg-black/70 backdrop-blur-sm flex items-center justify-center p-4">
                <div class="bg-white dark:bg-black/20 dark:backdrop-blur-md rounded-2xl shadow-xl w-full max-w-lg overflow-hidden flex flex-col border border-gray-100 dark:border-slate-800 transition-colors">
                    <form id="editTranslationForm" onsubmit="saveTranslation(event)">
                        <input type="hidden" name="csrf_token" value="<?= \App\Core\Session::getCsrfToken() ?>">
                        <input type="hidden" id="edit_namespace" name="namespace">
                        <input type="hidden" id="edit_key" name="key_name">

                        <div class="flex justify-between items-center px-6 py-4 border-b border-gray-100 dark:border-slate-800">
                            <h3 class="text-lg font-bold text-gray-800 dark:text-white flex items-center">
                                <i class="ph ph-translate text-primary-500 dark:text-primary-400 mr-2 text-xl"></i> <?= __('settings.trans_edit') ?>
                            </h3>
                            <button type="button" onclick="closeTranslationModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-slate-200 transition-colors p-1">
                                <i class="ph ph-x text-xl"></i>
                            </button>
                        </div>
                        <div class="p-6 overflow-y-auto bg-slate-50 dark:bg-slate-950/50 space-y-4">
                            <div class="bg-primary-50 dark:bg-primary-900/20 p-3 rounded-lg border border-primary-100 dark:border-primary-800/30 flex items-center mb-4">
                                <i class="ph ph-info mr-2 text-primary-600 dark:text-primary-400"></i>
                                <span class="text-sm text-primary-800 dark:text-primary-300 font-mono" id="display_key"></span>
                            </div>
                            
                            <div>
                                <label for="th_value" class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1"><?= __('settings.trans_th_label') ?></label>
                                <textarea id="th_value" name="th_value" rows="3" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-700 rounded-lg text-sm bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:outline-none focus:border-primary-500 focus:ring-1 focus:ring-primary-500"></textarea>
                            </div>
                            
                            <div>
                                <label for="en_value" class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1"><?= __('settings.trans_en_label') ?></label>
                                <textarea id="en_value" name="en_value" rows="3" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-700 rounded-lg text-sm bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:outline-none focus:border-primary-500 focus:ring-1 focus:ring-primary-500"></textarea>
                            </div>
                        </div>
                        <div class="px-6 py-4 border-t border-gray-100 dark:border-slate-800 bg-white dark:bg-black/20 dark:backdrop-blur-md flex justify-end gap-3">
                            <button type="button" onclick="closeTranslationModal()" class="px-5 py-2 text-sm font-medium text-gray-700 dark:text-slate-300 bg-white dark:bg-slate-800 border border-gray-300 dark:border-slate-700 rounded-lg hover:bg-gray-50 dark:hover:bg-primary-900/30 transition-colors">
                                <?= __('common.cancel') ?>
                            </button>
                            <button type="submit" class="px-5 py-2 text-sm font-medium text-white bg-sidebar rounded-lg hover:opacity-90 transition-colors shadow-sm">
                                <?= __('common.save') ?>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <script>
            $(document).ready(function() {
                if ($('#translationsTable').length) {
                    var table = $('#translationsTable').DataTable({
                        pageLength: 25,
                        dom: 'rt<"bottom flex flex-col sm:flex-row justify-between items-center mt-4"lip><"clear">'
                    });

                    $('#customTranslationSearch').on('keyup', function() {
                        table.search(this.value).draw();
                    });

                    $('#namespaceFilter').on('change', function() {
                        var val = $.fn.dataTable.util.escapeRegex($(this).val());
                        // Column 0 is the Namespace column
                        table.column(0).search(val ? '^' + val + '$' : '', true, false).draw();
                    });
                }
            });

            function editTranslation(ns, key, th, en) {
                $('#edit_namespace').val(ns);
                $('#edit_key').val(key);
                $('#th_value').val(th);
                $('#en_value').val(en);
                $('#display_key').text(ns + '.' + key);
                $('#editTranslationModal').removeClass('hidden');
            }

            function closeTranslationModal() {
                $('#editTranslationModal').addClass('hidden');
            }

            function saveTranslation(e) {
                e.preventDefault();
                
                $.ajax({
                    url: '/settings/translations/update',
                    type: 'POST',
                    data: $('#editTranslationForm').serialize(),
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: '<?= __('common.success') ?>',
                                text: response.message,
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: '<?= __('common.error') ?>',
                                text: response.message
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: '<?= __('common.error') ?>',
                            text: 'An unexpected error occurred.'
                        });
                    }
                });
            }
            </script>

            <?php endif; ?>

        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        // Toggle active visual state for setting cards
        $('input[type="radio"]').on('change', function() {
            var groupName = $(this).attr('name');
            var activeClasses = 'border-primary-600 dark:border-primary-500 bg-primary-50/20 dark:bg-primary-900/10 ring-2 ring-primary-500/10';
            var inactiveClasses = 'border-gray-200 dark:border-slate-800';

            // Remove active classes from all labels in this group
            $('input[name="' + groupName + '"]').closest('label').removeClass(activeClasses).addClass(inactiveClasses);
            
            // Add active classes to the checked one
            $(this).closest('label').removeClass(inactiveClasses).addClass(activeClasses);

            // Special case for check icon in sidebar theme
            if(groupName === 'sidebar_theme') {
                $('input[name="sidebar_theme"]').siblings('div').find('i.ph-check').remove();
                $(this).siblings('div').append('<i class="ph ph-check text-white text-xs"></i>');
            }
            
            // Special case for instant font size preview
            if(groupName === 'base_font_size') {
                $('html').removeClass('text-[14px] text-[16px] text-[18px]').addClass($(this).val());
            }
        });
    });
</script>
