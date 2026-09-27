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
                $googleEnabled = \App\Core\Settings::get('enable_google_login') === '1';
                $lineEnabled = \App\Core\Settings::get('enable_line_login') === '1';
                if ($googleEnabled || $lineEnabled): 
            ?>
            <!-- Social Login Divider -->
            <div class="relative py-2">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-300 dark:border-slate-700"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-2 bg-white dark:bg-[#1a2234] text-gray-500 dark:text-slate-400">หรือเข้าสู่ระบบด้วย</span>
                </div>
            </div>

            <!-- Social Login Buttons -->
            <div class="grid <?= ($googleEnabled && $lineEnabled) ? 'grid-cols-2' : 'grid-cols-1' ?> gap-3">
                <?php if ($googleEnabled): ?>
                <a href="/auth/google" class="w-full flex justify-center items-center px-4 py-2.5 rounded-xl border border-gray-300 dark:border-slate-700 bg-white dark:bg-[#0f172a] text-sm font-medium text-gray-700 dark:text-slate-300 hover:bg-gray-50 dark:hover:bg-slate-800 transition-colors">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/5/53/Google_%22G%22_Logo.svg" alt="Google" class="w-5 h-5 mr-2">
                    Google
                </a>
                <?php endif; ?>
                
                <?php if ($lineEnabled): ?>
                <a href="/auth/line" class="w-full flex justify-center items-center px-4 py-2.5 rounded-xl border border-[#00B900] bg-[#00B900] text-sm font-medium text-white hover:bg-[#009900] transition-colors shadow-sm shadow-[#00B900]/20">
                    <svg class="w-5 h-5 mr-2" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                        <path d="M24 10.304c0-5.369-5.383-9.738-12-9.738-6.616 0-12 4.369-12 9.738 0 4.814 3.963 8.89 9.382 9.615.366.079.866.242 1.002.553.121.282.079.722.037 1.011-.044.29-.205 1.229-.247 1.489-.053.318-.242.977.854.514 1.096-.461 5.922-3.488 8.169-6.028 2.062-2.316 2.803-4.706 2.803-7.154zm-14.717 3.393h-2.613c-.157 0-.285-.129-.285-.285v-5.263c0-.157.128-.285.285-.285.158 0 .285.128.285.285v4.978h2.328c.157 0 .285.128.285.285s-.128.285-.285.285zm3.433 0h-.569c-.157 0-.285-.129-.285-.285v-5.263c0-.157.128-.285.285-.285.158 0 .285.128.285.285v5.263c0 .156-.127.285-.285.285zm4.846-5.548v5.263c0 .156-.128.285-.285.285h-.569c-.046 0-.091-.011-.131-.031-.038-.022-.07-.052-.093-.089l-2.607-3.791v3.626c0 .156-.128.285-.285.285h-.569c-.157 0-.285-.129-.285-.285v-5.263c0-.157.128-.285.285-.285h.569c.046 0 .092.012.131.032.038.021.07.051.093.088l2.607 3.792v-3.627c0-.157.128-.285.285-.285h.569c.158 0 .285.128.285.285z"/>
                    </svg>
                    LINE
                </a>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <?php if (\App\Core\Settings::get('enable_registration') !== '0'): ?>
            <div class="text-center pt-4">
                <p class="text-sm text-gray-600 dark:text-slate-400">
                    ยังไม่มีบัญชี? <a href="/register" class="font-semibold text-blue-600 dark:text-blue-400 hover:underline transition-colors">สมัครสมาชิก</a>
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
