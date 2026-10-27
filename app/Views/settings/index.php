<?php
    use App\Core\Settings;
    use App\Core\Theme;
    use App\Core\ThemeColor;

    $tab = $tab ?? 'general';
?>
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white"><?= __('settings.title') ?></h1>
        <p class="text-sm text-gray-500 dark:text-slate-400 mt-1"><?= __('settings.manage_desc') ?></p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-4 gap-6 items-start">
    <!-- Sidebar Navigation -->
    <div class="lg:col-span-1 lg:sticky lg:top-6 self-start z-10">
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
                <li>
                    <a href="/settings/design_spec" class="flex items-center px-6 py-3 transition-colors <?= $tab === 'design_spec' ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-400 font-medium border-r-2 border-primary-600 dark:border-primary-500' : 'text-gray-500 dark:text-slate-400 hover:bg-primary-50 dark:hover:bg-primary-900/20 hover:text-primary-700 dark:hover:text-primary-300' ?>">
                        <i class="ph ph-file-text mr-3 text-lg text-indigo-500"></i> <?= __('settings.design_spec') ?>
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

                    <!-- Brand Icon & Logo / Favicon Customization System -->
                    <?php
                        $logoType      = $settings['logo_type'] ?? 'icon';
                        $selectedIcon  = $settings['logo_icon'] ?? \App\Core\BrandIcon::DEFAULT_ICON;
                        $selectedColor = $settings['logo_icon_color'] ?? \App\Core\BrandIcon::DEFAULT_COLOR;
                        $selectedColor = \App\Core\ThemeColor::normalize($selectedColor) ?? \App\Core\BrandIcon::DEFAULT_COLOR;
                        $selectedSize  = (int) ($settings['logo_icon_size'] ?? \App\Core\BrandIcon::DEFAULT_SIZE);
                        $selectedSize  = max(20, min(64, $selectedSize));

                        $faviconSync   = ($settings['favicon_sync_logo'] ?? '1') === '1';
                        $faviconType   = $settings['favicon_type'] ?? 'icon';
                        $favBg         = $settings['favicon_bg_color'] ?? 'transparent';

                        $allBrandIcons = \App\Core\BrandIcon::all();
                        $quickColors   = [
                            ['#3b82f6', 'Blue'],
                            ['#6366f1', 'Indigo'],
                            ['#8b5cf6', 'Purple'],
                            ['#10b981', 'Emerald'],
                            ['#f59e0b', 'Amber'],
                            ['#f43f5e', 'Rose'],
                            ['#0ea5e9', 'Sky'],
                            ['#64748b', 'Slate'],
                        ];
                        $sizePresets   = [
                            24 => __('settings.size_small') ?? 'เล็ก (24px)',
                            32 => __('settings.size_default') ?? 'มาตรฐาน (32px)',
                            40 => __('settings.size_medium') ?? 'กลาง (40px)',
                            48 => __('settings.size_large') ?? 'ใหญ่ (48px)',
                        ];
                    ?>
                    <div class="pt-6 border-t border-gray-100 dark:border-slate-800">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
                            <div>
                                <h4 class="text-base font-semibold text-gray-800 dark:text-white flex items-center">
                                    <i class="ph ph-cube-transparent text-primary-500 mr-2 text-xl"></i>
                                    <?= __('settings.brand_icon_title') ?>
                                </h4>
                                <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">
                                    <?= __('settings.brand_icon_desc') ?>
                                </p>
                            </div>

                            <!-- Mode Switcher: Vector Icon vs Custom Image -->
                            <div class="inline-flex p-1 bg-gray-100 dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 self-start sm:self-auto">
                                <button type="button" id="btnModeIcon" onclick="switchBrandMode('icon')"
                                    class="px-3.5 py-1.5 rounded-lg text-xs font-semibold transition-all flex items-center gap-1.5 <?= $logoType === 'icon' ? 'bg-white dark:bg-slate-900 text-primary-600 dark:text-primary-400 shadow-sm' : 'text-gray-500 dark:text-slate-400 hover:text-gray-800 dark:hover:text-white' ?>">
                                    <i class="ph ph-shapes text-sm"></i>
                                    <?= __('settings.mode_vector_icon') ?>
                                </button>
                                <button type="button" id="btnModeImage" onclick="switchBrandMode('image')"
                                    class="px-3.5 py-1.5 rounded-lg text-xs font-semibold transition-all flex items-center gap-1.5 <?= $logoType === 'image' ? 'bg-white dark:bg-slate-900 text-primary-600 dark:text-primary-400 shadow-sm' : 'text-gray-500 dark:text-slate-400 hover:text-gray-800 dark:hover:text-white' ?>">
                                    <i class="ph ph-image text-sm"></i>
                                    <?= __('settings.mode_custom_image') ?>
                                </button>
                            </div>
                        </div>

                        <!-- Hidden state inputs -->
                        <input type="hidden" name="logo_type" id="logo_type" value="<?= htmlspecialchars($logoType) ?>">
                        <input type="hidden" name="logo_icon" id="logo_icon" value="<?= htmlspecialchars($selectedIcon) ?>">
                        <input type="hidden" name="favicon_type" id="favicon_type" value="<?= htmlspecialchars($faviconType) ?>">

                        <!-- SECTION A: Vector Icon Selector & Customization -->
                        <div id="brandIconSection" class="<?= $logoType === 'image' ? 'hidden' : '' ?> space-y-6">

                            <!-- Dual Live Preview Card -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4 rounded-2xl bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 border border-slate-700/80 shadow-inner text-white">
                                <!-- Preview 1: Sidebar Header Preview -->
                                <div class="flex flex-col justify-between p-3.5 rounded-xl bg-slate-950/60 border border-slate-700/50 backdrop-blur-sm">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider flex items-center gap-1">
                                            <i class="ph ph-sidebar-simple text-sm"></i>
                                            <?= __('settings.preview_sidebar_title') ?>
                                        </span>
                                        <span class="text-[10px] text-emerald-400 font-mono bg-emerald-500/10 px-2 py-0.5 rounded-full border border-emerald-500/20">Live</span>
                                    </div>
                                    <div class="h-14 flex items-center px-4 rounded-lg bg-slate-900/90 border border-slate-700/40">
                                        <div id="liveSidebarIcon" class="shrink-0 mr-3 flex items-center justify-center transition-all duration-200">
                                            <!-- SVG dynamically rendered -->
                                        </div>
                                        <span id="liveSidebarTitle" class="font-bold text-white text-base tracking-wider truncate">
                                            <?= htmlspecialchars($settings['site_name'] ?? 'CORE SYSTEM') ?>
                                        </span>
                                    </div>
                                </div>

                                <!-- Preview 2: Browser Tab Favicon Preview -->
                                <div class="flex flex-col justify-between p-3.5 rounded-xl bg-slate-950/60 border border-slate-700/50 backdrop-blur-sm">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider flex items-center gap-1">
                                            <i class="ph ph-browsers text-sm"></i>
                                            <?= __('settings.preview_tab_title') ?>
                                        </span>
                                        <span class="text-[10px] text-emerald-400 font-mono bg-emerald-500/10 px-2 py-0.5 rounded-full border border-emerald-500/20">SVG Favicon</span>
                                    </div>
                                    <div class="h-14 flex items-end">
                                        <div class="w-full max-w-[280px] bg-slate-800/90 border border-slate-700/50 border-b-0 rounded-t-xl px-3 py-2 flex items-center justify-between shadow-sm">
                                            <div class="flex items-center gap-2 overflow-hidden">
                                                <div id="liveFaviconIcon" class="w-5 h-5 shrink-0 flex items-center justify-center rounded transition-all p-0.5" style="background-color: <?= htmlspecialchars($favBg === 'transparent' ? 'transparent' : $favBg) ?>;">
                                                    <!-- SVG dynamically rendered -->
                                                </div>
                                                <span id="liveTabTitle" class="text-xs text-slate-200 truncate font-medium">
                                                    <?= htmlspecialchars($settings['site_name'] ?? 'System') ?>
                                                </span>
                                            </div>
                                            <i class="ph ph-x text-xs text-slate-400 ml-2 hover:text-white shrink-0"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- 20 Icons Selection Grid -->
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300">
                                        <?= __('settings.select_icon_label') ?>
                                    </label>
                                    <span class="text-xs text-gray-500 dark:text-slate-400 font-mono">20 Phosphor Icons</span>
                                </div>
                                <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-5 gap-2.5 max-h-[300px] overflow-y-auto p-1.5 rounded-2xl border border-gray-200 dark:border-slate-800 bg-gray-50/50 dark:bg-slate-900/50" id="iconPickerGrid">
                                    <?php foreach ($allBrandIcons as $k => $iconData): 
                                        $isSelected = ($k === $selectedIcon);
                                    ?>
                                    <button type="button" onclick="selectBrandIcon('<?= htmlspecialchars($k) ?>')"
                                        data-icon-key="<?= htmlspecialchars($k) ?>"
                                        class="brand-icon-btn group relative flex flex-col items-center justify-center p-3 rounded-xl border text-center transition-all <?= $isSelected ? 'border-primary-500 bg-primary-50 dark:bg-primary-950/40 text-primary-600 dark:text-primary-400 ring-2 ring-primary-500/30 font-semibold shadow-sm' : 'border-gray-200 dark:border-slate-800 bg-white dark:bg-slate-800 text-gray-700 dark:text-slate-300 hover:border-primary-300 dark:hover:border-primary-700 hover:bg-gray-50 dark:hover:bg-slate-700/50' ?>">
                                        <span class="brand-icon-check absolute top-1.5 right-1.5 w-4 h-4 rounded-full bg-primary-500 text-white flex items-center justify-center text-[10px] <?= $isSelected ? '' : 'hidden' ?>">
                                            <i class="ph ph-check"></i>
                                        </span>
                                        <div class="w-8 h-8 flex items-center justify-center mb-1.5 icon-glyph shrink-0">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 256" width="28" height="28" class="transition-transform group-hover:scale-110">
                                                <?php foreach ($iconData['paths'] as $p): ?>
                                                    <path fill="currentColor" d="<?= htmlspecialchars($p) ?>"/>
                                                <?php endforeach; ?>
                                            </svg>
                                        </div>
                                        <span class="text-[11px] truncate w-full px-1"><?= __('settings.icon_' . $k) ?></span>
                                    </button>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <!-- Color & Size Controls in 2 columns -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2">
                                <!-- Column 1: Color Picker & Palette -->
                                <div class="space-y-3">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300">
                                        <?= __('settings.logo_icon_color') ?>
                                    </label>
                                    
                                    <div class="flex items-center gap-3">
                                        <div class="relative w-11 h-11 shrink-0 rounded-xl overflow-hidden border border-gray-300 dark:border-slate-700 shadow-sm cursor-pointer">
                                            <input type="color" id="logo_icon_color_picker" value="<?= htmlspecialchars($selectedColor) ?>"
                                                class="absolute -top-3 -left-3 w-16 h-16 cursor-pointer border-0 p-0">
                                        </div>
                                        <div class="relative flex-1">
                                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400 font-mono text-sm">#</span>
                                            <input type="text" id="logo_icon_color" name="logo_icon_color" maxlength="7"
                                                value="<?= htmlspecialchars($selectedColor) ?>"
                                                class="w-full pl-7 pr-3 py-2 bg-white dark:bg-slate-800 rounded-xl border border-gray-300 dark:border-slate-700 text-gray-900 dark:text-white font-mono text-sm uppercase focus:ring-2 focus:ring-primary-500 outline-none"
                                                placeholder="#3B82F6">
                                        </div>
                                    </div>

                                    <!-- Quick Swatches -->
                                    <div class="flex flex-wrap items-center gap-2 pt-1">
                                        <?php foreach ($quickColors as [$hex, $colorName]): ?>
                                        <button type="button" onclick="setBrandColor('<?= $hex ?>')" title="<?= $colorName ?>"
                                            class="w-7 h-7 rounded-lg border border-black/10 dark:border-white/10 hover:scale-110 active:scale-95 transition-all shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
                                            style="background-color: <?= $hex ?>;"></button>
                                        <?php endforeach; ?>
                                    </div>
                                </div>

                                <!-- Column 2: Size Slider & Presets -->
                                <div class="space-y-3">
                                    <div class="flex items-center justify-between">
                                        <label for="logo_icon_size" class="text-sm font-medium text-gray-700 dark:text-slate-300">
                                            <?= __('settings.logo_icon_size') ?>
                                        </label>
                                        <span id="logo_icon_size_badge" class="px-2.5 py-0.5 rounded-full text-xs font-mono font-bold bg-primary-50 dark:bg-primary-950/40 text-primary-600 dark:text-primary-400 border border-primary-200 dark:border-primary-800">
                                            <?= $selectedSize ?>px
                                        </span>
                                    </div>

                                    <input type="range" id="logo_icon_size" name="logo_icon_size" min="20" max="64" step="2"
                                        value="<?= $selectedSize ?>"
                                        class="w-full h-2 bg-gray-200 dark:bg-slate-700 rounded-lg appearance-none cursor-pointer accent-primary-600">

                                    <div class="grid grid-cols-4 gap-1.5 pt-1">
                                        <?php foreach ($sizePresets as $px => $label): ?>
                                        <button type="button" onclick="setBrandSize(<?= $px ?>)"
                                            class="px-2 py-1 text-[11px] font-medium rounded-lg border border-gray-200 dark:border-slate-800 bg-white dark:bg-slate-800 text-gray-600 dark:text-slate-300 hover:border-primary-500 hover:text-primary-600 transition-colors">
                                            <?= $px ?>px
                                        </button>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Favicon Sync & Background Options -->
                            <div class="pt-4 border-t border-gray-100 dark:border-slate-800/80 space-y-3">
                                <label class="flex items-center cursor-pointer">
                                    <input type="checkbox" name="favicon_sync_logo" id="favicon_sync_logo" value="1"
                                        <?= $faviconSync ? 'checked' : '' ?>
                                        class="w-4 h-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                                    <span class="ml-2.5 text-sm font-medium text-gray-700 dark:text-slate-300">
                                        <?= __('settings.sync_favicon_with_logo') ?>
                                    </span>
                                </label>

                                <!-- Favicon Background selector -->
                                <div class="flex items-center gap-4 text-xs text-gray-600 dark:text-slate-400">
                                    <span class="font-medium"><?= __('settings.favicon_bg') ?>:</span>
                                    <label class="inline-flex items-center gap-1.5 cursor-pointer">
                                        <input type="radio" name="favicon_bg_color" value="transparent" <?= $favBg === 'transparent' ? 'checked' : '' ?> onchange="setFaviconBg('transparent')" class="text-primary-600">
                                        <span><?= __('settings.favicon_bg_transparent') ?></span>
                                    </label>
                                    <label class="inline-flex items-center gap-1.5 cursor-pointer">
                                        <input type="radio" name="favicon_bg_color" value="#0f172a" <?= $favBg === '#0f172a' ? 'checked' : '' ?> onchange="setFaviconBg('#0f172a')" class="text-primary-600">
                                        <span><?= __('settings.favicon_bg_dark') ?></span>
                                    </label>
                                    <label class="inline-flex items-center gap-1.5 cursor-pointer">
                                        <input type="radio" name="favicon_bg_color" value="#ffffff" <?= $favBg === '#ffffff' ? 'checked' : '' ?> onchange="setFaviconBg('#ffffff')" class="text-primary-600">
                                        <span><?= __('settings.favicon_bg_light') ?></span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- SECTION B: Custom Image Upload Fallback -->
                        <div id="brandImageSection" class="<?= $logoType === 'icon' ? 'hidden' : '' ?> pt-4 space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-2xl">
                                <div>
                                    <label for="site_logo" class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1"><?= __('settings.site_logo') ?></label>
                                    <p class="text-xs text-gray-500 dark:text-slate-400 mb-2"><?= __('settings.site_logo_hint') ?></p>
                                    <?php if (!empty($settings['site_logo'])): ?>
                                        <div class="mb-3">
                                            <img src="<?= htmlspecialchars($settings['site_logo']) ?>" alt="Logo" class="h-12 object-contain bg-slate-100 dark:bg-slate-800 p-1 rounded-xl border border-gray-200 dark:border-slate-700">
                                        </div>
                                    <?php endif; ?>
                                    <input type="file" id="site_logo" name="site_logo" accept="image/*" class="block w-full text-sm text-gray-500 dark:text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-primary-50 dark:file:bg-slate-800 file:text-primary-700 dark:file:text-primary-400 hover:file:bg-primary-100 dark:hover:file:bg-slate-700">
                                </div>

                                <div>
                                    <label for="site_favicon" class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1"><?= __('settings.site_favicon') ?></label>
                                    <p class="text-xs text-gray-500 dark:text-slate-400 mb-2"><?= __('settings.site_favicon_hint') ?></p>
                                    <?php if (!empty($settings['site_favicon'])): ?>
                                        <div class="mb-3">
                                            <img src="<?= htmlspecialchars($settings['site_favicon']) ?>" alt="Favicon" class="h-8 w-8 object-contain bg-slate-100 dark:bg-slate-800 p-1 rounded-lg border border-gray-200 dark:border-slate-700">
                                        </div>
                                    <?php endif; ?>
                                    <input type="file" id="site_favicon" name="site_favicon" accept="image/*" class="block w-full text-sm text-gray-500 dark:text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-primary-50 dark:file:bg-slate-800 file:text-primary-700 dark:file:text-primary-400 hover:file:bg-primary-100 dark:hover:file:bg-slate-700">
                                </div>
                            </div>
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
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1"><?= __('settings.sidebar_theme') ?></label>
                        <p class="text-xs text-gray-500 dark:text-slate-400 mb-3"><?= __('settings.sidebar_theme_desc') ?></p>

                        <?php
                            $currentTheme = $settings['sidebar_theme'] ?? 'slate';
                            $customColor = $settings['sidebar_custom_color'] ?? Theme::DEFAULT_CUSTOM_COLOR;
                            $customColor = ThemeColor::normalize($customColor) ?? Theme::DEFAULT_CUSTOM_COLOR;
                            $isCustom = $currentTheme === 'custom';
                        ?>

                        <!-- Preset swatches -->
                        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 max-w-3xl">
                            <?php foreach (Theme::PRESETS as $presetKey => $preset): ?>
                                <label class="relative flex flex-col items-center p-3.5 rounded-xl border cursor-pointer transition-all hover:bg-primary-50 dark:hover:bg-primary-900/20 hover:text-primary-700 dark:hover:text-primary-300/40 theme-preset-label <?= $currentTheme === $presetKey ? 'is-active border-primary-600 dark:border-primary-500 bg-primary-50/20 dark:bg-primary-900/10 ring-2 ring-primary-500/10' : 'border-gray-200 dark:border-slate-800' ?>">
                                    <input type="radio" name="sidebar_theme" value="<?= htmlspecialchars($presetKey) ?>" class="sr-only" <?= $currentTheme === $presetKey ? 'checked' : '' ?>>
                                    <div class="w-8 h-8 rounded-md border shadow-inner mb-2.5 flex items-center justify-center theme-swatch" style="background-color: <?= htmlspecialchars($preset['background']) ?>; border-color: <?= htmlspecialchars($preset['border']) ?>;">
                                        <i class="ph ph-check text-white text-xs theme-check-icon <?= $currentTheme === $presetKey ? '' : 'hidden' ?>"></i>
                                    </div>
                                    <span class="text-xs font-semibold text-gray-800 dark:text-slate-200 text-center leading-tight"><?= htmlspecialchars(__('settings.preset_' . $presetKey)) ?></span>
                                </label>
                            <?php endforeach; ?>

                            <!-- Custom color option -->
                            <label class="relative flex flex-col items-center p-3.5 rounded-xl border cursor-pointer transition-all hover:bg-primary-50 dark:hover:bg-primary-900/20 hover:text-primary-700 dark:hover:text-primary-300/40 theme-preset-label <?= $isCustom ? 'is-active border-primary-600 dark:border-primary-500 bg-primary-50/20 dark:bg-primary-900/10 ring-2 ring-primary-500/10' : 'border-gray-200 dark:border-slate-800' ?>">
                                <input type="radio" name="sidebar_theme" value="custom" class="sr-only" <?= $isCustom ? 'checked' : '' ?>>
                                <div class="w-8 h-8 rounded-md border border-gray-300 dark:border-slate-700 shadow-inner mb-2.5 flex items-center justify-center theme-swatch" style="background-color: <?= htmlspecialchars($customColor) ?>;">
                                    <i class="ph ph-check text-white text-xs theme-check-icon <?= $isCustom ? '' : 'hidden' ?>"></i>
                                </div>
                                <span class="text-xs font-semibold text-gray-800 dark:text-slate-200 text-center leading-tight"><?= __('settings.preset_custom') ?></span>
                            </label>
                        </div>

                        <!-- Custom Color Picker -->
                        <div id="sidebarColorPicker" class="mt-5 <?= $isCustom ? '' : 'hidden' ?>">
                            <div class="max-w-3xl rounded-xl border border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-black/20 p-4 transition-colors">
                                <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                                    <!-- Native color input -->
                                    <div class="flex items-center gap-3">
                                        <input type="color" id="sidebarColorInput" name="sidebar_custom_color"
                                               value="<?= htmlspecialchars($customColor) ?>"
                                               class="w-14 h-14 rounded-lg border border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-800 cursor-pointer p-1 shadow-sm transition-transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-primary-500">
                                        <div>
                                            <p class="text-sm font-medium text-gray-700 dark:text-slate-300"><?= __('settings.sidebar_color_picker') ?></p>
                                            <p class="text-xs text-gray-500 dark:text-slate-400"><?= __('settings.sidebar_color_picker_desc') ?></p>
                                        </div>
                                    </div>

                                    <!-- HEX text input -->
                                    <div class="flex items-center gap-2 sm:ml-auto">
                                        <label for="sidebarColorHex" class="text-xs font-medium text-gray-600 dark:text-slate-400">HEX</label>
                                        <input type="text" id="sidebarColorHex" value="<?= htmlspecialchars($customColor) ?>" maxlength="7"
                                               inputmode="text" autocomplete="off" spellcheck="false"
                                               class="w-28 px-3 py-2 bg-white dark:bg-slate-800 rounded-lg border border-gray-300 dark:border-slate-700 text-gray-900 dark:text-white font-mono text-sm uppercase focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition-colors">
                                        <button type="button" id="sidebarColorReset" title="<?= __('settings.sidebar_color_reset') ?>"
                                                class="px-3 py-2 text-gray-500 dark:text-slate-400 hover:text-primary-600 dark:hover:text-primary-400 border border-gray-300 dark:border-slate-700 rounded-lg transition-colors">
                                            <i class="ph ph-arrow-counter-clockwise text-lg"></i>
                                        </button>
                                    </div>
                                </div>

                                <!-- Validation message -->
                                <p id="sidebarColorError" class="hidden mt-2 text-xs text-red-500"></p>

                                <!-- Live preview -->
                                <div class="mt-4">
                                    <p class="text-xs font-medium text-gray-600 dark:text-slate-400 mb-2"><?= __('settings.sidebar_color_preview') ?></p>
                                    <div class="flex items-center gap-3">
                                        <div id="sidebarPreviewSwatch" class="w-12 h-12 rounded-lg border shadow-inner flex items-center justify-center flex-shrink-0" style="background-color: <?= htmlspecialchars($customColor) ?>; border-color: rgba(255,255,255,0.15);">
                                            <i class="ph ph-sidebar-simple text-lg" id="sidebarPreviewIcon" style="color: <?= htmlspecialchars(ThemeColor::contrastColor($customColor)) ?>;"></i>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-sm font-medium text-gray-800 dark:text-slate-200 truncate"><?= htmlspecialchars(Settings::get('site_name', 'CORE SYSTEM')) ?></p>
                                            <p class="text-xs text-gray-500 dark:text-slate-400 truncate" id="sidebarPreviewHex"><?= htmlspecialchars(strtoupper($customColor)) ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
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
                    // ── Sidebar Theme: Preset + Custom Color Picker ──
                    const themeRadios      = document.querySelectorAll('input[name="sidebar_theme"]');
                    const colorPickerWrap  = document.getElementById('sidebarColorPicker');
                    const colorInput       = document.getElementById('sidebarColorInput');
                    const colorHexInput    = document.getElementById('sidebarColorHex');
                    const colorResetBtn    = document.getElementById('sidebarColorReset');
                    const colorError       = document.getElementById('sidebarColorError');
                    const previewSwatch    = document.getElementById('sidebarPreviewSwatch');
                    const previewIcon      = document.getElementById('sidebarPreviewIcon');
                    const previewHex       = document.getElementById('sidebarPreviewHex');
                    const defaultColor     = '<?= htmlspecialchars(Theme::DEFAULT_CUSTOM_COLOR) ?>';

                    const ACTIVE   = ['border-primary-600', 'dark:border-primary-500', 'bg-primary-50/20', 'dark:bg-primary-900/10', 'ring-2', 'ring-primary-500/10'];
                    const INACTIVE = ['border-gray-200', 'dark:border-slate-800'];

                    /** ตรวจสอบความถูกต้องของค่า HEX */
                    const isValidHex = (value) => /^#?([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/.test(String(value).trim());

                    /** แปลง HEX ให้อยู่ในรูปแบบ #rrggbb */
                    const normalizeHex = (value) => {
                        let hex = String(value).trim().replace(/^#/, '');
                        if (!isValidHex(hex)) return null;
                        if (hex.length === 3) hex = hex[0] + hex[0] + hex[1] + hex[1] + hex[2] + hex[2];
                        return '#' + hex.toLowerCase();
                    };

                    /** เลือกสีตัวอักษรที่อ่านได้ชัดบนพื้นหลัง */
                    const contrastColor = (hex) => {
                        const n = parseInt(hex.slice(1), 16);
                        const r = (n >> 16) & 255, g = (n >> 8) & 255, b = n & 255;
                        const l = (0.299 * r + 0.587 * g + 0.114 * b) / 255;
                        return l > 0.58 ? '#0f172a' : '#ffffff';
                    };

                    /** อัปเดตตัวอย่างสีแบบเรียลไทม์ */
                    const updatePreview = (hex) => {
                        if (!hex) return;
                        previewSwatch.style.backgroundColor = hex;
                        previewIcon.style.color = contrastColor(hex);
                        previewHex.textContent = hex.toUpperCase();
                    };

                    /** แสดง/ซ่อนข้อความแจ้งเตือน */
                    const showError = (message) => {
                        if (!message) {
                            colorError.classList.add('hidden');
                            colorError.textContent = '';
                            return;
                        }
                        colorError.textContent = message;
                        colorError.classList.remove('hidden');
                    };

                    /** เปิด/ปิด Color Picker ตามโหมดที่เลือก */
                    const togglePicker = (isCustom) => {
                        if (colorPickerWrap) {
                            colorPickerWrap.classList.toggle('hidden', !isCustom);
                        }
                    };

                    /** ซิงค์ค่าสีระหว่าง Color Input กับช่อง HEX */
                    const syncColor = (hex, source) => {
                        if (!hex) return;
                        colorInput.value = hex;
                        if (source !== 'hex') colorHexInput.value = hex.toUpperCase();
                        updatePreview(hex);
                        showError('');
                    };

                    themeRadios.forEach(radio => {
                        radio.addEventListener('change', function() {
                            // รีเซ็ต label ทั้งหมด
                            themeRadios.forEach(r => {
                                const label = r.closest('label');
                                if (!label) return;
                                label.classList.remove(...ACTIVE);
                                label.classList.add(...INACTIVE);
                                const icon = label.querySelector('.theme-check-icon');
                                if (icon) icon.classList.add('hidden');
                            });

                            // ตั้งค่า label ที่เลือก
                            const activeLabel = this.closest('label');
                            if (activeLabel) {
                                activeLabel.classList.remove(...INACTIVE);
                                activeLabel.classList.add(...ACTIVE);
                                const activeIcon = activeLabel.querySelector('.theme-check-icon');
                                if (activeIcon) activeIcon.classList.remove('hidden');
                            }

                            togglePicker(this.value === 'custom');
                        });
                    });

                    // Native color picker
                    if (colorInput) {
                        colorInput.addEventListener('input', function() {
                            syncColor(this.value, 'picker');
                        });
                    }

                    // ช่องกรอก HEX
                    if (colorHexInput) {
                        colorHexInput.addEventListener('input', function() {
                            const raw = this.value.trim();
                            if (raw === '') {
                                showError('');
                                return;
                            }
                            const normalized = normalizeHex(raw);
                            if (!normalized) {
                                showError('<?= addslashes(__('settings.sidebar_color_invalid')) ?>');
                                return;
                            }
                            syncColor(normalized, 'hex');
                        });

                        colorHexInput.addEventListener('blur', function() {
                            const normalized = normalizeHex(this.value);
                            if (normalized) {
                                this.value = normalized.toUpperCase();
                                showError('');
                            }
                        });
                    }

                    // ปุ่มรีเซ็ตกลับเป็นสีเริ่มต้น
                    if (colorResetBtn) {
                        colorResetBtn.addEventListener('click', function() {
                            syncColor(defaultColor, 'reset');
                        });
                    }

                    // เริ่มต้น: ถ้าเป็นโหมด custom ให้แสดง picker
                    const customRadio = document.querySelector('input[name="sidebar_theme"][value="custom"]');
                    togglePicker(!!(customRadio && customRadio.checked));

                    // ── Brand Icon & Logo/Favicon Live Customizer ──
                    const brandIcons = <?= json_encode(\App\Core\BrandIcon::all()) ?>;
                    const logoIconInput = document.getElementById('logo_icon');
                    const logoColorInput = document.getElementById('logo_icon_color');
                    const logoColorPicker = document.getElementById('logo_icon_color_picker');
                    const logoSizeInput = document.getElementById('logo_icon_size');
                    const logoSizeBadge = document.getElementById('logo_icon_size_badge');
                    const liveSidebarIcon = document.getElementById('liveSidebarIcon');
                    const liveFaviconIcon = document.getElementById('liveFaviconIcon');
                    const liveSidebarTitle = document.getElementById('liveSidebarTitle');
                    const liveTabTitle = document.getElementById('liveTabTitle');
                    const siteNameInput = document.getElementById('site_name');

                    window.switchBrandMode = function(mode) {
                        const isIcon = mode === 'icon';
                        document.getElementById('logo_type').value = mode;
                        document.getElementById('favicon_type').value = mode;
                        document.getElementById('brandIconSection').classList.toggle('hidden', !isIcon);
                        document.getElementById('brandImageSection').classList.toggle('hidden', isIcon);

                        const btnIcon = document.getElementById('btnModeIcon');
                        const btnImg = document.getElementById('btnModeImage');
                        if (isIcon) {
                            btnIcon.className = "px-3.5 py-1.5 rounded-lg text-xs font-semibold transition-all flex items-center gap-1.5 bg-white dark:bg-slate-900 text-primary-600 dark:text-primary-400 shadow-sm";
                            btnImg.className = "px-3.5 py-1.5 rounded-lg text-xs font-semibold transition-all flex items-center gap-1.5 text-gray-500 dark:text-slate-400 hover:text-gray-800 dark:hover:text-white";
                        } else {
                            btnImg.className = "px-3.5 py-1.5 rounded-lg text-xs font-semibold transition-all flex items-center gap-1.5 bg-white dark:bg-slate-900 text-primary-600 dark:text-primary-400 shadow-sm";
                            btnIcon.className = "px-3.5 py-1.5 rounded-lg text-xs font-semibold transition-all flex items-center gap-1.5 text-gray-500 dark:text-slate-400 hover:text-gray-800 dark:hover:text-white";
                        }
                    };

                    window.selectBrandIcon = function(key) {
                        if (!brandIcons[key]) return;
                        if (logoIconInput) logoIconInput.value = key;
                        document.querySelectorAll('.brand-icon-btn').forEach(btn => {
                            const isMatch = btn.getAttribute('data-icon-key') === key;
                            btn.className = "brand-icon-btn group relative flex flex-col items-center justify-center p-3 rounded-xl border text-center transition-all " +
                                (isMatch 
                                    ? "border-primary-500 bg-primary-50 dark:bg-primary-950/40 text-primary-600 dark:text-primary-400 ring-2 ring-primary-500/30 font-semibold shadow-sm"
                                    : "border-gray-200 dark:border-slate-800 bg-white dark:bg-slate-800 text-gray-700 dark:text-slate-300 hover:border-primary-300 dark:hover:border-primary-700 hover:bg-gray-50 dark:hover:bg-slate-700/50");
                            const check = btn.querySelector('.brand-icon-check');
                            if (check) check.classList.toggle('hidden', !isMatch);
                        });
                        updateBrandLivePreviews();
                    };

                    window.setBrandColor = function(hex) {
                        if (!hex) return;
                        hex = hex.toUpperCase();
                        if (logoColorPicker) logoColorPicker.value = hex;
                        if (logoColorInput) logoColorInput.value = hex;
                        updateBrandLivePreviews();
                    };

                    window.setBrandSize = function(px) {
                        if (logoSizeInput) logoSizeInput.value = px;
                        if (logoSizeBadge) logoSizeBadge.textContent = px + 'px';
                        updateBrandLivePreviews();
                    };

                    window.setFaviconBg = function(bg) {
                        if (liveFaviconIcon) {
                            liveFaviconIcon.style.backgroundColor = (bg === 'transparent' ? 'transparent' : bg);
                        }
                    };

                    function renderSvgMarkup(iconKey, color, size) {
                        const icon = brandIcons[iconKey] || brandIcons['cube'];
                        let paths = '';
                        if (icon && icon.paths) {
                            icon.paths.forEach(p => {
                                paths += `<path fill="${color}" d="${p}"/>`;
                            });
                        }
                        return `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 256" width="${size}" height="${size}" style="display:inline-block;vertical-align:middle;">${paths}</svg>`;
                    }

                    function updateBrandLivePreviews() {
                        const key = logoIconInput ? logoIconInput.value : 'cube';
                        let color = logoColorInput ? logoColorInput.value.trim() : '#3b82f6';
                        if (!/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/.test(color)) {
                            color = '#3b82f6';
                        }
                        const size = logoSizeInput ? parseInt(logoSizeInput.value, 10) || 32 : 32;

                        if (liveSidebarIcon) {
                            liveSidebarIcon.innerHTML = renderSvgMarkup(key, color, size);
                        }
                        if (liveFaviconIcon) {
                            liveFaviconIcon.innerHTML = renderSvgMarkup(key, color, 16);
                        }
                        if (siteNameInput) {
                            const title = siteNameInput.value.trim() || 'CORE SYSTEM';
                            if (liveSidebarTitle) liveSidebarTitle.textContent = title;
                            if (liveTabTitle) liveTabTitle.textContent = title;
                        }
                    }

                    if (logoColorPicker) {
                        logoColorPicker.addEventListener('input', function() {
                            setBrandColor(this.value);
                        });
                    }
                    if (logoColorInput) {
                        logoColorInput.addEventListener('input', function() {
                            let val = this.value.trim();
                            if (!val.startsWith('#') && val.length > 0) val = '#' + val;
                            if (/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/.test(val)) {
                                if (logoColorPicker) logoColorPicker.value = val;
                                updateBrandLivePreviews();
                            }
                        });
                    }
                    if (logoSizeInput) {
                        logoSizeInput.addEventListener('input', function() {
                            if (logoSizeBadge) logoSizeBadge.textContent = this.value + 'px';
                            updateBrandLivePreviews();
                        });
                    }
                    if (siteNameInput) {
                        siteNameInput.addEventListener('input', updateBrandLivePreviews);
                    }

                    // Initial render of preview
                    updateBrandLivePreviews();
                });
            </script>
            <?php elseif ($tab === 'login'): ?>
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
            
            <!-- Sticky Header for the entire tab -->
            <div class="sticky top-[-24px] md:top-[-32px] z-20 flex items-center justify-between mb-6 border-b border-gray-100 dark:border-slate-800 pb-4 pt-6 md:pt-8 -mt-6 md:-mt-8 -mx-6 md:-mx-8 px-6 md:px-8 bg-white/95 dark:bg-slate-900/95 backdrop-blur-md rounded-t-2xl">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-0">
                    <i class="ph ph-gear mr-2"></i>การตั้งค่าปุ่มสลับภาษา
                </h3>
                <button type="submit" form="translationsSettingsForm" class="px-5 py-2 text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 rounded-lg transition-colors shadow-sm flex items-center">
                    <i class="ph ph-check-circle mr-2 text-lg"></i> <?= __('settings.save_settings') ?>
                </button>
            </div>

            <form action="/settings/translations" method="POST" id="translationsSettingsForm" class="mb-10 pb-8 border-b border-gray-100 dark:border-slate-800">
                <input type="hidden" name="csrf_token" value="<?= \App\Core\Session::getCsrfToken() ?>">

                <div class="space-y-6">
                    <!-- Enable Switcher Toggle -->
                    <div class="flex items-center justify-between max-w-lg">
                        <div>
                            <h4 class="text-sm font-medium text-gray-800 dark:text-white">แสดงปุ่มสลับภาษา</h4>
                            <p class="text-xs text-gray-500 dark:text-slate-400">เปิด/ปิด ปุ่มสลับภาษาที่แถบเมนูด้านบน</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="hidden" name="enable_language_switcher" value="0">
                            <input type="checkbox" name="enable_language_switcher" value="1" class="sr-only peer" <?= (!isset($settings['enable_language_switcher']) || $settings['enable_language_switcher'] == '1') ? 'checked' : '' ?>>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-slate-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary-600"></div>
                        </label>
                    </div>

                    <!-- Available Languages -->
                    <?php 
                        $availLangs = isset($settings['available_languages']) && $settings['available_languages'] !== '' ? explode(',', $settings['available_languages']) : ['th', 'en']; 
                    ?>
                    <div class="pt-4 border-t border-gray-100 dark:border-slate-800">
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">ภาษาที่เปิดให้ใช้งาน</label>
                        <div class="flex gap-4">
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="available_languages[]" value="th" class="rounded border-gray-300 text-primary-600 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50" <?= in_array('th', $availLangs) ? 'checked' : '' ?>>
                                <span class="ml-2 text-sm text-gray-700 dark:text-slate-300">ภาษาไทย (TH)</span>
                            </label>
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="available_languages[]" value="en" class="rounded border-gray-300 text-primary-600 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50" <?= in_array('en', $availLangs) ? 'checked' : '' ?>>
                                <span class="ml-2 text-sm text-gray-700 dark:text-slate-300">English (EN)</span>
                            </label>
                        </div>
                    </div>
                </div>
            </form>

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
                <div class="bg-white dark:bg-black/20 dark:backdrop-blur-md rounded-2xl shadow-xl w-full max-w-lg overflow-hidden flex flex-col max-h-[85vh] md:max-h-[90vh] border border-gray-100 dark:border-slate-800 transition-colors">
                    <form id="editTranslationForm" onsubmit="saveTranslation(event)" class="flex flex-col h-full max-h-[85vh] md:max-h-[90vh] overflow-hidden">
                        <input type="hidden" name="csrf_token" value="<?= \App\Core\Session::getCsrfToken() ?>">
                        <input type="hidden" id="edit_namespace" name="namespace">
                        <input type="hidden" id="edit_key" name="key_name">

                        <!-- Fixed Modal Header -->
                        <div class="flex justify-between items-center px-6 py-4 border-b border-gray-100 dark:border-slate-800 flex-shrink-0 bg-white dark:bg-slate-900/80">
                            <h3 class="text-lg font-bold text-gray-800 dark:text-white flex items-center">
                                <i class="ph ph-translate text-primary-500 dark:text-primary-400 mr-2 text-xl"></i> <?= __('settings.trans_edit') ?>
                            </h3>
                            <button type="button" onclick="closeTranslationModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-slate-200 transition-colors p-1">
                                <i class="ph ph-x text-xl"></i>
                            </button>
                        </div>

                        <!-- Scrollable Modal Body -->
                        <div class="p-6 overflow-y-auto flex-1 bg-slate-50 dark:bg-slate-950/50 space-y-4">
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

                        <!-- Fixed Modal Footer -->
                        <div class="px-6 py-4 border-t border-gray-100 dark:border-slate-800 bg-white dark:bg-slate-900/80 flex justify-end gap-3 flex-shrink-0">
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
                            text: '<?= __('common.unexpected_error') ?>'
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
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white flex items-center">
                        <i class="ph ph-share-network mr-2 text-primary-600 dark:text-primary-400"></i><?= __('settings.social_login') ?>
                    </h3>
                    <button type="submit" id="saveSocialBtn" class="px-5 py-2 text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 rounded-lg transition-colors shadow-sm flex items-center">
                        <i class="ph ph-check-circle mr-2 text-lg"></i>
                        <span><?= __('settings.save_settings') ?></span>
                    </button>
                </div>
                <input type="hidden" name="csrf_token" value="<?= \App\Core\Session::getCsrfToken() ?>">

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 md:gap-8">
                    <!-- 1. Google Settings -->
                    <div class="bg-white dark:bg-slate-800/80 p-5 md:p-6 rounded-2xl border border-gray-200 dark:border-slate-700 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
                        <div class="absolute top-0 left-0 w-1.5 h-full bg-[#EA4335] opacity-0 group-hover:opacity-100 transition-opacity"></div>
                        
                        <div class="flex items-center justify-between mb-6 border-b border-gray-100 dark:border-slate-700/50 pb-4">
                            <h4 class="text-base font-semibold text-gray-800 dark:text-white flex items-center">
                                <div class="w-8 h-8 rounded-lg bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-700 flex items-center justify-center mr-3 shadow-xs shrink-0">
                                    <svg class="w-5 h-5" viewBox="0 0 24 24">
                                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                                    </svg>
                                </div>
                                <?= __('settings.google_login') ?>
                            </h4>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="enable_google_login" value="1" class="sr-only peer" <?= ($settings['enable_google_login'] ?? '0') === '1' ? 'checked' : '' ?>>
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary-300 dark:peer-focus:ring-primary-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-primary-600"></div>
                                <span class="ml-3 text-sm font-medium text-gray-700 dark:text-slate-300"><?= __('settings.enable_google_login') ?></span>
                            </label>
                        </div>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1"><?= __('settings.google_client_id') ?></label>
                                <input type="text" name="google_client_id" value="<?= htmlspecialchars($settings['google_client_id'] ?? '') ?>" placeholder="e.g. 123456789-abcdef.apps.googleusercontent.com" class="w-full px-4 py-2 bg-white dark:bg-slate-900 border border-gray-300 dark:border-slate-700 rounded-lg text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition-colors">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1"><?= __('settings.google_client_secret') ?></label>
                                <input type="text" name="google_client_secret" value="<?= htmlspecialchars($settings['google_client_secret'] ?? '') ?>" placeholder="e.g. GOCSPX-xxxxxxxxxxxxxxxx" class="w-full px-4 py-2 bg-white dark:bg-slate-900 border border-gray-300 dark:border-slate-700 rounded-lg text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition-colors">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1"><?= __('settings.google_redirect_uri') ?></label>
                                <div class="flex items-center gap-2">
                                    <input type="text" id="google_redirect_uri_input" name="google_redirect_uri" value="<?= htmlspecialchars($settings['google_redirect_uri'] ?? \App\Core\Config::get('APP_URL') . '/auth/google/callback') ?>" class="w-full px-4 py-2 bg-gray-50 dark:bg-slate-900/50 border border-gray-200 dark:border-slate-700 rounded-lg text-sm text-gray-500 dark:text-slate-400 focus:outline-none" readonly>
                                    <button type="button" onclick="copyToClipboard('google_redirect_uri_input', this)" class="shrink-0 px-3 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-gray-700 dark:text-slate-300 rounded-lg transition-colors border border-gray-200 dark:border-slate-600" title="<?= __('settings.copied_to_clipboard') ?>">
                                        <i class="ph ph-copy text-lg"></i>
                                    </button>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-slate-400 mt-2 flex items-center gap-1.5">
                                    <i class="ph ph-info text-sm text-primary-500"></i>
                                    <?= __('settings.google_redirect_hint') ?>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- 2. LINE Settings -->
                    <div class="bg-white dark:bg-slate-800/80 p-5 md:p-6 rounded-2xl border border-gray-200 dark:border-slate-700 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
                        <div class="absolute top-0 left-0 w-1.5 h-full bg-[#06C755] opacity-0 group-hover:opacity-100 transition-opacity"></div>
                        
                        <div class="flex items-center justify-between mb-6 border-b border-gray-100 dark:border-slate-700/50 pb-4">
                            <h4 class="text-base font-semibold text-gray-800 dark:text-white flex items-center">
                                <div class="w-8 h-8 rounded-lg bg-[#06C755] flex items-center justify-center mr-3 shadow-xs shrink-0">
                                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none">
                                        <path fill="#FFFFFF" d="M19.365 9.863c.349 0 .63.285.63.631 0 .345-.281.63-.63.63H17.61v1.125h1.755c.349 0 .63.283.63.63 0 .344-.281.629-.63.629h-2.386c-.345 0-.627-.285-.627-.629V8.108c0-.345.282-.63.63-.63h2.386c.346 0 .627.285.627.63 0 .349-.281.63-.63.63H17.61v1.125h1.755zm-3.855 3.016c0 .27-.174.51-.432.596-.064.021-.133.031-.199.031-.211 0-.391-.09-.51-.25l-2.443-3.317v2.94c0 .344-.279.629-.631.629-.346 0-.626-.285-.626-.629V8.108c0-.27.173-.51.43-.595.06-.023.136-.033.194-.033.195 0 .375.104.495.254l2.462 3.33V8.108c0-.345.282-.63.63-.63.345 0 .63.285.63.63v4.771zm-5.741 0c0 .344-.282.629-.631.629-.345 0-.627-.285-.627-.629V8.108c0-.345.282-.63.627-.63.349 0 .631.285.631.63v4.771zm-2.466.629H4.917c-.345 0-.63-.285-.63-.629V8.108c0-.345.285-.63.63-.63.348 0 .63.285.63.63v4.141h1.756c.348 0 .629.283.629.63 0 .344-.281.629-.629.629M24 10.314C24 4.943 18.615.572 12 .572S0 4.943 0 10.314c0 4.811 4.27 8.842 10.035 9.608.391.082.923.258 1.058.59.12.301.079.77.038 1.08-.058.42-.27 1.62-.27 1.62s-.135.81.48.442c.615-.368 6.502-3.834 8.88-6.559C22.425 14.737 24 12.637 24 10.314"/>
                                    </svg>
                                </div>
                                <?= __('settings.line_login') ?>
                            </h4>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="enable_line_login" value="1" class="sr-only peer" <?= ($settings['enable_line_login'] ?? '0') === '1' ? 'checked' : '' ?>>
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary-300 dark:peer-focus:ring-primary-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-primary-600"></div>
                                <span class="ml-3 text-sm font-medium text-gray-700 dark:text-slate-300"><?= __('settings.enable_line_login') ?></span>
                            </label>
                        </div>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1"><?= __('settings.line_client_id') ?></label>
                                <input type="text" name="line_client_id" value="<?= htmlspecialchars($settings['line_client_id'] ?? '') ?>" placeholder="e.g. 165xxxxxxxx" class="w-full px-4 py-2 bg-white dark:bg-slate-900 border border-gray-300 dark:border-slate-700 rounded-lg text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition-colors">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1"><?= __('settings.line_client_secret') ?></label>
                                <input type="text" name="line_client_secret" value="<?= htmlspecialchars($settings['line_client_secret'] ?? '') ?>" placeholder="e.g. 84xxxxxxxxxxxxxxxxxxxxxxxxxxxxxx" class="w-full px-4 py-2 bg-white dark:bg-slate-900 border border-gray-300 dark:border-slate-700 rounded-lg text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition-colors">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1"><?= __('settings.line_redirect_uri') ?></label>
                                <div class="flex items-center gap-2">
                                    <input type="text" id="line_redirect_uri_input" name="line_redirect_uri" value="<?= htmlspecialchars($settings['line_redirect_uri'] ?? \App\Core\Config::get('APP_URL') . '/auth/line/callback') ?>" class="w-full px-4 py-2 bg-gray-50 dark:bg-slate-900/50 border border-gray-200 dark:border-slate-700 rounded-lg text-sm text-gray-500 dark:text-slate-400 focus:outline-none" readonly>
                                    <button type="button" onclick="copyToClipboard('line_redirect_uri_input', this)" class="shrink-0 px-3 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-gray-700 dark:text-slate-300 rounded-lg transition-colors border border-gray-200 dark:border-slate-600" title="<?= __('settings.copied_to_clipboard') ?>">
                                        <i class="ph ph-copy text-lg"></i>
                                    </button>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-slate-400 mt-2 flex items-center gap-1.5">
                                    <i class="ph ph-info text-sm text-primary-500"></i>
                                    <?= __('settings.line_redirect_hint') ?>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- 3. Facebook Settings -->
                    <div class="bg-white dark:bg-slate-800/80 p-5 md:p-6 rounded-2xl border border-gray-200 dark:border-slate-700 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
                        <div class="absolute top-0 left-0 w-1.5 h-full bg-[#1877F2] opacity-0 group-hover:opacity-100 transition-opacity"></div>
                        
                        <div class="flex items-center justify-between mb-6 border-b border-gray-100 dark:border-slate-700/50 pb-4">
                            <h4 class="text-base font-semibold text-gray-800 dark:text-white flex items-center">
                                <div class="w-8 h-8 rounded-lg bg-[#1877F2] flex items-center justify-center mr-3 shadow-xs shrink-0">
                                    <svg class="w-5 h-5 fill-white" viewBox="0 0 24 24">
                                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                    </svg>
                                </div>
                                <?= __('settings.facebook_login') ?>
                            </h4>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="enable_facebook_login" value="1" class="sr-only peer" <?= ($settings['enable_facebook_login'] ?? '0') === '1' ? 'checked' : '' ?>>
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary-300 dark:peer-focus:ring-primary-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-primary-600"></div>
                                <span class="ml-3 text-sm font-medium text-gray-700 dark:text-slate-300"><?= __('settings.enable_facebook_login') ?></span>
                            </label>
                        </div>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1"><?= __('settings.facebook_client_id') ?></label>
                                <input type="text" name="facebook_client_id" value="<?= htmlspecialchars($settings['facebook_client_id'] ?? '') ?>" placeholder="e.g. 10xxxxxxxxxxxxxx" class="w-full px-4 py-2 bg-white dark:bg-slate-900 border border-gray-300 dark:border-slate-700 rounded-lg text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition-colors">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1"><?= __('settings.facebook_client_secret') ?></label>
                                <input type="text" name="facebook_client_secret" value="<?= htmlspecialchars($settings['facebook_client_secret'] ?? '') ?>" placeholder="e.g. a1b2c3d4e5f6xxxxxxxxxxxxxxxx" class="w-full px-4 py-2 bg-white dark:bg-slate-900 border border-gray-300 dark:border-slate-700 rounded-lg text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition-colors">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1"><?= __('settings.facebook_redirect_uri') ?></label>
                                <div class="flex items-center gap-2">
                                    <input type="text" id="facebook_redirect_uri_input" name="facebook_redirect_uri" value="<?= htmlspecialchars($settings['facebook_redirect_uri'] ?? \App\Core\Config::get('APP_URL') . '/auth/facebook/callback') ?>" class="w-full px-4 py-2 bg-gray-50 dark:bg-slate-900/50 border border-gray-200 dark:border-slate-700 rounded-lg text-sm text-gray-500 dark:text-slate-400 focus:outline-none" readonly>
                                    <button type="button" onclick="copyToClipboard('facebook_redirect_uri_input', this)" class="shrink-0 px-3 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-gray-700 dark:text-slate-300 rounded-lg transition-colors border border-gray-200 dark:border-slate-600" title="<?= __('settings.copied_to_clipboard') ?>">
                                        <i class="ph ph-copy text-lg"></i>
                                    </button>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-slate-400 mt-2 flex items-center gap-1.5">
                                    <i class="ph ph-info text-sm text-primary-500"></i>
                                    <?= __('settings.facebook_redirect_hint') ?>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- 4. GitHub Settings -->
                    <div class="bg-white dark:bg-slate-800/80 p-5 md:p-6 rounded-2xl border border-gray-200 dark:border-slate-700 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
                        <div class="absolute top-0 left-0 w-1.5 h-full bg-[#24292F] dark:bg-white opacity-0 group-hover:opacity-100 transition-opacity"></div>
                        
                        <div class="flex items-center justify-between mb-6 border-b border-gray-100 dark:border-slate-700/50 pb-4">
                            <h4 class="text-base font-semibold text-gray-800 dark:text-white flex items-center">
                                <div class="w-8 h-8 rounded-lg bg-[#24292F] dark:bg-white flex items-center justify-center mr-3 shadow-xs shrink-0">
                                    <svg class="w-5 h-5 fill-white dark:fill-[#24292F]" viewBox="0 0 24 24">
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.53 1.032 1.53 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z"/>
                                    </svg>
                                </div>
                                <?= __('settings.github_login') ?>
                            </h4>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="enable_github_login" value="1" class="sr-only peer" <?= ($settings['enable_github_login'] ?? '0') === '1' ? 'checked' : '' ?>>
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary-300 dark:peer-focus:ring-primary-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-primary-600"></div>
                                <span class="ml-3 text-sm font-medium text-gray-700 dark:text-slate-300"><?= __('settings.enable_github_login') ?></span>
                            </label>
                        </div>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1"><?= __('settings.github_client_id') ?></label>
                                <input type="text" name="github_client_id" value="<?= htmlspecialchars($settings['github_client_id'] ?? '') ?>" placeholder="e.g. Ov23lixxxxxxxxxx" class="w-full px-4 py-2 bg-white dark:bg-slate-900 border border-gray-300 dark:border-slate-700 rounded-lg text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition-colors">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1"><?= __('settings.github_client_secret') ?></label>
                                <input type="text" name="github_client_secret" value="<?= htmlspecialchars($settings['github_client_secret'] ?? '') ?>" placeholder="e.g. 5bxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx" class="w-full px-4 py-2 bg-white dark:bg-slate-900 border border-gray-300 dark:border-slate-700 rounded-lg text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition-colors">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1"><?= __('settings.github_redirect_uri') ?></label>
                                <div class="flex items-center gap-2">
                                    <input type="text" id="github_redirect_uri_input" name="github_redirect_uri" value="<?= htmlspecialchars($settings['github_redirect_uri'] ?? \App\Core\Config::get('APP_URL') . '/auth/github/callback') ?>" class="w-full px-4 py-2 bg-gray-50 dark:bg-slate-900/50 border border-gray-200 dark:border-slate-700 rounded-lg text-sm text-gray-500 dark:text-slate-400 focus:outline-none" readonly>
                                    <button type="button" onclick="copyToClipboard('github_redirect_uri_input', this)" class="shrink-0 px-3 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-gray-700 dark:text-slate-300 rounded-lg transition-colors border border-gray-200 dark:border-slate-600" title="<?= __('settings.copied_to_clipboard') ?>">
                                        <i class="ph ph-copy text-lg"></i>
                                    </button>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-slate-400 mt-2 flex items-center gap-1.5">
                                    <i class="ph ph-info text-sm text-primary-500"></i>
                                    <?= __('settings.github_redirect_hint') ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <?php elseif ($tab === 'design_spec'): ?>
                <?php require __DIR__ . '/design_spec.php'; ?>
            <?php endif; ?>

        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        // Toggle active visual state for setting cards
        // หมายเหตุ: sidebar_theme ถูกจัดการโดย script เฉพาะของ Color Picker อยู่แล้ว
        $('input[type="radio"]').not('input[name="sidebar_theme"]').on('change', function() {
            var groupName = $(this).attr('name');
            var activeClasses = 'border-primary-600 dark:border-primary-500 bg-primary-50/20 dark:bg-primary-900/10 ring-2 ring-primary-500/10';
            var inactiveClasses = 'border-gray-200 dark:border-slate-800';

            // Remove active classes from all labels in this group
            $('input[name="' + groupName + '"]').closest('label').removeClass(activeClasses).addClass(inactiveClasses);
            
            // Add active classes to the checked one
            $(this).closest('label').removeClass(inactiveClasses).addClass(activeClasses);

            // Special case for instant font size preview
            if(groupName === 'base_font_size') {
                $('html').removeClass('text-[14px] text-[16px] text-[18px]').addClass($(this).val());
            }
        });

        // Button preloading & double-submit prevention (Rule 4)
        $('form[id$="SettingsForm"], #generalSettingsForm').on('submit', function() {
            var $btn = $(this).find('button[type="submit"]');
            $btn.prop('disabled', true).addClass('opacity-75 cursor-not-allowed');
            $btn.html('<i class="ph ph-circle-notch animate-spin mr-2 text-lg"></i> <span><?= addslashes(__('common.saving')) ?></span>');
        });
    });

    // Global copy to clipboard helper with SweetAlert2 top-center Toast
    function copyToClipboard(inputId, btnElement) {
        var copyText = document.getElementById(inputId);
        if (!copyText) return;
        
        copyText.select();
        copyText.setSelectionRange(0, 99999);
        
        function showFeedback() {
            var $icon = $(btnElement).find('i');
            $icon.removeClass('ph-copy').addClass('ph-check text-green-500 dark:text-green-400');
            setTimeout(function() {
                $icon.removeClass('ph-check text-green-500 dark:text-green-400').addClass('ph-copy');
            }, 2000);

            if (typeof Swal !== 'undefined') {
                Swal.mixin({
                    toast: true,
                    position: 'top',
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true,
                    customClass: {
                        popup: 'dark:bg-slate-800 dark:text-white border border-gray-100 dark:border-slate-700 shadow-xl',
                        title: 'text-[13px] font-medium font-sans'
                    }
                }).fire({
                    icon: 'success',
                    title: '<?= addslashes(__('settings.copied_to_clipboard')) ?>'
                });
            }
        }

        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(copyText.value).then(showFeedback).catch(function() {
                document.execCommand('copy');
                showFeedback();
            });
        } else {
            document.execCommand('copy');
            showFeedback();
        }
    }
</script>
