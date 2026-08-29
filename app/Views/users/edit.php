<form action="/users/<?= \App\Core\Security::encodeId($user['id']) ?>/edit" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?= \App\Core\Session::getCsrfToken() ?>">

    <div class="mb-6 flex items-center justify-between sticky -top-6 md:-top-8 z-40 bg-slate-50/90 dark:bg-[#0f172a]/90 backdrop-blur-md py-4 -mx-6 md:-mx-8 px-6 md:px-8 border-b border-gray-200 dark:border-slate-800">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white"><?= __('users.edit_user') ?></h1>
            <div class="text-sm text-gray-500 dark:text-slate-400 mt-1 flex items-center">
                <a href="/users" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors"><?= __('common.user_management') ?></a>
                <i class="ph ph-caret-right mx-2 text-xs"></i>
                <span class="text-gray-700 dark:text-slate-300"><?= __('users.edit_crumb') ?></span>
                <i class="ph ph-caret-right mx-2 text-xs"></i>
                <span class="text-gray-700 dark:text-slate-300"><?= htmlspecialchars($user['username']) ?></span>
            </div>
        </div>
        <div class="flex items-center space-x-3">
            <a href="/users" class="px-5 py-2.5 text-sm font-medium text-gray-700 dark:text-slate-300 bg-white dark:bg-slate-800 border border-gray-300 dark:border-slate-700 rounded-md hover:bg-slate-50 dark:hover:bg-slate-700 transition-all duration-200 shadow-sm">
                <?= __('common.cancel') ?>
            </a>
            <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-sidebar rounded-md hover:opacity-90 transition-colors shadow-sm flex items-center">
                <i class="ph ph-floppy-disk mr-2 text-lg"></i> <?= __('common.save') ?>
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Left Column: Profile Picture -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white dark:bg-slate-900/40 dark:backdrop-blur-xl rounded-md shadow-sm border border-slate-200 dark:border-slate-700/50 p-6 transition-all hover:shadow-md flex flex-col items-center text-center">
                <h3 class="text-sm font-semibold text-gray-800 dark:text-white mb-5 w-full text-left border-b border-gray-100 dark:border-slate-800 pb-3 flex items-center">
                    <i class="ph ph-image text-primary-500 mr-2 text-lg"></i> <?= __('users.profile_picture') === 'users.profile_picture' ? 'รูปโปรไฟล์' : __('users.profile_picture') ?>
                </h3>
                
                <div class="w-32 h-32 rounded-full border-4 border-slate-50 dark:border-slate-800 shadow-sm overflow-hidden mb-5 bg-slate-100 dark:bg-slate-800 flex items-center justify-center relative group cursor-pointer" onclick="document.getElementById('avatar').click()">
                    <?php
                        $avatarUrl = !empty($user['avatar']) ? '/uploads/avatars/' . $user['avatar'] : 'https://ui-avatars.com/api/?name=' . urlencode($user['username']) . '&background=random';
                    ?>
                    <img id="avatar-preview" src="<?= htmlspecialchars($avatarUrl) ?>" alt="Avatar Preview" class="w-full h-full object-cover">
                    
                    <div class="absolute inset-0 bg-black/40 hidden group-hover:flex items-center justify-center transition-all backdrop-blur-sm">
                        <i class="ph ph-camera text-white text-2xl"></i>
                    </div>
                </div>
                
                <input type="file" name="avatar" id="avatar" class="hidden" accept="image/jpeg,image/png,image/gif" onchange="previewAvatar(this)">
                
                <button type="button" onclick="document.getElementById('avatar').click()" class="px-4 py-2 text-xs font-medium text-primary-600 dark:text-primary-400 bg-primary-50 dark:bg-primary-900/20 rounded-md hover:bg-primary-100 dark:hover:bg-primary-900/40 transition-colors border border-primary-100 dark:border-primary-800/50 w-full flex items-center justify-center">
                    <i class="ph ph-upload-simple mr-1.5"></i> <?= __('common.change_image') === 'common.change_image' ? 'เปลี่ยนรูปภาพ' : __('common.change_image') ?>
                </button>
            </div>
            
            <div class="bg-primary-50/50 dark:bg-primary-900/10 rounded-md border border-primary-200/60 dark:border-primary-800/40 p-5 transition-all shadow-sm">
                <div class="flex items-start space-x-3 text-sm text-primary-800 dark:text-primary-200">
                    <i class="ph-fill ph-info text-primary-500 dark:text-primary-400 text-xl mt-0.5 flex-shrink-0 animate-pulse"></i>
                    <p class="leading-relaxed"><?= __('users.picture_hint') === 'users.picture_hint' ? 'รองรับไฟล์ JPG, PNG ขนาดไม่เกิน 2MB แนะนำให้ใช้รูปทรงสี่เหลี่ยมจัตุรัส' : __('users.picture_hint') ?></p>
                </div>
            </div>
        </div>

        <!-- Right Column: User Information -->
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-slate-900/40 dark:backdrop-blur-xl rounded-md shadow-sm border border-slate-200 dark:border-slate-700/50 p-6 md:p-8 transition-all hover:shadow-md">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-6 border-b border-gray-100 dark:border-slate-800 pb-3 flex items-center">
                    <i class="ph ph-user-circle text-primary-500 mr-2 text-xl"></i> <?= __('users.user_info') === 'users.user_info' ? 'ข้อมูลทั่วไป' : __('users.user_info') ?>
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-2">
                    <!-- Username -->
                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5"><?= __('users.username') ?> <span class="text-red-500">*</span></label>
                        <input type="text" id="username" name="username" value="<?= htmlspecialchars($user['username']) ?>" required 
                            class="w-full px-4 py-2.5 rounded-md border border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-800 text-gray-900 dark:text-white focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-primary-500/40 focus:border-primary-500 hover:border-slate-400 dark:hover:border-slate-500 transition-all duration-200 shadow-sm outline-none">
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5"><?= __('users.email') ?> <span class="text-red-500">*</span></label>
                        <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required 
                            class="w-full px-4 py-2.5 rounded-md border border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-800 text-gray-900 dark:text-white focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-primary-500/40 focus:border-primary-500 hover:border-slate-400 dark:hover:border-slate-500 transition-all duration-200 shadow-sm outline-none">
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5"><?= __('users.password') ?> <span class="text-xs text-gray-500 dark:text-slate-400 font-normal">(<?= __('users.leave_blank') ?>)</span></label>
                        <input type="password" id="password" name="password" 
                            class="w-full px-4 py-2.5 rounded-md border border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-800 text-gray-900 dark:text-white focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-primary-500/40 focus:border-primary-500 hover:border-slate-400 dark:hover:border-slate-500 transition-all duration-200 shadow-sm outline-none"
                            placeholder="••••••••">
                    </div>

                    <!-- Role -->
                    <div>
                        <label for="role_id" class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5"><?= __('users.assign_role') ?></label>
                        <select id="role_id" name="role_id" 
                            class="w-full px-4 py-2.5 rounded-md border border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-800 text-gray-900 dark:text-white focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-primary-500/40 focus:border-primary-500 hover:border-slate-400 dark:hover:border-slate-500 transition-all duration-200 shadow-sm outline-none cursor-pointer">
                            <option value=""><?= __('users.no_role') ?></option>
                            <?php foreach ($roles as $role): ?>
                                <option value="<?= \App\Core\Security::encodeId($role['id']) ?>" <?= $user['role_id'] == $role['id'] ? 'selected' : '' ?>><?= htmlspecialchars($role['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5"><?= __('users.status') ?></label>
                        <select id="status" name="status" 
                            class="w-full px-4 py-2.5 rounded-md border border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-800 text-gray-900 dark:text-white focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-primary-500/40 focus:border-primary-500 hover:border-slate-400 dark:hover:border-slate-500 transition-all duration-200 shadow-sm outline-none cursor-pointer">
                            <option value="active" <?= $user['status'] == 'active' ? 'selected' : '' ?>><?= __('common.active') ?></option>
                            <option value="inactive" <?= $user['status'] == 'inactive' ? 'selected' : '' ?>><?= __('common.inactive') ?></option>
                            <option value="banned" <?= $user['status'] == 'banned' ? 'selected' : '' ?>><?= __('common.banned') ?></option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
function previewAvatar(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('avatar-preview').src = e.target.result;
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
