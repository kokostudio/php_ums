<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white"><?= __('profile.title') ?></h1>
        <p class="text-sm text-gray-500 dark:text-slate-400 mt-1"><?= __('profile.subtitle') ?></p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Left Column: User Card -->
    <div class="lg:col-span-1">
        <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-800 p-6 text-center relative transition-colors">
            <?php if (!empty($user['profile_image'])): ?>
                <img src="<?= htmlspecialchars($user['profile_image']) ?>" alt="Profile Picture" class="w-24 h-24 mx-auto rounded-full object-cover shadow-inner mb-4 border-2 border-white dark:border-slate-900 outline outline-2 outline-blue-100 dark:outline-blue-900/30">
            <?php else: ?>
                <div class="w-24 h-24 mx-auto rounded-full bg-gradient-to-tr from-blue-500 to-purple-500 flex items-center justify-center font-bold text-3xl text-white shadow-inner mb-4">
                    <?= htmlspecialchars(strtoupper(substr($user['username'], 0, 1))) ?>
                </div>
            <?php endif; ?>
            <h2 class="text-xl font-bold text-gray-900 dark:text-white"><?= htmlspecialchars($user['username']) ?></h2>
            <p class="text-sm text-gray-500 dark:text-slate-400 mb-4"><?= htmlspecialchars($roleName) ?></p>
            
            <div class="pt-4 border-t border-gray-100 dark:border-slate-800 flex justify-between text-sm text-gray-600 dark:text-slate-300">
                <span class="font-medium"><?= __('profile.joined') ?></span>
                <span><?= formatDate($user['created_at']) ?></span>
            </div>
            <div class="pt-2 flex justify-between text-sm text-gray-600 dark:text-slate-300">
                <span class="font-medium"><?= __('profile.last_login') ?></span>
                <span><?= $user['last_login'] ? formatDate($user['last_login'], true) : __('profile.never') ?></span>
            </div>
        </div>
    </div>

    <!-- Right Column: Settings Forms -->
    <div class="lg:col-span-2 space-y-6">
        
        <!-- General Info Form -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-800 p-6 transition-colors">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4 border-b border-gray-100 dark:border-slate-800 pb-2"><?= __('profile.general_info') ?></h3>
            <form action="/profile" method="POST" enctype="multipart/form-data">
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
                        <input type="file" name="profile_image" id="profile_image" accept="image/*" class="text-sm text-gray-500 dark:text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 dark:file:bg-slate-800 file:text-blue-700 dark:file:text-blue-400 hover:file:bg-blue-100 dark:hover:file:bg-slate-700">
                    </div>
                </div>
                
                <div class="grid grid-cols-1 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1"><?= __('profile.username_no_change') ?></label>
                        <input type="text" value="<?= htmlspecialchars($user['username']) ?>" readonly
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-slate-700 bg-gray-50 dark:bg-slate-800/50 text-gray-500 dark:text-slate-400 outline-none">
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1"><?= __('profile.email_address') ?> <span class="text-red-500">*</span></label>
                        <input type="email" id="email" name="email" required value="<?= htmlspecialchars($user['email']) ?>"
                            class="w-full px-4 py-2.5 bg-white dark:bg-slate-800 rounded-lg border border-gray-300 dark:border-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-colors">
                    </div>
                </div>
                
                <div class="flex justify-end">
                    <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors shadow-sm flex items-center">
                        <i class="ph ph-floppy-disk mr-2"></i> <?= __('profile.update_profile') ?>
                    </button>
                </div>
            </form>
        </div>

        <!-- Change Password Form -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-800 p-6 transition-colors">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4 border-b border-gray-100 dark:border-slate-800 pb-2"><?= __('profile.change_password') ?></h3>
            <form action="/profile/password" method="POST">
                <input type="hidden" name="csrf_token" value="<?= \App\Core\Session::getCsrfToken() ?>">
                
                <div class="grid grid-cols-1 gap-4 mb-4">
                    <div>
                        <label for="current_password" class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1"><?= __('profile.current_password') ?> <span class="text-red-500">*</span></label>
                        <input type="password" id="current_password" name="current_password" required
                            class="w-full px-4 py-2.5 bg-white dark:bg-slate-800 rounded-lg border border-gray-300 dark:border-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-colors">
                    </div>
                    <div>
                        <label for="new_password" class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1"><?= __('profile.new_password') ?> <span class="text-red-500">*</span></label>
                        <input type="password" id="new_password" name="new_password" required
                            class="w-full px-4 py-2.5 bg-white dark:bg-slate-800 rounded-lg border border-gray-300 dark:border-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-colors">
                    </div>
                    <div>
                        <label for="confirm_password" class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1"><?= __('profile.confirm_new_password') ?> <span class="text-red-500">*</span></label>
                        <input type="password" id="confirm_password" name="confirm_password" required
                            class="w-full px-4 py-2.5 bg-white dark:bg-slate-800 rounded-lg border border-gray-300 dark:border-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-colors">
                    </div>
                </div>
                
                <div class="flex justify-end">
                    <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-slate-800 rounded-lg hover:bg-slate-900 transition-colors shadow-sm flex items-center">
                        <i class="ph ph-lock-key mr-2"></i> <?= __('profile.update_password') ?>
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>

<script>
    document.getElementById('profile_image').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('profileImagePreview');
                preview.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover">`;
            }
            reader.readAsDataURL(file);
        }
    });
</script>
