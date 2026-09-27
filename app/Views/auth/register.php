<div class="bg-white dark:bg-[#1a2234] rounded-2xl shadow-xl border border-gray-100 dark:border-slate-800/60 overflow-hidden transition-colors w-full">
    <div class="p-3 md:p-6">
        <!-- Form Header -->
        <div class="mb-4">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white flex items-center">
                <i class="ph ph-user-plus text-blue-600 dark:text-blue-500 mr-2"></i>
                สมัครสมาชิก
            </h2>
            <p class="text-sm text-gray-500 dark:text-slate-400 mt-1">สมัครสมาชิกเพื่อเข้าใช้งาน <?php echo htmlspecialchars(\App\Core\Settings::get('site_name', 'WorkPOS')) ?></p>
        </div>

        <form action="/register" method="POST" id="registerForm" class="space-y-4" novalidate>
            <input type="hidden" name="csrf_token" value="<?php echo \App\Core\Session::getCsrfToken() ?>">

            <div class="space-y-1.5">
                <label for="username" class="block text-sm font-medium text-gray-700 dark:text-slate-300"><?php echo __('auth.username') ?? 'ชื่อผู้ใช้งาน' ?></label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400 dark:text-slate-500">
                        <i class="ph ph-user text-lg" id="username-icon"></i>
                    </div>
                    <input type="text" id="username" name="username" required autofocus autocomplete="username"
                        class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-300 dark:border-slate-700 bg-white dark:bg-[#0f172a] text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-colors"
                        placeholder="ชื่อผู้ใช้งาน"
                        oninput="clearError('username')">
                </div>
                <p id="username-error" class="hidden text-xs text-red-500 font-medium"></p>
            </div>

            <div class="space-y-1.5">
                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-slate-300"><?php echo __('auth.email') ?? 'อีเมล' ?></label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400 dark:text-slate-500">
                        <i class="ph ph-envelope-simple text-lg" id="email-icon"></i>
                    </div>
                    <input type="email" id="email" name="email" required autocomplete="email"
                        class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-300 dark:border-slate-700 bg-white dark:bg-[#0f172a] text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-colors"
                        placeholder="name@example.com"
                        oninput="clearError('email')">
                </div>
                <p id="email-error" class="hidden text-xs text-red-500 font-medium"></p>
            </div>

            <div class="space-y-1.5">
                <label for="password" class="block text-sm font-medium text-gray-700 dark:text-slate-300"><?php echo __('auth.password') ?? 'รหัสผ่าน' ?></label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400 dark:text-slate-500">
                        <i class="ph ph-lock-key text-lg" id="password-icon"></i>
                    </div>
                    <input type="password" id="password" name="password" required autocomplete="new-password"
                        class="w-full pl-10 pr-10 py-2.5 rounded-xl border border-gray-300 dark:border-slate-700 bg-white dark:bg-[#0f172a] text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-colors"
                        placeholder="••••••••"
                        oninput="clearError('password')">
                </div>
                <p id="password-error" class="hidden text-xs text-red-500 font-medium"></p>
            </div>

            <div class="space-y-1.5">
                <label for="confirm_password" class="block text-sm font-medium text-gray-700 dark:text-slate-300"><?php echo __('auth.confirm_password') ?? 'ยืนยันรหัสผ่าน' ?></label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400 dark:text-slate-500">
                        <i class="ph ph-lock-key text-lg" id="confirm_password-icon"></i>
                    </div>
                    <input type="password" id="confirm_password" name="confirm_password" required autocomplete="new-password"
                        class="w-full pl-10 pr-10 py-2.5 rounded-xl border border-gray-300 dark:border-slate-700 bg-white dark:bg-[#0f172a] text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-colors"
                        placeholder="••••••••"
                        oninput="clearError('confirm_password')">
                </div>
                <p id="confirm_password-error" class="hidden text-xs text-red-500 font-medium"></p>
            </div>

            <div class="pt-2">
                <button type="submit" class="w-full flex justify-center items-center px-4 py-2 rounded-xl shadow-lg shadow-blue-500/30 dark:shadow-blue-900/20 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all">
                    <i class="ph ph-user-plus mr-2 text-lg"></i> สมัครสมาชิก
                </button>
            </div>

            <div class="text-center pt-4">
                <p class="text-sm text-gray-600 dark:text-slate-400">
                    มีบัญชีอยู่แล้ว? <a href="/login" class="font-semibold text-blue-600 dark:text-blue-400 hover:underline transition-colors">เข้าสู่ระบบ</a>
                </p>
            </div>
        </form>
    </div>
</div>

<script>
function showError(fieldId, message) {
    const input = document.getElementById(fieldId);
    const errorMsg = document.getElementById(fieldId + '-error');
    const icon = document.getElementById(fieldId + '-icon');

    input.classList.remove('border-gray-300', 'dark:border-slate-700', 'focus:border-blue-500', 'focus:ring-blue-500');
    input.classList.add('border-red-500', 'dark:border-red-500', 'focus:border-red-500', 'focus:ring-red-500');

    if(icon) {
        icon.parentElement.classList.remove('text-gray-400', 'dark:text-slate-500');
        icon.parentElement.classList.add('text-red-500');
    }

    errorMsg.textContent = message;
    errorMsg.classList.remove('hidden');

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

    if(icon) {
        icon.parentElement.classList.add('text-gray-400', 'dark:text-slate-500');
        icon.parentElement.classList.remove('text-red-500');
    }

    errorMsg.classList.add('hidden');
    errorMsg.textContent = '';
}

document.getElementById('registerForm').addEventListener('submit', function(e) {
    const user = document.getElementById('username').value.trim();
    const email = document.getElementById('email').value.trim();
    const pass = document.getElementById('password').value;
    const confirmPass = document.getElementById('confirm_password').value;

    let isValid = true;

    if (!user) {
        showError('username', 'กรุณากรอกชื่อผู้ใช้งาน');
        isValid = false;
    } else {
        clearError('username');
    }

    if (!email) {
        showError('email', 'กรุณากรอกอีเมล');
        isValid = false;
    } else {
        clearError('email');
    }

    if (!pass) {
        showError('password', 'กรุณากรอกรหัสผ่าน');
        isValid = false;
    } else {
        clearError('password');
    }

    if (!confirmPass) {
        showError('confirm_password', 'กรุณายืนยันรหัสผ่าน');
        isValid = false;
    } else if (pass !== confirmPass) {
        showError('confirm_password', 'รหัสผ่านไม่ตรงกัน');
        isValid = false;
    } else {
        clearError('confirm_password');
    }

    if (!isValid) {
        e.preventDefault();
        e.stopPropagation();
    }
});
</script>
