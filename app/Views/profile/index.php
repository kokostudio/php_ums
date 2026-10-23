<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white"><?= __('profile.title') ?></h1>
        <p class="text-sm text-gray-500 dark:text-slate-400 mt-1"><?= __('profile.subtitle') ?></p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Left Column: User Card -->
    <div class="lg:col-span-1">
        <div class="bg-white dark:bg-black/20 dark:backdrop-blur-md rounded-2xl shadow-sm border border-gray-100 dark:border-slate-800 overflow-hidden relative transition-colors">
            <!-- Cover Banner -->
            <div class="h-28 bg-gradient-to-r from-primary-600 to-indigo-600 dark:from-primary-900/80 dark:to-indigo-900/80 relative">
                <div class="absolute inset-0 opacity-10 bg-[url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI4IiBoZWlnaHQ9IjgiPgo8cmVjdCB3aWR0aD0iOCIgaGVpZ2h0PSI4IiBmaWxsPSIjZmZmIiBmaWxsLW9wYWNpdHk9IjAuMSI+PC9yZWN0Pgo8cGF0aCBkPSJNMCAwTDggOFpNOCAwTDAgOFoiIHN0cm9rZT0iIzAwMCIgc3Ryb2tlLW9wYWNpdHk9IjAuMSI+PC9wYXRoPgo8L3N2Zz4=')]"></div>
            </div>

            <div class="px-6 pb-6 text-center relative -mt-12">
                <?php if (!empty($user['profile_image'])): ?>
                    <img src="<?= htmlspecialchars($user['profile_image']) ?>" alt="Profile Picture" class="w-24 h-24 mx-auto rounded-full object-cover shadow-lg border-4 border-white dark:border-slate-900 relative z-10 bg-white dark:bg-slate-900">
                <?php else: ?>
                    <div class="w-24 h-24 mx-auto rounded-full bg-gradient-to-tr from-primary-500 to-purple-500 flex items-center justify-center font-bold text-3xl text-white shadow-lg border-4 border-white dark:border-slate-900 relative z-10">
                        <?= htmlspecialchars(strtoupper(substr($user['username'], 0, 1))) ?>
                    </div>
                <?php endif; ?>
                
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mt-3"><?= htmlspecialchars($user['username']) ?></h2>
                
                <div class="mt-2 mb-5">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-primary-100 text-primary-700 dark:bg-primary-900/30 dark:text-primary-400 border border-primary-200 dark:border-primary-800/50">
                        <i class="ph-fill ph-shield-star mr-1.5"></i> <?= htmlspecialchars($roleName) ?>
                    </span>
                </div>
                
                <div class="pt-4 border-t border-gray-100 dark:border-slate-800/60 flex flex-col space-y-3 text-sm text-left">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center text-gray-500 dark:text-slate-400">
                            <i class="ph ph-calendar-blank mr-2 text-lg"></i>
                            <span><?= __('profile.joined') ?></span>
                        </div>
                        <span class="font-medium text-gray-800 dark:text-slate-200"><?= formatDate($user['created_at']) ?></span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center text-gray-500 dark:text-slate-400">
                            <i class="ph ph-clock-counter-clockwise mr-2 text-lg"></i>
                            <span><?= __('profile.last_login') ?></span>
                        </div>
                        <span class="font-medium text-gray-800 dark:text-slate-200"><?= $user['last_login'] ? formatDate($user['last_login'], true) : __('profile.never') ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Settings Forms -->
    <div class="lg:col-span-2 space-y-6">
        
        <!-- Profile Info Form -->
        <div class="bg-white dark:bg-black/20 dark:backdrop-blur-md rounded-2xl shadow-sm border border-gray-100 dark:border-slate-800 p-6 transition-colors">
            <form action="/profile" method="POST" enctype="multipart/form-data" id="profileInfoForm">
                <div class="sticky top-[-24px] md:top-[-32px] z-20 flex items-center justify-between mb-6 border-b border-gray-100 dark:border-slate-800 pb-4 pt-6 md:pt-8 -mt-6 md:-mt-8 -mx-6 px-6 bg-white/95 dark:bg-slate-900/95 backdrop-blur-md rounded-t-2xl">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white">
                        <?= __('profile.profile_info') ?>
                    </h3>
                    <button type="submit" class="px-5 py-2 text-sm font-medium text-white bg-sidebar hover:opacity-90 rounded-lg transition-colors shadow-sm flex items-center">
                        <i class="ph ph-floppy-disk mr-2 text-lg"></i> <?= __('profile.update_profile') ?>
                    </button>
                </div>
                <input type="hidden" name="csrf_token" value="<?= \App\Core\Session::getCsrfToken() ?>">
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1"><?= __('profile.profile_picture') ?></label>
                    <div class="flex items-center space-x-4">
                        <div id="profileImagePreview" class="w-16 h-16 rounded-full bg-gray-100 dark:bg-slate-800 flex items-center justify-center overflow-hidden border border-gray-200 dark:border-slate-700">
                            <?php if (!empty($user['profile_image'])): ?>
                                <img src="<?= htmlspecialchars($user['profile_image']) ?>" class="w-full h-full object-cover">
                            <?php else: ?>
                                <i class="ph ph-image text-gray-400 dark:text-slate-500 text-2xl"></i>
                            <?php endif; ?>
                        </div>
                        <input type="file" id="profile_image_input" accept="image/png, image/jpeg, image/webp" class="text-sm text-gray-500 dark:text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary-50 dark:file:bg-slate-800 file:text-primary-700 dark:file:text-primary-400 hover:file:bg-primary-100 dark:hover:file:bg-slate-700">
                        <input type="hidden" name="cropped_image" id="cropped_image_input">
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="first_name" class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1"><?= __('profile.first_name') ?? 'First Name' ?></label>
                        <input type="text" id="first_name" name="first_name" value="<?= htmlspecialchars($user['first_name'] ?? '') ?>"
                            class="w-full px-4 py-2.5 bg-gray-50 dark:bg-slate-900/50 rounded-lg border border-gray-200 dark:border-slate-700/50 text-gray-900 dark:text-white focus:bg-white dark:focus:bg-slate-800 focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 outline-none transition-all shadow-sm">
                    </div>
                    <div>
                        <label for="last_name" class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1"><?= __('profile.last_name') ?? 'Last Name' ?></label>
                        <input type="text" id="last_name" name="last_name" value="<?= htmlspecialchars($user['last_name'] ?? '') ?>"
                            class="w-full px-4 py-2.5 bg-gray-50 dark:bg-slate-900/50 rounded-lg border border-gray-200 dark:border-slate-700/50 text-gray-900 dark:text-white focus:bg-white dark:focus:bg-slate-800 focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 outline-none transition-all shadow-sm">
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1"><?= __('profile.username_no_change') ?></label>
                        <input type="text" value="<?= htmlspecialchars($user['username']) ?>" readonly
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-200 dark:border-slate-700/50 bg-gray-100 dark:bg-slate-800/80 text-gray-500 dark:text-slate-400 outline-none cursor-not-allowed">
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1"><?= __('profile.email_address') ?> <span class="text-red-500">*</span></label>
                        <input type="email" id="email" name="email" required value="<?= htmlspecialchars($user['email']) ?>"
                            class="w-full px-4 py-2.5 bg-gray-50 dark:bg-slate-900/50 rounded-lg border border-gray-200 dark:border-slate-700/50 text-gray-900 dark:text-white focus:bg-white dark:focus:bg-slate-800 focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 outline-none transition-all shadow-sm">
                    </div>
                </div>
                
            </form>
        </div>

        <!-- Change Password Form -->
        <div class="bg-white dark:bg-black/20 dark:backdrop-blur-md rounded-2xl shadow-sm border border-gray-100 dark:border-slate-800 p-6 transition-colors mt-6">
            <form action="/profile/password" method="POST" id="passwordForm">
                <div class="sticky top-[-24px] md:top-[-32px] z-20 flex items-center justify-between mb-6 border-b border-gray-100 dark:border-slate-800 pb-4 pt-6 md:pt-8 -mt-6 md:-mt-8 -mx-6 px-6 bg-white/95 dark:bg-slate-900/95 backdrop-blur-md rounded-t-2xl">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white">
                        <?= __('profile.change_password') ?>
                    </h3>
                    <button type="submit" class="px-5 py-2 text-sm font-medium text-white bg-sidebar hover:opacity-90 rounded-lg transition-colors shadow-sm flex items-center">
                        <i class="ph ph-key mr-2 text-lg"></i> <?= __('profile.update_password') ?>
                    </button>
                </div>
                <input type="hidden" name="csrf_token" value="<?= \App\Core\Session::getCsrfToken() ?>">
                
                <div class="grid grid-cols-1 gap-4 mb-4">
                    <div>
                        <label for="current_password" class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1"><?= __('profile.current_password') ?> <span class="text-red-500">*</span></label>
                        <input type="password" id="current_password" name="current_password" required
                            class="w-full px-4 py-2.5 bg-gray-50 dark:bg-slate-900/50 rounded-lg border border-gray-200 dark:border-slate-700/50 text-gray-900 dark:text-white focus:bg-white dark:focus:bg-slate-800 focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 outline-none transition-all shadow-sm">
                    </div>
                    <div>
                        <label for="new_password" class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1"><?= __('profile.new_password') ?> <span class="text-red-500">*</span></label>
                        <input type="password" id="new_password" name="new_password" required
                            class="w-full px-4 py-2.5 bg-gray-50 dark:bg-slate-900/50 rounded-lg border border-gray-200 dark:border-slate-700/50 text-gray-900 dark:text-white focus:bg-white dark:focus:bg-slate-800 focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 outline-none transition-all shadow-sm">
                    </div>
                    <div>
                        <label for="confirm_password" class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1"><?= __('profile.confirm_new_password') ?> <span class="text-red-500">*</span></label>
                        <input type="password" id="confirm_password" name="confirm_password" required
                            class="w-full px-4 py-2.5 bg-gray-50 dark:bg-slate-900/50 rounded-lg border border-gray-200 dark:border-slate-700/50 text-gray-900 dark:text-white focus:bg-white dark:focus:bg-slate-800 focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 outline-none transition-all shadow-sm">
                    </div>
                </div>
                
            </form>
        </div>

        <!-- 2FA Setup -->
        <div class="bg-white dark:bg-black/20 dark:backdrop-blur-md rounded-2xl shadow-sm border border-gray-100 dark:border-slate-800 p-6 transition-colors mt-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4 border-b border-gray-100 dark:border-slate-800 pb-2 flex items-center">
                <i class="ph ph-shield-check mr-2 text-primary-500"></i> <?= __('profile.two_factor') ?>
            </h3>

            <?php if ($user['two_factor_enabled']): ?>
                <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4 mb-4 flex items-start">
                    <i class="ph-fill ph-check-circle text-green-500 text-xl mr-3 mt-0.5"></i>
                    <div>
                        <h4 class="text-sm font-bold text-green-800 dark:text-green-400 mb-1"><?= __('profile.2fa_enabled') ?></h4>
                        <p class="text-xs text-green-700 dark:text-green-500 mb-3"><?= __('profile.2fa_enabled_desc') ?></p>
                        
                        <form action="/profile/2fa/disable" method="POST" class="flex gap-2">
                            <input type="hidden" name="csrf_token" value="<?= \App\Core\Session::getCsrfToken() ?>">
                            <input type="password" name="password" placeholder="<?= __('profile.current_password_placeholder') ?>" required
                                class="px-3 py-1.5 text-sm bg-white dark:bg-slate-800 rounded border border-gray-300 dark:border-slate-700 text-gray-900 dark:text-white outline-none focus:ring-1 focus:ring-red-500 focus:border-red-500">
                            <button type="submit" onclick="return confirm('<?= __('profile.disable_2fa_confirm') ?>')" class="px-4 py-1.5 text-sm font-medium text-white bg-red-500 rounded hover:bg-red-600 transition-colors shadow-sm">
                                <?= __('profile.disable_2fa') ?>
                            </button>
                        </form>
                    </div>
                </div>
            <?php else: ?>
                <div class="mb-4 text-sm text-gray-600 dark:text-slate-400">
                    <p class="mb-2"><?= __('profile.2fa_disabled_desc') ?></p>
                </div>

                <div class="flex flex-col md:flex-row items-center md:items-start gap-6 bg-slate-50 dark:bg-slate-800/50 p-4 rounded-xl border border-gray-100 dark:border-slate-700">
                    <div class="bg-white p-2 rounded-lg shadow-sm">
                        <?= $qrCodeSvg ?>
                    </div>
                    <div class="flex-1 w-full">
                        <p class="text-sm text-gray-700 dark:text-slate-300 mb-2 font-medium"><?= __('profile.2fa_manual_key') ?></p>
                        <code class="block bg-gray-200 dark:bg-slate-900 text-slate-800 dark:text-slate-200 px-3 py-2 rounded text-sm mb-4 break-all select-all border border-gray-300 dark:border-slate-700">
                            <?= htmlspecialchars($twoFactorSecret) ?>
                        </code>

                        <form action="/profile/2fa/enable" method="POST">
                            <input type="hidden" name="csrf_token" value="<?= \App\Core\Session::getCsrfToken() ?>">
                            <label for="totp_code" class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1"><?= __('profile.enter_verification_code') ?></label>
                            <div class="flex gap-2">
                                <input type="text" id="totp_code" name="totp_code" required maxlength="6" pattern="[0-9]{6}" placeholder="123456"
                                    class="w-full px-4 py-2 bg-white dark:bg-slate-800 rounded-lg border border-gray-300 dark:border-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition-colors tracking-widest text-center">
                                <button type="submit" class="px-5 py-2 text-sm font-medium text-white bg-sidebar rounded-lg hover:opacity-90 transition-colors shadow-sm whitespace-nowrap">
                                    <?= __('profile.verify_and_enable') ?>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Social Connections -->
        <div class="bg-white dark:bg-black/20 dark:backdrop-blur-md rounded-2xl shadow-sm border border-gray-100 dark:border-slate-800 p-6 transition-colors mt-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4 border-b border-gray-100 dark:border-slate-800 pb-2 flex items-center">
                <i class="ph ph-share-network mr-2 text-blue-500"></i> <?= __('profile.social_connections') ?? 'Social Connections' ?>
            </h3>
            <p class="text-sm text-gray-500 dark:text-slate-400 mb-6"><?= __('profile.social_connections_desc') ?? 'Connect your social accounts to log in quickly.' ?></p>

            <div class="space-y-4">
                <!-- Google -->
                <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-slate-800/50 rounded-xl border border-gray-100 dark:border-slate-700">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-white dark:bg-slate-900 rounded-xl flex items-center justify-center shadow-xs border border-gray-200 dark:border-slate-700 mr-4 shrink-0">
                            <svg class="w-5 h-5" viewBox="0 0 24 24">
                                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-gray-800 dark:text-white">Google</h4>
                            <p class="text-xs text-gray-500 dark:text-slate-400">
                                <?php if (!empty($user['google_id'])): ?>
                                    <span class="text-emerald-600 dark:text-emerald-400 font-medium flex items-center gap-1"><i class="ph-fill ph-check-circle"></i><?= __('profile.connected') ?></span>
                                <?php else: ?>
                                    <?= __('profile.not_connected') ?>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                    <div>
                        <?php if (!empty($user['google_id'])): ?>
                            <form action="/profile/social/google/disconnect" method="POST" class="inline" onsubmit="event.preventDefault(); confirmDisconnect(this, '<?= addslashes(__('profile.disconnect_confirm_google')) ?>');">
                                <input type="hidden" name="csrf_token" value="<?= \App\Core\Session::getCsrfToken() ?>">
                                <button type="submit" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-slate-300 bg-white dark:bg-slate-800 border border-gray-300 dark:border-slate-600 rounded-lg hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors shadow-xs">
                                    <?= __('profile.disconnect') ?>
                                </button>
                            </form>
                        <?php else: ?>
                            <a href="/auth/google" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors shadow-xs inline-block">
                                <?= __('profile.connect') ?>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- LINE -->
                <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-slate-800/50 rounded-xl border border-gray-100 dark:border-slate-700">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-[#06C755] rounded-xl flex items-center justify-center shadow-xs mr-4 shrink-0">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none">
                                <path fill="#FFFFFF" d="M19.365 9.863c.349 0 .63.285.63.631 0 .345-.281.63-.63.63H17.61v1.125h1.755c.349 0 .63.283.63.63 0 .344-.281.629-.63.629h-2.386c-.345 0-.627-.285-.627-.629V8.108c0-.345.282-.63.63-.63h2.386c.346 0 .627.285.627.63 0 .349-.281.63-.63.63H17.61v1.125h1.755zm-3.855 3.016c0 .27-.174.51-.432.596-.064.021-.133.031-.199.031-.211 0-.391-.09-.51-.25l-2.443-3.317v2.94c0 .344-.279.629-.631.629-.346 0-.626-.285-.626-.629V8.108c0-.27.173-.51.43-.595.06-.023.136-.033.194-.033.195 0 .375.104.495.254l2.462 3.33V8.108c0-.345.282-.63.63-.63.345 0 .63.285.63.63v4.771zm-5.741 0c0 .344-.282.629-.631.629-.345 0-.627-.285-.627-.629V8.108c0-.345.282-.63.627-.63.349 0 .631.285.631.63v4.771zm-2.466.629H4.917c-.345 0-.63-.285-.63-.629V8.108c0-.345.285-.63.63-.63.348 0 .63.285.63.63v4.141h1.756c.348 0 .629.283.629.63 0 .344-.281.629-.629.629M24 10.314C24 4.943 18.615.572 12 .572S0 4.943 0 10.314c0 4.811 4.27 8.842 10.035 9.608.391.082.923.258 1.058.59.12.301.079.77.038 1.08-.058.42-.27 1.62-.27 1.62s-.135.81.48.442c.615-.368 6.502-3.834 8.88-6.559C22.425 14.737 24 12.637 24 10.314"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-gray-800 dark:text-white">LINE</h4>
                            <p class="text-xs text-gray-500 dark:text-slate-400">
                                <?php if (!empty($user['line_id'])): ?>
                                    <span class="text-emerald-600 dark:text-emerald-400 font-medium flex items-center gap-1"><i class="ph-fill ph-check-circle"></i><?= __('profile.connected') ?></span>
                                <?php else: ?>
                                    <?= __('profile.not_connected') ?>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                    <div>
                        <?php if (!empty($user['line_id'])): ?>
                            <form action="/profile/social/line/disconnect" method="POST" class="inline" onsubmit="event.preventDefault(); confirmDisconnect(this, '<?= addslashes(__('profile.disconnect_confirm_line')) ?>');">
                                <input type="hidden" name="csrf_token" value="<?= \App\Core\Session::getCsrfToken() ?>">
                                <button type="submit" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-slate-300 bg-white dark:bg-slate-800 border border-gray-300 dark:border-slate-600 rounded-lg hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors shadow-xs">
                                    <?= __('profile.disconnect') ?>
                                </button>
                            </form>
                        <?php else: ?>
                            <a href="/auth/line" class="px-4 py-2 text-sm font-medium text-white bg-[#06C755] rounded-lg hover:bg-[#05b04c] transition-colors shadow-xs inline-block">
                                <?= __('profile.connect') ?>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Facebook -->
                <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-slate-800/50 rounded-xl border border-gray-100 dark:border-slate-700">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-[#1877F2] rounded-xl flex items-center justify-center shadow-xs mr-4 shrink-0">
                            <svg class="w-5 h-5 fill-white" viewBox="0 0 24 24">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-gray-800 dark:text-white">Facebook</h4>
                            <p class="text-xs text-gray-500 dark:text-slate-400">
                                <?php if (!empty($user['facebook_id'])): ?>
                                    <span class="text-emerald-600 dark:text-emerald-400 font-medium flex items-center gap-1"><i class="ph-fill ph-check-circle"></i><?= __('profile.connected') ?></span>
                                <?php else: ?>
                                    <?= __('profile.not_connected') ?>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                    <div>
                        <?php if (!empty($user['facebook_id'])): ?>
                            <form action="/profile/social/facebook/disconnect" method="POST" class="inline" onsubmit="event.preventDefault(); confirmDisconnect(this, '<?= addslashes(__('profile.disconnect_confirm_facebook')) ?>');">
                                <input type="hidden" name="csrf_token" value="<?= \App\Core\Session::getCsrfToken() ?>">
                                <button type="submit" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-slate-300 bg-white dark:bg-slate-800 border border-gray-300 dark:border-slate-600 rounded-lg hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors shadow-xs">
                                    <?= __('profile.disconnect') ?>
                                </button>
                            </form>
                        <?php else: ?>
                            <a href="/auth/facebook" class="px-4 py-2 text-sm font-medium text-white bg-[#1877F2] rounded-lg hover:bg-[#166fe5] transition-colors shadow-xs inline-block">
                                <?= __('profile.connect') ?>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- GitHub -->
                <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-slate-800/50 rounded-xl border border-gray-100 dark:border-slate-700">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-[#24292F] dark:bg-white rounded-xl flex items-center justify-center shadow-xs mr-4 shrink-0">
                            <svg class="w-5 h-5 fill-white dark:fill-[#24292F]" viewBox="0 0 24 24">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.53 1.032 1.53 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-gray-800 dark:text-white">GitHub</h4>
                            <p class="text-xs text-gray-500 dark:text-slate-400">
                                <?php if (!empty($user['github_id'])): ?>
                                    <span class="text-emerald-600 dark:text-emerald-400 font-medium flex items-center gap-1"><i class="ph-fill ph-check-circle"></i><?= __('profile.connected') ?></span>
                                <?php else: ?>
                                    <?= __('profile.not_connected') ?>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                    <div>
                        <?php if (!empty($user['github_id'])): ?>
                            <form action="/profile/social/github/disconnect" method="POST" class="inline" onsubmit="event.preventDefault(); confirmDisconnect(this, '<?= addslashes(__('profile.disconnect_confirm_github')) ?>');">
                                <input type="hidden" name="csrf_token" value="<?= \App\Core\Session::getCsrfToken() ?>">
                                <button type="submit" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-slate-300 bg-white dark:bg-slate-800 border border-gray-300 dark:border-slate-600 rounded-lg hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors shadow-xs">
                                    <?= __('profile.disconnect') ?>
                                </button>
                            </form>
                        <?php else: ?>
                            <a href="/auth/github" class="px-4 py-2 text-sm font-medium text-white bg-[#24292F] rounded-lg hover:bg-[#1b1f24] transition-colors shadow-xs inline-block">
                                <?= __('profile.connect') ?>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Active Sessions -->
        <div class="bg-white dark:bg-black/20 dark:backdrop-blur-md rounded-2xl shadow-sm border border-gray-100 dark:border-slate-800 p-6 transition-colors mt-6 flex items-center justify-between">
            <div>
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-1 flex items-center">
                    <i class="ph ph-devices mr-2 text-purple-500"></i> <?= __('profile.active_sessions') ?>
                </h3>
                <p class="text-sm text-gray-500 dark:text-slate-400"><?= __('profile.active_sessions_desc') ?></p>
            </div>
            <a href="/profile/sessions" class="px-6 py-2.5 text-sm font-medium text-white bg-sidebar rounded-lg hover:-translate-y-0.5 hover:shadow-lg hover:shadow-primary-500/30 transition-all duration-200 whitespace-nowrap flex items-center">
                <?= __('profile.manage_sessions') ?> <i class="ph ph-arrow-right ml-2"></i>
            </a>
        </div>

    </div>
