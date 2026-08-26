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
        
        <!-- General Info Form -->
        <div class="bg-white dark:bg-black/20 dark:backdrop-blur-md rounded-2xl shadow-sm border border-gray-100 dark:border-slate-800 p-6 transition-colors">
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
                        <input type="file" id="profile_image_input" accept="image/png, image/jpeg, image/webp" class="text-sm text-gray-500 dark:text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary-50 dark:file:bg-slate-800 file:text-primary-700 dark:file:text-primary-400 hover:file:bg-primary-100 dark:hover:file:bg-slate-700">
                        <input type="hidden" name="cropped_image" id="cropped_image_input">
                    </div>
                </div>
                
                <div class="grid grid-cols-1 gap-4 mb-4">
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
                
                <div class="flex justify-end">
                    <button type="submit" class="px-6 py-2.5 text-sm font-medium text-white bg-sidebar rounded-lg hover:-translate-y-0.5 hover:shadow-lg hover:shadow-primary-500/30 transition-all duration-200 flex items-center">
                        <i class="ph ph-floppy-disk mr-2"></i> <?= __('profile.update_profile') ?>
                    </button>
                </div>
            </form>
        </div>

        <!-- Change Password Form -->
        <div class="bg-white dark:bg-black/20 dark:backdrop-blur-md rounded-2xl shadow-sm border border-gray-100 dark:border-slate-800 p-6 transition-colors">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4 border-b border-gray-100 dark:border-slate-800 pb-2"><?= __('profile.change_password') ?></h3>
            <form action="/profile/password" method="POST">
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
                
                <div class="flex justify-end">
                    <button type="submit" class="px-6 py-2.5 text-sm font-medium text-white bg-sidebar rounded-lg hover:-translate-y-0.5 hover:shadow-lg hover:shadow-primary-500/30 transition-all duration-200 flex items-center">
                        <i class="ph ph-key mr-2"></i> <?= __('profile.update_password') ?>
                    </button>
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
<div id="cropperModal" class="fixed inset-0 z-[100] hidden bg-black/50 backdrop-blur-sm flex items-center justify-center opacity-0 transition-opacity duration-300">
    <div class="bg-white dark:bg-black/20 dark:backdrop-blur-md rounded-2xl shadow-2xl w-full max-w-lg mx-4 overflow-hidden border border-gray-200 dark:border-slate-800 scale-95 transition-transform duration-300 transform" id="cropperModalContent">
        <div class="p-4 border-b border-gray-100 dark:border-slate-800 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white"><?= __('profile.crop_profile_picture') ?></h3>
            <button type="button" id="closeCropperBtn" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                <i class="ph ph-x text-xl"></i>
            </button>
        </div>
        <div class="p-4 bg-gray-50 dark:bg-slate-950 flex justify-center items-center" style="height: 400px;">
            <div class="w-full h-full">
                <img id="cropperImage" src="" class="max-w-full hidden">
            </div>
        </div>
        <div class="p-4 border-t border-gray-100 dark:border-slate-800 flex justify-end gap-3 bg-white dark:bg-black/20 dark:backdrop-blur-md">
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
</script>
