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
                <li>
                    <a href="/settings/security" class="flex items-center px-6 py-3 transition-colors <?= $tab === 'security' ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-400 font-medium border-r-2 border-primary-600 dark:border-primary-500' : 'text-gray-500 dark:text-slate-400 hover:bg-primary-50 dark:hover:bg-primary-900/20 hover:text-primary-700 dark:hover:text-primary-300' ?>">
                        <i class="ph ph-shield-check mr-3 text-lg"></i> <?= __('settings.security') ?? 'Security' ?>
                    </a>
                </li>
                <li>
                    <a href="/settings/social" class="flex items-center px-6 py-3 transition-colors <?= $tab === 'social' ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-400 font-medium border-r-2 border-primary-600 dark:border-primary-500' : 'text-gray-500 dark:text-slate-400 hover:bg-primary-50 dark:hover:bg-primary-900/20 hover:text-primary-700 dark:hover:text-primary-300' ?>">
                        <i class="ph ph-share-network mr-3 text-lg"></i> <?= __('settings.social_login') ?? 'Social Login' ?>
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Setting Form -->
    <div class="lg:col-span-3">
        <div class="bg-white dark:bg-black/20 dark:backdrop-blur-md rounded-2xl shadow-sm border border-gray-100 dark:border-slate-800 p-6 md:p-8 transition-colors">

            <?php if ($tab === 'general'): ?>
            <form action="/settings/general" method="POST" enctype="multipart/form-data" id="generalSettingsForm">
                <div class="sticky top-[-24px] md:top-[-32px] z-20 flex items-center justify-between mb-6 border-b border-gray-100 dark:border-slate-800 pb-4 pt-6 md:pt-8 -mt-6 md:-mt-8 -mx-6 md:-mx-8 px-6 md:px-8 bg-white/95 dark:bg-slate-900/95 backdrop-blur-md rounded-t-2xl">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white">
                        <i class="ph ph-sliders mr-2"></i><?= __('settings.general_info') ?>
                    </h3>
                    <button type="submit" class="px-5 py-2 text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 rounded-lg transition-colors shadow-sm flex items-center">
                        <i class="ph ph-check-circle mr-2 text-lg"></i> <?= __('settings.save_settings') ?>
                    </button>
                </div>
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
                                <div class="w-8 h-8 rounded-md bg-slate-900 border border-slate-700 shadow-inner mb-2.5 flex items-center justify-center">
                                    <?php if ($currentTheme === 'slate'): ?><i class="ph ph-check text-white text-xs"></i><?php endif; ?>
                                </div>
                                <span class="text-xs font-semibold text-gray-800 dark:text-slate-200">Slate Dark</span>
                            </label>

                            <!-- Midnight Black -->
                            <label class="relative flex flex-col items-center p-3.5 rounded-xl border cursor-pointer transition-all hover:bg-primary-50 dark:hover:bg-primary-900/20 hover:text-primary-700 dark:hover:text-primary-300/40 <?= $currentTheme === 'black' ? 'border-primary-600 dark:border-primary-500 bg-primary-50/20 dark:bg-primary-900/10 ring-2 ring-primary-500/10' : 'border-gray-200 dark:border-slate-800' ?>">
                                <input type="radio" name="sidebar_theme" value="black" class="sr-only" <?= $currentTheme === 'black' ? 'checked' : '' ?>>
                                <div class="w-8 h-8 rounded-md bg-zinc-950 border border-zinc-800 shadow-inner mb-2.5 flex items-center justify-center">
                                    <?php if ($currentTheme === 'black'): ?><i class="ph ph-check text-white text-xs"></i><?php endif; ?>
                                </div>
                                <span class="text-xs font-semibold text-gray-800 dark:text-slate-200">Midnight</span>
                            </label>

                            <!-- Deep Indigo -->
                            <label class="relative flex flex-col items-center p-3.5 rounded-xl border cursor-pointer transition-all hover:bg-primary-50 dark:hover:bg-primary-900/20 hover:text-primary-700 dark:hover:text-primary-300/40 <?= $currentTheme === 'indigo' ? 'border-primary-600 dark:border-primary-500 bg-primary-50/20 dark:bg-primary-900/10 ring-2 ring-primary-500/10' : 'border-gray-200 dark:border-slate-800' ?>">
                                <input type="radio" name="sidebar_theme" value="indigo" class="sr-only" <?= $currentTheme === 'indigo' ? 'checked' : '' ?>>
                                <div class="w-8 h-8 rounded-md bg-indigo-950 border border-indigo-900/50 shadow-inner mb-2.5 flex items-center justify-center">
                                    <?php if ($currentTheme === 'indigo'): ?><i class="ph ph-check text-white text-xs"></i><?php endif; ?>
                                </div>
                                <span class="text-xs font-semibold text-gray-800 dark:text-slate-200">Deep Indigo</span>
                            </label>

                            <!-- Emerald Forest -->
                            <label class="relative flex flex-col items-center p-3.5 rounded-xl border cursor-pointer transition-all hover:bg-primary-50 dark:hover:bg-primary-900/20 hover:text-primary-700 dark:hover:text-primary-300/40 <?= $currentTheme === 'emerald' ? 'border-primary-600 dark:border-primary-500 bg-primary-50/20 dark:bg-primary-900/10 ring-2 ring-primary-500/10' : 'border-gray-200 dark:border-slate-800' ?>">
                                <input type="radio" name="sidebar_theme" value="emerald" class="sr-only" <?= $currentTheme === 'emerald' ? 'checked' : '' ?>>
                                <div class="w-8 h-8 rounded-md bg-emerald-950 border border-emerald-900/50 shadow-inner mb-2.5 flex items-center justify-center">
                                    <?php if ($currentTheme === 'emerald'): ?><i class="ph ph-check text-white text-xs"></i><?php endif; ?>
                                </div>
                                <span class="text-xs font-semibold text-gray-800 dark:text-slate-200">Emerald</span>
                            </label>

                            <!-- Rose Crimson -->
                            <label class="relative flex flex-col items-center p-3.5 rounded-xl border cursor-pointer transition-all hover:bg-primary-50 dark:hover:bg-primary-900/20 hover:text-primary-700 dark:hover:text-primary-300/40 <?= $currentTheme === 'rose' ? 'border-primary-600 dark:border-primary-500 bg-primary-50/20 dark:bg-primary-900/10 ring-2 ring-primary-500/10' : 'border-gray-200 dark:border-slate-800' ?>">
                                <input type="radio" name="sidebar_theme" value="rose" class="sr-only" <?= $currentTheme === 'rose' ? 'checked' : '' ?>>
                                <div class="w-8 h-8 rounded-md bg-rose-950 border border-rose-900/50 shadow-inner mb-2.5 flex items-center justify-center">
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
                    <label class="flex items-center cursor-pointer mb-4">
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

                    <label class="flex items-center cursor-pointer">
                        <div class="relative">
                            <input type="checkbox" name="enable_registration" value="1" class="sr-only" <?= ($settings['enable_registration'] ?? '1') === '1' ? 'checked' : '' ?>>
                            <div class="block bg-gray-200 dark:bg-slate-700 w-10 h-6 rounded-full transition-colors"></div>
                            <div class="dot absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition-transform"></div>
                        </div>
                        <div class="ml-3 text-sm font-medium text-gray-700 dark:text-slate-300">
                            <?= __('settings.enable_registration') ?? 'เปิดรับสมัครสมาชิก (Enable Registration)' ?>
                            <p class="text-xs text-gray-500 dark:text-slate-400 font-normal mt-0.5"><?= __('settings.enable_registration_desc') ?? 'อนุญาตให้ผู้ใช้ใหม่สามารถสมัครสมาชิกได้' ?></p>
                        </div>
                    </label>
                </div>
                
                <style>
                    input:checked ~ .block { background-color: #ef4444; }
                    input:checked ~ .dot { transform: translateX(100%); }
                </style>

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

                // Copy to clipboard helper
                function copyToClipboard(inputId, btnElement) {
                    var copyText = document.getElementById(inputId);
                    
                    if (!copyText) return;
                    
                    // Select and copy
                    copyText.select();
                    copyText.setSelectionRange(0, 99999); /* For mobile devices */
                    
                    try {
                        navigator.clipboard.writeText(copyText.value).then(function() {
                            // Visual feedback
                            var icon = $(btnElement).find('i');
                            icon.removeClass('ph-copy').addClass('ph-check text-green-500 dark:text-green-400');
                            
                            setTimeout(function() {
                                icon.removeClass('ph-check text-green-500 dark:text-green-400').addClass('ph-copy');
                            }, 2000);
                        });
                    } catch (err) {
                        // Fallback
                        document.execCommand('copy');
                        var icon = $(btnElement).find('i');
                        icon.removeClass('ph-copy').addClass('ph-check text-green-500 dark:text-green-400');
                        setTimeout(function() {
                            icon.removeClass('ph-check text-green-500 dark:text-green-400').addClass('ph-copy');
                        }, 2000);
                    }
                }
            </script>
</body>
</html>            <?php elseif ($tab === 'login'): ?>
            <!-- ===== LOGIN PAGE TAB ===== -->
            <form action="/settings/login" method="POST" enctype="multipart/form-data" id="loginSettingsForm">
                <div class="sticky top-[-24px] md:top-[-32px] z-20 flex items-center justify-between mb-6 border-b border-gray-100 dark:border-slate-800 pb-4 pt-6 md:pt-8 -mt-6 md:-mt-8 -mx-6 md:-mx-8 px-6 md:px-8 bg-white/95 dark:bg-slate-900/95 backdrop-blur-md rounded-t-2xl">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white">
                        <i class="ph ph-sign-in mr-2"></i><?= __('settings.login_page') ?>
                    </h3>
                    <button type="submit" class="px-5 py-2 text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 rounded-lg transition-colors shadow-sm flex items-center">
                        <i class="ph ph-check-circle mr-2 text-lg"></i> <?= __('settings.save_settings') ?>
                    </button>
                </div>
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

                    <div>
                        <label for="login_edition" class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Edition Text</label>
                        <p class="text-xs text-gray-500 dark:text-slate-400 mb-2">ข้อความใต้โลโก้ เช่น ERP EDITION</p>
                        <input type="text" id="login_edition" name="login_edition" 
                            value="<?= htmlspecialchars($settings['login_edition'] ?? 'ERP EDITION') ?>"
                            class="w-full max-w-lg px-4 py-2.5 bg-white dark:bg-slate-800 rounded-lg border border-gray-300 dark:border-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition-colors"
                            placeholder="ERP EDITION">
                    </div>

                    <div class="pt-6 border-t border-gray-100 dark:border-slate-800">
                        <label for="login_pattern" class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">ลายพื้นหลัง (Background Pattern)</label>
                        <p class="text-xs text-gray-500 dark:text-slate-400 mb-2">เลือกลายตกแต่งพื้นหลังสำหรับหน้าจอล็อกอิน</p>
                        <select id="login_pattern" name="login_pattern" class="w-full max-w-lg px-4 py-2.5 bg-white dark:bg-slate-800 rounded-lg border border-gray-300 dark:border-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition-colors">
                            <option value="dots" <?= ($settings['login_pattern'] ?? 'dots') === 'dots' ? 'selected' : '' ?>>จุดและแสงฟุ้ง (Dots & Orbs)</option>
                            <option value="grid" <?= ($settings['login_pattern'] ?? 'dots') === 'grid' ? 'selected' : '' ?>>ตารางมินิมอล (Minimal Grid)</option>
                            <option value="mesh" <?= ($settings['login_pattern'] ?? 'dots') === 'mesh' ? 'selected' : '' ?>>สีไล่ระดับนุ่มนวล (Soft Mesh)</option>
                            <option value="lines" <?= ($settings['login_pattern'] ?? 'dots') === 'lines' ? 'selected' : '' ?>>เส้นทแยงมุม (Diagonal Lines)</option>
                            <option value="glass" <?= ($settings['login_pattern'] ?? 'dots') === 'glass' ? 'selected' : '' ?>>กระจกหรูหรา (Glassmorphism)</option>
                            <option value="none" <?= ($settings['login_pattern'] ?? 'dots') === 'none' ? 'selected' : '' ?>>ไม่มีลาย (None)</option>
                        </select>
                    </div>

                    <div class="pt-6 mt-6 border-t border-gray-100 dark:border-slate-800">
                        <div class="flex items-center justify-between max-w-lg mb-4">
                            <div>
                                <h4 class="text-sm font-semibold text-gray-800 dark:text-white">Feature Cards</h4>
                                <p class="text-xs text-gray-500 dark:text-slate-400">แสดงการ์ดคุณสมบัติ 3 รายการในหน้า Login</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="hidden" name="login_features_enabled" value="0">
                                <input type="checkbox" name="login_features_enabled" value="1" class="sr-only peer" <?= (!isset($settings['login_features_enabled']) || $settings['login_features_enabled'] == '1') ? 'checked' : '' ?>>
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-slate-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>
                        
                        <div class="space-y-4 max-w-lg">
                            <!-- Feature 1 -->
                            <div class="flex gap-3">
                                <div class="w-1/3">
                                    <label class="block text-xs text-gray-500 mb-1">Icon 1 (e.g. ph-shield-check)</label>
                                    <input type="text" name="login_feature1_icon" value="<?= htmlspecialchars($settings['login_feature1_icon'] ?? 'ph-shield-check') ?>" class="w-full px-3 py-2 text-sm bg-white dark:bg-slate-800 rounded border border-gray-300 dark:border-slate-700 text-gray-900 dark:text-white">
                                </div>
                                <div class="w-2/3">
                                    <label class="block text-xs text-gray-500 mb-1">Text 1</label>
                                    <input type="text" name="login_feature1_text" value="<?= htmlspecialchars($settings['login_feature1_text'] ?? 'เข้าสู่ระบบปลอดภัยด้วย SSO') ?>" class="w-full px-3 py-2 text-sm bg-white dark:bg-slate-800 rounded border border-gray-300 dark:border-slate-700 text-gray-900 dark:text-white">
                                </div>
                            </div>
                            <!-- Feature 2 -->
                            <div class="flex gap-3">
                                <div class="w-1/3">
                                    <label class="block text-xs text-gray-500 mb-1">Icon 2 (e.g. ph-package)</label>
                                    <input type="text" name="login_feature2_icon" value="<?= htmlspecialchars($settings['login_feature2_icon'] ?? 'ph-package') ?>" class="w-full px-3 py-2 text-sm bg-white dark:bg-slate-800 rounded border border-gray-300 dark:border-slate-700 text-gray-900 dark:text-white">
                                </div>
                                <div class="w-2/3">
                                    <label class="block text-xs text-gray-500 mb-1">Text 2</label>
                                    <input type="text" name="login_feature2_text" value="<?= htmlspecialchars($settings['login_feature2_text'] ?? 'จัดการสินค้า สต็อก และลูกค้า') ?>" class="w-full px-3 py-2 text-sm bg-white dark:bg-slate-800 rounded border border-gray-300 dark:border-slate-700 text-gray-900 dark:text-white">
                                </div>
                            </div>
                            <!-- Feature 3 -->
                            <div class="flex gap-3">
                                <div class="w-1/3">
                                    <label class="block text-xs text-gray-500 mb-1">Icon 3 (e.g. ph-chart-line-up)</label>
                                    <input type="text" name="login_feature3_icon" value="<?= htmlspecialchars($settings['login_feature3_icon'] ?? 'ph-chart-line-up') ?>" class="w-full px-3 py-2 text-sm bg-white dark:bg-slate-800 rounded border border-gray-300 dark:border-slate-700 text-gray-900 dark:text-white">
                                </div>
                                <div class="w-2/3">
                                    <label class="block text-xs text-gray-500 mb-1">Text 3</label>
                                    <input type="text" name="login_feature3_text" value="<?= htmlspecialchars($settings['login_feature3_text'] ?? 'รายงานและแดชบอร์ดแบบเรียลไทม์') ?>" class="w-full px-3 py-2 text-sm bg-white dark:bg-slate-800 rounded border border-gray-300 dark:border-slate-700 text-gray-900 dark:text-white">
                                </div>
                            </div>
                        </div>
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
                        <label for="login_logo_size" class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">ขนาดโลโก้ในหน้าล็อกอิน (Login Logo Size)</label>
                        <p class="text-xs text-gray-500 dark:text-slate-400 mb-2">ปรับขนาดความสูงของโลโก้ในหน้าล็อกอิน</p>
                        <select id="login_logo_size" name="login_logo_size" class="w-full max-w-lg px-4 py-2.5 bg-white dark:bg-slate-800 rounded-lg border border-gray-300 dark:border-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition-colors">
                            <option value="h-8" <?= ($settings['login_logo_size'] ?? 'h-12') === 'h-8' ? 'selected' : '' ?>>เล็ก (Small)</option>
                            <option value="h-12" <?= ($settings['login_logo_size'] ?? 'h-12') === 'h-12' ? 'selected' : '' ?>>ปานกลาง (Medium - Default)</option>
                            <option value="h-16" <?= ($settings['login_logo_size'] ?? 'h-12') === 'h-16' ? 'selected' : '' ?>>ใหญ่ (Large)</option>
                            <option value="h-24" <?= ($settings['login_logo_size'] ?? 'h-12') === 'h-24' ? 'selected' : '' ?>>ใหญ่มาก (Extra Large)</option>
                            <option value="h-32" <?= ($settings['login_logo_size'] ?? 'h-12') === 'h-32' ? 'selected' : '' ?>>ใหญ่พิเศษ (Jumbo)</option>
                        </select>
                    </div>

                    <div class="pt-4 border-t border-gray-50 dark:border-slate-800">
                        <label for="login_title_size" class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">ขนาดตัวอักษรหัวข้อ (Login Title Size)</label>
                        <p class="text-xs text-gray-500 dark:text-slate-400 mb-2">ปรับขนาดความใหญ่ของหัวข้อในหน้าล็อกอิน</p>
                        <select id="login_title_size" name="login_title_size" class="w-full max-w-lg px-4 py-2.5 bg-white dark:bg-slate-800 rounded-lg border border-gray-300 dark:border-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition-colors">
                            <option value="text-3xl md:text-4xl" <?= ($settings['login_title_size'] ?? 'text-4xl md:text-5xl') === 'text-3xl md:text-4xl' ? 'selected' : '' ?>>เล็ก (Small)</option>
                            <option value="text-4xl md:text-5xl" <?= ($settings['login_title_size'] ?? 'text-4xl md:text-5xl') === 'text-4xl md:text-5xl' ? 'selected' : '' ?>>ปานกลาง (Medium - Default)</option>
                            <option value="text-5xl md:text-6xl" <?= ($settings['login_title_size'] ?? 'text-4xl md:text-5xl') === 'text-5xl md:text-6xl' ? 'selected' : '' ?>>ใหญ่ (Large)</option>
                            <option value="text-6xl md:text-7xl" <?= ($settings['login_title_size'] ?? 'text-4xl md:text-5xl') === 'text-6xl md:text-7xl' ? 'selected' : '' ?>>ใหญ่พิเศษ (Extra Large)</option>
                        </select>
                    </div>

                    <div class="pt-4 border-t border-gray-50 dark:border-slate-800">
                        <label for="login_site_name_size" class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">ขนาดตัวอักษรชื่อโปรแกรม (Program Name Size)</label>
                        <p class="text-xs text-gray-500 dark:text-slate-400 mb-2">ปรับขนาดชื่อโปรแกรมที่แสดงข้างโลโก้ในหน้าล็อกอิน</p>
                        <select id="login_site_name_size" name="login_site_name_size" class="w-full max-w-lg px-4 py-2.5 bg-white dark:bg-slate-800 rounded-lg border border-gray-300 dark:border-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition-colors">
                            <option value="text-lg" <?= ($settings['login_site_name_size'] ?? 'text-xl') === 'text-lg' ? 'selected' : '' ?>>เล็ก (Small)</option>
                            <option value="text-xl" <?= ($settings['login_site_name_size'] ?? 'text-xl') === 'text-xl' ? 'selected' : '' ?>>ปานกลาง (Medium - Default)</option>
                            <option value="text-2xl" <?= ($settings['login_site_name_size'] ?? 'text-xl') === 'text-2xl' ? 'selected' : '' ?>>ใหญ่ (Large)</option>
                            <option value="text-3xl" <?= ($settings['login_site_name_size'] ?? 'text-xl') === 'text-3xl' ? 'selected' : '' ?>>ใหญ่มาก (Extra Large)</option>
                        </select>
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

            <?php elseif ($tab === 'security'): ?>
            <!-- ===== SECURITY TAB ===== -->
            <form action="/settings/security" method="POST" id="securitySettingsForm">
                <div class="sticky top-[-24px] md:top-[-32px] z-20 flex items-center justify-between mb-6 border-b border-gray-100 dark:border-slate-800 pb-4 pt-6 md:pt-8 -mt-6 md:-mt-8 -mx-6 md:-mx-8 px-6 md:px-8 bg-white/95 dark:bg-slate-900/95 backdrop-blur-md rounded-t-2xl">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white">
                        <i class="ph ph-shield-check mr-2"></i><?= __('settings.security') ?? 'Security Settings' ?>
                    </h3>
                    <button type="submit" class="px-5 py-2 text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 rounded-lg transition-colors shadow-sm flex items-center">
                        <i class="ph ph-check-circle mr-2 text-lg"></i> <?= __('settings.save_settings') ?>
                    </button>
                </div>
                <input type="hidden" name="csrf_token" value="<?= \App\Core\Session::getCsrfToken() ?>">

                <div class="space-y-6">
                    <div>
                        <h4 class="text-sm font-semibold text-gray-800 dark:text-white mb-3"><?= __('settings.pwd_policy') ?? 'Password Policy' ?></h4>
                        <div class="space-y-4">
                            <div>
                                <label for="password_min_length" class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1"><?= __('settings.min_pwd_length') ?? 'Minimum Password Length' ?></label>
                                <p class="text-xs text-gray-500 dark:text-slate-400 mb-2"><?= __('settings.min_pwd_length_desc') ?? 'Minimum characters required for new users and password changes.' ?></p>
                                <input type="number" id="password_min_length" name="password_min_length" min="4" max="32" required 
                                    value="<?= htmlspecialchars($settings['password_min_length'] ?? '6') ?>"
                                    class="w-full max-w-lg px-4 py-2.5 bg-white dark:bg-slate-800 rounded-lg border border-gray-300 dark:border-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition-colors">
                            </div>

                            <div class="space-y-2 max-w-lg pt-2">
                                <label class="flex items-center">
                                    <input type="hidden" name="pwd_require_upper" value="0">
                                    <input type="checkbox" name="pwd_require_upper" value="1" <?= (isset($settings['pwd_require_upper']) && $settings['pwd_require_upper'] == '1') ? 'checked' : '' ?> class="rounded border-gray-300 text-primary-600 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-slate-300"><?= __('settings.req_upper') ?? 'Require Uppercase Letter (A-Z)' ?></span>
                                </label>
                                <label class="flex items-center">
                                    <input type="hidden" name="pwd_require_lower" value="0">
                                    <input type="checkbox" name="pwd_require_lower" value="1" <?= (isset($settings['pwd_require_lower']) && $settings['pwd_require_lower'] == '1') ? 'checked' : '' ?> class="rounded border-gray-300 text-primary-600 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-slate-300"><?= __('settings.req_lower') ?? 'Require Lowercase Letter (a-z)' ?></span>
                                </label>
                                <label class="flex items-center">
                                    <input type="hidden" name="pwd_require_number" value="0">
                                    <input type="checkbox" name="pwd_require_number" value="1" <?= (isset($settings['pwd_require_number']) && $settings['pwd_require_number'] == '1') ? 'checked' : '' ?> class="rounded border-gray-300 text-primary-600 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-slate-300"><?= __('settings.req_number') ?? 'Require Number (0-9)' ?></span>
                                </label>
                                <label class="flex items-center">
                                    <input type="hidden" name="pwd_require_special" value="0">
                                    <input type="checkbox" name="pwd_require_special" value="1" <?= (isset($settings['pwd_require_special']) && $settings['pwd_require_special'] == '1') ? 'checked' : '' ?> class="rounded border-gray-300 text-primary-600 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-slate-300"><?= __('settings.req_special') ?? 'Require Special Character (!@#$...)' ?></span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-gray-50 dark:border-slate-800">
                        <h4 class="text-sm font-semibold text-gray-800 dark:text-white mb-3"><?= __('settings.2fa_admin') ?? 'Two-Factor Authentication (2FA)' ?></h4>
                        <label class="flex items-center mb-2">
                            <input type="hidden" name="force_2fa_admin" value="0">
                            <input type="checkbox" name="force_2fa_admin" value="1" <?= (isset($settings['force_2fa_admin']) && $settings['force_2fa_admin'] == '1') ? 'checked' : '' ?> class="rounded border-gray-300 text-primary-600 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50">
                            <span class="ml-2 text-sm font-medium text-gray-700 dark:text-slate-300"><?= __('settings.force_2fa_admin') ?? 'Force 2FA for Admins' ?></span>
                        </label>
                        <p class="text-xs text-gray-500 dark:text-slate-400 max-w-lg"><?= __('settings.force_2fa_admin_desc') ?? 'If enabled, users with Admin privileges will be forced to set up 2FA immediately upon login.' ?></p>
                    </div>

                    <div class="pt-4 border-t border-gray-50 dark:border-slate-800">
                        <h4 class="text-sm font-semibold text-gray-800 dark:text-white mb-3"><?= __('settings.login_security') ?? 'Login Security & Rate Limiting' ?></h4>
                        <div class="space-y-4">
                            <div>
                                <label for="login_max_attempts" class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1"><?= __('settings.max_login_attempts') ?? 'Maximum Login Attempts' ?></label>
                                <p class="text-xs text-gray-500 dark:text-slate-400 mb-2"><?= __('settings.max_login_attempts_desc') ?? 'Number of failed login attempts allowed before a temporary block.' ?></p>
                                <input type="number" id="login_max_attempts" name="login_max_attempts" min="1" max="20" required 
                                    value="<?= htmlspecialchars($settings['login_max_attempts'] ?? '5') ?>"
                                    class="w-full max-w-lg px-4 py-2.5 bg-white dark:bg-slate-800 rounded-lg border border-gray-300 dark:border-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition-colors">
                            </div>

                        </div>
                    </div>
                    <div class="pt-4 border-t border-gray-50 dark:border-slate-800">
                        <label for="session_lifetime" class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1"><?= __('settings.session_lifetime') ?? 'Session Lifetime (Minutes)' ?></label>
                        <p class="text-xs text-gray-500 dark:text-slate-400 mb-2"><?= __('settings.session_lifetime_desc') ?? 'Inactivity period allowed before the user is automatically logged out.' ?></p>
                        <input type="number" id="session_lifetime" name="session_lifetime" min="5" max="10080" required 
                            value="<?= htmlspecialchars($settings['session_lifetime'] ?? '120') ?>"
                            class="w-full max-w-lg px-4 py-2.5 bg-white dark:bg-slate-800 rounded-lg border border-gray-300 dark:border-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition-colors">
                    </div>

                    <div class="pt-4 border-t border-gray-50 dark:border-slate-800">
                        <h4 class="text-sm font-semibold text-gray-800 dark:text-white mb-3"><?= __('settings.email_verification') ?? 'Email Verification' ?></h4>
                        <label class="flex items-start cursor-pointer">
                            <div class="flex items-center h-5">
                                <input type="hidden" name="require_email_verification" value="0">
                                <input type="checkbox" name="require_email_verification" value="1" <?= (isset($settings['require_email_verification']) && $settings['require_email_verification'] == '1') ? 'checked' : '' ?> class="rounded border-gray-300 text-primary-600 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50">
                            </div>
                            <div class="ml-3 text-sm">
                                <span class="font-medium text-gray-700 dark:text-slate-300"><?= __('settings.require_email_verification') ?? 'Require Email Verification' ?></span>
                                <p class="text-gray-500 dark:text-slate-400 mt-1"><?= __('settings.require_email_verification_desc') ?? 'Require new users to verify their email address before they can log in.' ?></p>
                            </div>
                        </label>
                    </div>
                </div>
            </form>

            <?php endif; ?>

            <?php if ($tab === 'social'): ?>
            <form action="/settings/social" method="POST" id="socialSettingsForm">
                <div class="sticky top-[-24px] md:top-[-32px] z-20 flex items-center justify-between mb-6 border-b border-gray-100 dark:border-slate-800 pb-4 pt-6 md:pt-8 -mt-6 md:-mt-8 -mx-6 md:-mx-8 px-6 md:px-8 bg-white/95 dark:bg-slate-900/95 backdrop-blur-md rounded-t-2xl">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white">
                        <i class="ph ph-share-network mr-2"></i><?= __('settings.social_login') ?? 'Social Login' ?>
                    </h3>
                    <button type="submit" class="px-5 py-2 text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 rounded-lg transition-colors shadow-sm flex items-center">
                        <i class="ph ph-check-circle mr-2 text-lg"></i> <?= __('settings.save_settings') ?>
                    </button>
                </div>
                <input type="hidden" name="csrf_token" value="<?= \App\Core\Session::getCsrfToken() ?>">

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 md:gap-8">
                    <!-- Google Settings -->
                    <div class="bg-white dark:bg-slate-800/80 p-5 md:p-6 rounded-2xl border border-gray-200 dark:border-slate-700 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
                        <!-- Decorative accent -->
                        <div class="absolute top-0 left-0 w-1 h-full bg-[#EA4335] opacity-0 group-hover:opacity-100 transition-opacity"></div>
                        
                        <div class="flex items-center justify-between mb-6 border-b border-gray-100 dark:border-slate-700/50 pb-4">
                            <h4 class="text-base font-semibold text-gray-800 dark:text-white flex items-center">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/5/53/Google_%22G%22_Logo.svg" alt="Google" class="w-5 h-5 mr-2">
                                <?= __('settings.google_login') ?? 'Google Login' ?>
                            </h4>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="enable_google_login" value="1" class="sr-only peer" <?= ($settings['enable_google_login'] ?? '0') === '1' ? 'checked' : '' ?>>
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary-300 dark:peer-focus:ring-primary-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-primary-600"></div>
                                <span class="ml-3 text-sm font-medium text-gray-700 dark:text-slate-300"><?= __('settings.enable_google_login') ?? 'Enable Google Login' ?></span>
                            </label>
                        </div>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1"><?= __('settings.google_client_id') ?? 'Client ID' ?></label>
                                <input type="text" name="google_client_id" value="<?= htmlspecialchars($settings['google_client_id'] ?? '') ?>" class="w-full px-4 py-2 bg-white dark:bg-slate-900 border border-gray-300 dark:border-slate-700 rounded-lg text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition-colors">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1"><?= __('settings.google_client_secret') ?? 'Client Secret' ?></label>
                                <input type="text" name="google_client_secret" value="<?= htmlspecialchars($settings['google_client_secret'] ?? '') ?>" class="w-full px-4 py-2 bg-white dark:bg-slate-900 border border-gray-300 dark:border-slate-700 rounded-lg text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition-colors">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1"><?= __('settings.google_redirect_uri') ?? 'Redirect URI' ?></label>
                                <div class="flex items-center gap-2">
                                    <input type="text" id="google_redirect_uri_input" name="google_redirect_uri" value="<?= htmlspecialchars($settings['google_redirect_uri'] ?? \App\Core\Config::get('APP_URL') . '/auth/google/callback') ?>" class="w-full px-4 py-2 bg-gray-50 dark:bg-slate-900/50 border border-gray-200 dark:border-slate-700 rounded-lg text-sm text-gray-500 dark:text-slate-400 focus:outline-none" readonly>
                                    <button type="button" onclick="copyToClipboard('google_redirect_uri_input', this)" class="shrink-0 px-3 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-gray-700 dark:text-slate-300 rounded-lg transition-colors border border-gray-200 dark:border-slate-600" title="Copy to clipboard">
                                        <i class="ph ph-copy text-lg"></i>
                                    </button>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-slate-400 mt-2"><i class="ph ph-info mr-1"></i>Copy this URI to your Google Cloud Console authorized redirect URIs.</p>
                            </div>
                        </div>
                    </div>

                    <!-- LINE Settings -->
                    <div class="bg-white dark:bg-slate-800/80 p-5 md:p-6 rounded-2xl border border-gray-200 dark:border-slate-700 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
                        <!-- Decorative accent -->
                        <div class="absolute top-0 left-0 w-1 h-full bg-[#00B900] opacity-0 group-hover:opacity-100 transition-opacity"></div>
                        
                        <div class="flex items-center justify-between mb-6 border-b border-gray-100 dark:border-slate-700/50 pb-4">
                            <h4 class="text-base font-semibold text-gray-800 dark:text-white flex items-center">
                                <svg class="w-5 h-5 mr-2 text-[#00B900]" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M24 10.304c0-5.369-5.383-9.738-12-9.738-6.616 0-12 4.369-12 9.738 0 4.814 3.963 8.89 9.382 9.615.366.079.866.242 1.002.553.121.282.079.722.037 1.011-.044.29-.205 1.229-.247 1.489-.053.318-.242.977.854.514 1.096-.461 5.922-3.488 8.169-6.028 2.062-2.316 2.803-4.706 2.803-7.154zm-14.717 3.393h-2.613c-.157 0-.285-.129-.285-.285v-5.263c0-.157.128-.285.285-.285.158 0 .285.128.285.285v4.978h2.328c.157 0 .285.128.285.285s-.128.285-.285.285zm3.433 0h-.569c-.157 0-.285-.129-.285-.285v-5.263c0-.157.128-.285.285-.285.158 0 .285.128.285.285v5.263c0 .156-.127.285-.285.285zm4.846-5.548v5.263c0 .156-.128.285-.285.285h-.569c-.046 0-.091-.011-.131-.031-.038-.022-.07-.052-.093-.089l-2.607-3.791v3.626c0 .156-.128.285-.285.285h-.569c-.157 0-.285-.129-.285-.285v-5.263c0-.157.128-.285.285-.285h.569c.046 0 .092.012.131.032.038.021.07.051.093.088l2.607 3.792v-3.627c0-.157.128-.285.285-.285h.569c.158 0 .285.128.285.285z"/>
                                </svg>
                                <?= __('settings.line_login') ?? 'LINE Login' ?>
                            </h4>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="enable_line_login" value="1" class="sr-only peer" <?= ($settings['enable_line_login'] ?? '0') === '1' ? 'checked' : '' ?>>
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary-300 dark:peer-focus:ring-primary-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-primary-600"></div>
                                <span class="ml-3 text-sm font-medium text-gray-700 dark:text-slate-300"><?= __('settings.enable_line_login') ?? 'Enable LINE Login' ?></span>
                            </label>
                        </div>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1"><?= __('settings.line_client_id') ?? 'Channel ID' ?></label>
                                <input type="text" name="line_client_id" value="<?= htmlspecialchars($settings['line_client_id'] ?? '') ?>" class="w-full px-4 py-2 bg-white dark:bg-slate-900 border border-gray-300 dark:border-slate-700 rounded-lg text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition-colors">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1"><?= __('settings.line_client_secret') ?? 'Channel Secret' ?></label>
                                <input type="text" name="line_client_secret" value="<?= htmlspecialchars($settings['line_client_secret'] ?? '') ?>" class="w-full px-4 py-2 bg-white dark:bg-slate-900 border border-gray-300 dark:border-slate-700 rounded-lg text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition-colors">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1"><?= __('settings.line_redirect_uri') ?? 'Callback URL' ?></label>
                                <div class="flex items-center gap-2">
                                    <input type="text" id="line_redirect_uri_input" name="line_redirect_uri" value="<?= htmlspecialchars($settings['line_redirect_uri'] ?? \App\Core\Config::get('APP_URL') . '/auth/line/callback') ?>" class="w-full px-4 py-2 bg-gray-50 dark:bg-slate-900/50 border border-gray-200 dark:border-slate-700 rounded-lg text-sm text-gray-500 dark:text-slate-400 focus:outline-none" readonly>
                                    <button type="button" onclick="copyToClipboard('line_redirect_uri_input', this)" class="shrink-0 px-3 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-gray-700 dark:text-slate-300 rounded-lg transition-colors border border-gray-200 dark:border-slate-600" title="Copy to clipboard">
                                        <i class="ph ph-copy text-lg"></i>
                                    </button>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-slate-400 mt-2"><i class="ph ph-info mr-1"></i>Copy this URI to your LINE Developers Console callback URLs.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
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