</div>

<!-- Cropper Modal -->
<div id="cropperModal" class="fixed inset-0 z-[100] hidden bg-black/50 backdrop-blur-sm flex items-center justify-center p-4 opacity-0 transition-opacity duration-300">
    <div class="bg-white dark:bg-black/20 dark:backdrop-blur-md rounded-2xl shadow-2xl w-full max-w-lg mx-4 overflow-hidden border border-gray-200 dark:border-slate-800 scale-95 transition-transform duration-300 transform flex flex-col max-h-[85vh] md:max-h-[90vh]" id="cropperModalContent">
        <!-- Fixed Modal Header -->
        <div class="p-4 border-b border-gray-100 dark:border-slate-800 flex justify-between items-center flex-shrink-0 bg-white dark:bg-slate-900/80">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white"><?= __('profile.crop_profile_picture') ?></h3>
            <button type="button" id="closeCropperBtn" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                <i class="ph ph-x text-xl"></i>
            </button>
        </div>
        <!-- Scrollable Modal Body -->
        <div class="p-4 bg-gray-50 dark:bg-slate-950 flex justify-center items-center flex-1 overflow-y-auto" style="min-height: 320px; max-height: 420px;">
            <div class="w-full h-full flex items-center justify-center">
                <img id="cropperImage" src="" class="max-w-full hidden">
            </div>
        </div>
        <!-- Fixed Modal Footer -->
        <div class="p-4 border-t border-gray-100 dark:border-slate-800 flex justify-end gap-3 bg-white dark:bg-slate-900/80 flex-shrink-0">
            <button type="button" id="cancelCropBtn" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-slate-300 bg-white dark:bg-slate-800 border border-gray-300 dark:border-slate-700 rounded-lg hover:bg-primary-50 dark:hover:bg-primary-900/20 hover:text-primary-600 dark:hover:text-primary-300 hover:border-primary-200 dark:hover:border-primary-800 transition-colors"><?= __('common.cancel') ?></button>
            <button type="button" id="applyCropBtn" class="px-4 py-2 text-sm font-medium text-white bg-sidebar rounded-lg hover:opacity-90 transition-colors shadow-sm"><?= __('profile.apply_crop') ?></button>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        let cropper = null;
        const inputImage = document.getElementById('profile_image_input');
        const cropperModal = document.getElementById('cropperModal');
        const cropperModalContent = document.getElementById('cropperModalContent');
        const cropperImage = document.getElementById('cropperImage');
        const closeBtn = document.getElementById('closeCropperBtn');
        const cancelBtn = document.getElementById('cancelCropBtn');
        const applyBtn = document.getElementById('applyCropBtn');
        const previewDiv = document.getElementById('profileImagePreview');
        const croppedImageInput = document.getElementById('cropped_image_input');

        function openModal() {
            cropperModal.classList.remove('hidden');
            // Trigger reflow
            void cropperModal.offsetWidth;
            cropperModal.classList.remove('opacity-0');
            cropperModalContent.classList.remove('scale-95');
        }

        function closeModal() {
            cropperModal.classList.add('opacity-0');
            cropperModalContent.classList.add('scale-95');
            setTimeout(() => {
                cropperModal.classList.add('hidden');
                if (cropper) {
                    cropper.destroy();
                    cropper = null;
                }
                inputImage.value = ''; // Reset input to allow selecting same file again if canceled
            }, 300);
        }

        inputImage.addEventListener('change', function (e) {
            const files = e.target.files;
            if (files && files.length > 0) {
                const reader = new FileReader();
                reader.onload = function (event) {
                    cropperImage.src = event.target.result;
                    cropperImage.classList.remove('hidden');
                    
                    openModal();
                    
                    if (cropper) {
                        cropper.destroy();
                    }
                    
                    cropper = new Cropper(cropperImage, {
                        aspectRatio: 1,
                        viewMode: 1,
                        dragMode: 'move',
                        autoCropArea: 0.8,
                        restore: false,
                        guides: true,
                        center: true,
                        highlight: false,
                        cropBoxMovable: true,
                        cropBoxResizable: true,
                        toggleDragModeOnDblclick: false,
                        background: false
                    });
                };
                reader.readAsDataURL(files[0]);
            }
        });

        const closeActions = [closeBtn, cancelBtn];
        closeActions.forEach(btn => btn.addEventListener('click', closeModal));

        applyBtn.addEventListener('click', function () {
            if (!cropper) return;
            
            const canvas = cropper.getCroppedCanvas({
                width: 256,
                height: 256,
                imageSmoothingEnabled: true,
                imageSmoothingQuality: 'high',
            });

            const base64Image = canvas.toDataURL('image/webp', 0.9);
            
            // Set preview
            previewDiv.innerHTML = `<img src="${base64Image}" class="w-full h-full object-cover">`;
            
            // Set hidden input for form submission
            croppedImageInput.value = base64Image;
            
            closeModal();
        });
    });

    function confirmDisconnect(form, message) {
        Swal.fire({
            title: '<?= addslashes(__('common.confirm')) ?>',
            text: message,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: '<?= addslashes(__('profile.disconnect')) ?>',
            cancelButtonText: '<?= addslashes(__('common.cancel')) ?>',
            reverseButtons: true,
            customClass: {
                popup: 'dark:bg-slate-800 dark:text-white border border-gray-100 dark:border-slate-700 shadow-2xl rounded-2xl'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    }
</script>
