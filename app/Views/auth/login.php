<div class="bg-white dark:bg-[#1a2234] rounded-2xl shadow-xl border border-gray-100 dark:border-slate-800/60 overflow-hidden transition-colors w-full">
    <div class="p-3 md:p-6">
        <!-- Form Header -->
        <div class="mb-4">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white flex items-center">
                <i class="ph ph-sign-in text-blue-600 dark:text-blue-500 mr-2"></i>
                เข้าสู่ระบบ
            </h2>
            <p class="text-sm text-gray-500 dark:text-slate-400 mt-1">เข้าสู่ระบบศูนย์กลาง <?php echo htmlspecialchars(\App\Core\Settings::get('site_name', 'WorkPOS')) ?></p>
        </div>

        <form action="/login" method="POST" id="loginForm" class="space-y-4" novalidate>
            <input type="hidden" name="csrf_token" value="<?php echo \App\Core\Session::getCsrfToken() ?>">

            <div class="space-y-1.5">
                <label for="username" class="block text-sm font-medium text-gray-700 dark:text-slate-300"><?php echo __('auth.username') ?></label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400 dark:text-slate-500">
                        <i class="ph ph-user text-lg" id="username-icon"></i>
                    </div>
                    <?php $oldUser = \App\Core\Session::getFlash('old_username') ?? ''; ?>
                    <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($oldUser) ?>" required autofocus autocomplete="username"
                        class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-300 dark:border-slate-700 bg-white dark:bg-[#0f172a] text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-colors"
                        placeholder="name@example.com หรือ username"
                        oninput="clearError('username')">
                </div>
                <p id="username-error" class="hidden text-xs text-red-500 font-medium"></p>
            </div>

            <div class="space-y-1.5">
                <label for="password" class="block text-sm font-medium text-gray-700 dark:text-slate-300"><?php echo __('auth.password') ?></label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400 dark:text-slate-500">
                        <i class="ph ph-lock-key text-lg" id="password-icon"></i>
                    </div>
                    <input type="password" id="password" name="password" required autocomplete="current-password"
                        class="w-full pl-10 pr-10 py-2.5 rounded-xl border border-gray-300 dark:border-slate-700 bg-white dark:bg-[#0f172a] text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-colors"
                        placeholder="••••••••"
                        oninput="clearError('password')">
                    <button type="button" onclick="togglePassword()" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 dark:text-slate-500 hover:text-gray-600 dark:hover:text-slate-300 transition-colors">
                        <i id="togglePasswordIcon" class="ph ph-eye text-lg"></i>
                    </button>
                </div>
                <p id="password-error" class="hidden text-xs text-red-500 font-medium"></p>
            </div>

            <div class="flex items-center justify-between pt-1">
                <div class="flex items-center">
                    <input id="remember" name="remember" type="checkbox" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 dark:border-slate-700 rounded dark:bg-[#0f172a]">
                    <label for="remember" class="ml-2 block text-sm text-gray-700 dark:text-slate-300"><?php echo __('auth.remember_me') ?></label>
                </div>
                <div class="text-sm">
                    <a href="/forgot-password" class="font-medium text-blue-600 hover:text-blue-500 dark:text-blue-400 transition-colors"><?php echo __('auth.forgot_password') ?>?</a>
                </div>
            </div>

            <div class="pt-2">
                <button type="submit" class="w-full flex justify-center items-center px-4 py-2 rounded-xl shadow-lg shadow-blue-500/30 dark:shadow-blue-900/20 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all">
                    <i class="ph ph-sign-in mr-2 text-lg"></i> <?php echo __('auth.sign_in') ?>
                </button>
            </div>
        </form>

        <?php if ($unverifiedEmail = \App\Core\Session::getFlash('unverified_email')): ?>
        <form action="/resend-verification" method="POST" class="mt-4">
            <input type="hidden" name="csrf_token" value="<?php echo \App\Core\Session::getCsrfToken() ?>">
            <input type="hidden" name="email" value="<?php echo htmlspecialchars($unverifiedEmail) ?>">
            <button type="submit" class="w-full flex justify-center items-center px-4 py-2 rounded-xl text-sm font-medium text-blue-600 bg-blue-50 dark:bg-blue-900/30 hover:bg-blue-100 dark:hover:bg-blue-900/50 transition-colors">
                <i class="ph ph-envelope-simple mr-2"></i> ส่งอีเมลยืนยันอีกครั้ง
            </button>
        </form>
        <?php endif; ?>

            <?php 
                $googleEnabled   = \App\Core\Settings::get('enable_google_login') === '1';
                $lineEnabled     = \App\Core\Settings::get('enable_line_login') === '1';
                $facebookEnabled = \App\Core\Settings::get('enable_facebook_login') === '1';
                $githubEnabled   = \App\Core\Settings::get('enable_github_login') === '1';
                $enabledCount    = ($googleEnabled ? 1 : 0) + ($lineEnabled ? 1 : 0) + ($facebookEnabled ? 1 : 0) + ($githubEnabled ? 1 : 0);
                
                if ($enabledCount > 0): 
            ?>
            <!-- Social Login Divider -->
            <div class="relative py-2">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-300 dark:border-slate-700"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-2 bg-white dark:bg-[#1a2234] text-gray-500 dark:text-slate-400"><?= __('auth.or_login_with') ?></span>
                </div>
            </div>

            <!-- Social Login Buttons -->
            <div class="grid <?= $enabledCount === 1 ? 'grid-cols-1' : ($enabledCount === 2 ? 'grid-cols-2' : ($enabledCount === 3 ? 'grid-cols-3' : 'grid-cols-2 sm:grid-cols-4')) ?> gap-2.5">
                <?php if ($googleEnabled): ?>
                <a href="/auth/google" class="w-full flex justify-center items-center px-3 py-2.5 rounded-xl border border-gray-300 dark:border-slate-700 bg-white dark:bg-[#0f172a] text-sm font-medium text-gray-700 dark:text-slate-300 hover:bg-gray-50 dark:hover:bg-slate-800 transition-colors shadow-xs" title="Google">
                    <svg class="w-5 h-5 mr-1.5 shrink-0" viewBox="0 0 24 24">
                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                    </svg>
                    <span>Google</span>
                </a>
                <?php endif; ?>
                
                <?php if ($lineEnabled): ?>
                <a href="/auth/line" class="w-full flex justify-center items-center px-3 py-2.5 rounded-xl border border-[#06C755] bg-[#06C755] text-sm font-medium text-white hover:bg-[#05b04c] transition-colors shadow-xs" title="LINE">
                    <svg class="w-5 h-5 mr-1.5 shrink-0" viewBox="0 0 24 24" fill="none">
                        <path fill="#FFFFFF" d="M19.365 9.863c.349 0 .63.285.63.631 0 .345-.281.63-.63.63H17.61v1.125h1.755c.349 0 .63.283.63.63 0 .344-.281.629-.63.629h-2.386c-.345 0-.627-.285-.627-.629V8.108c0-.345.282-.63.63-.63h2.386c.346 0 .627.285.627.63 0 .349-.281.63-.63.63H17.61v1.125h1.755zm-3.855 3.016c0 .27-.174.51-.432.596-.064.021-.133.031-.199.031-.211 0-.391-.09-.51-.25l-2.443-3.317v2.94c0 .344-.279.629-.631.629-.346 0-.626-.285-.626-.629V8.108c0-.27.173-.51.43-.595.06-.023.136-.033.194-.033.195 0 .375.104.495.254l2.462 3.33V8.108c0-.345.282-.63.63-.63.345 0 .63.285.63.63v4.771zm-5.741 0c0 .344-.282.629-.631.629-.345 0-.627-.285-.627-.629V8.108c0-.345.282-.63.627-.63.349 0 .631.285.631.63v4.771zm-2.466.629H4.917c-.345 0-.63-.285-.63-.629V8.108c0-.345.285-.63.63-.63.348 0 .63.285.63.63v4.141h1.756c.348 0 .629.283.629.63 0 .344-.281.629-.629.629M24 10.314C24 4.943 18.615.572 12 .572S0 4.943 0 10.314c0 4.811 4.27 8.842 10.035 9.608.391.082.923.258 1.058.59.12.301.079.77.038 1.08-.058.42-.27 1.62-.27 1.62s-.135.81.48.442c.615-.368 6.502-3.834 8.88-6.559C22.425 14.737 24 12.637 24 10.314"/>
                    </svg>
                    <span>LINE</span>
                </a>
                <?php endif; ?>

                <?php if ($facebookEnabled): ?>
                <a href="/auth/facebook" class="w-full flex justify-center items-center px-3 py-2.5 rounded-xl border border-[#1877F2] bg-[#1877F2] text-sm font-medium text-white hover:bg-[#166fe5] transition-colors shadow-xs" title="Facebook">
                    <svg class="w-5 h-5 mr-1.5 fill-white shrink-0" viewBox="0 0 24 24">
                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                    </svg>
                    <span>Facebook</span>
                </a>
                <?php endif; ?>

                <?php if ($githubEnabled): ?>
                <a href="/auth/github" class="w-full flex justify-center items-center px-3 py-2.5 rounded-xl border border-gray-300 dark:border-slate-700 bg-[#24292F] text-white hover:bg-[#1b1f24] transition-colors shadow-xs" title="GitHub">
                    <svg class="w-5 h-5 mr-1.5 fill-white shrink-0" viewBox="0 0 24 24">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.53 1.032 1.53 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z"/>
                    </svg>
                    <span>GitHub</span>
                </a>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <?php if (\App\Core\Settings::get('enable_registration') !== '0'): ?>
            <div class="text-center pt-4">
                <p class="text-sm text-gray-600 dark:text-slate-400">
                    <?= __('auth.dont_have_account') ?> <a href="/register" class="font-semibold text-blue-600 dark:text-blue-400 hover:underline transition-colors"><?= __('auth.register') ?></a>
                </p>
            </div>
            <?php endif; ?>
    </div>
</div>

<script>
function togglePassword() {
    const passwordInput = document.getElementById('password');
    const icon = document.getElementById('togglePasswordIcon');

    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        icon.classList.remove('ph-eye');
        icon.classList.add('ph-eye-slash');
    } else {
        passwordInput.type = 'password';
        icon.classList.remove('ph-eye-slash');
        icon.classList.add('ph-eye');
    }
}

function showError(fieldId, message) {
    const input = document.getElementById(fieldId);
    const errorMsg = document.getElementById(fieldId + '-error');
    const icon = document.getElementById(fieldId + '-icon');

    input.classList.remove('border-gray-300', 'dark:border-slate-700', 'focus:border-blue-500', 'focus:ring-blue-500');
    input.classList.add('border-red-500', 'dark:border-red-500', 'focus:border-red-500', 'focus:ring-red-500');

    icon.parentElement.classList.remove('text-gray-400', 'dark:text-slate-500');
    icon.parentElement.classList.add('text-red-500');

    errorMsg.textContent = message;
    errorMsg.classList.remove('hidden');

    // add small shake animation to input container
    input.parentElement.classList.add('animate__animated', 'animate__headShake');
    setTimeout(() => {
        input.parentElement.classList.remove('animate__animated', 'animate__headShake');
    }, 500);
}

function clearError(fieldId) {
    const input = document.getElementById(fieldId);
    const errorMsg = document.getElementById(fieldId + '-error');
    const icon = document.getElementById(fieldId + '-icon');

    input.classList.add('border-gray-300', 'dark:border-slate-700', 'focus:border-blue-500', 'focus:ring-blue-500');
    input.classList.remove('border-red-500', 'dark:border-red-500', 'focus:border-red-500', 'focus:ring-red-500');

    icon.parentElement.classList.add('text-gray-400', 'dark:text-slate-500');
    icon.parentElement.classList.remove('text-red-500');

    errorMsg.classList.add('hidden');
    errorMsg.textContent = '';
}

document.getElementById('loginForm').addEventListener('submit', function(e) {
    const user = document.getElementById('username').value.trim();
    const pass = document.getElementById('password').value;

    let isValid = true;

    if (!user) {
        showError('username', '<?php echo __('auth.username_required') ?? 'กรุณากรอกชื่อผู้ใช้งาน' ?>');
        isValid = false;
    } else {
        clearError('username');
    }

    if (!pass) {
        showError('password', '<?php echo __('auth.password_required') ?? 'กรุณากรอกรหัสผ่าน' ?>');
        isValid = false;
    } else {
        clearError('password');
    }

    if (!isValid) {
        e.preventDefault();
        e.stopPropagation(); // Stop global loading state

        // Focus on first invalid field
        if (!user) document.getElementById('username').focus();
        else document.getElementById('password').focus();
    }
});
</script>
