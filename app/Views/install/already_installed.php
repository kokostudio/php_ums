<!DOCTYPE html>
<html lang="th" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ระบบถูกติดตั้งแล้ว | PHP UMS - Standard Core System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="/css/sweetalert2-theme.css">
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: { sans: ['Prompt', 'sans-serif'] },
                    colors: {
                        primary: {
                            50: '#eff6ff', 100: '#dbeafe', 200: '#bfdbfe', 300: '#93c5fd',
                            400: '#60a5fa', 500: '#3b82f6', 600: '#2563eb', 700: '#1d4ed8',
                            800: '#1e40af', 900: '#1e3a8a',
                        },
                        sidebar: '#0f172a'
                    }
                }
            }
        }
    </script>
    <style>body { font-family: 'Prompt', sans-serif; }</style>
    <script>
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }
        function toggleTheme() {
            if (document.documentElement.classList.contains('dark')) {
                document.documentElement.classList.remove('dark');
                localStorage.theme = 'light';
            } else {
                document.documentElement.classList.add('dark');
                localStorage.theme = 'dark';
            }
        }
    </script>
</head>
<body class="bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-100 min-h-full flex flex-col justify-between antialiased transition-colors duration-200">

    <nav class="bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 transition-colors">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center">
                <div class="w-9 h-9 rounded-lg bg-blue-600 flex items-center justify-center mr-3 shadow-sm text-white">
                    <i class="ph ph-cube text-xl"></i>
                </div>
                <span class="font-semibold text-slate-800 dark:text-slate-100">PHP UMS - Standard Core System</span>
            </div>
            <button onclick="toggleTheme()" class="w-9 h-9 flex items-center justify-center rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors text-slate-500 dark:text-slate-400">
                <i class="ph ph-sun text-xl hidden dark:block"></i>
                <i class="ph ph-moon text-xl dark:hidden"></i>
            </button>
        </div>
    </nav>

    <main class="max-w-4xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-12 flex-1">
        <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 p-6 md:p-10 text-center">
            <div class="w-16 h-16 rounded-2xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center mx-auto mb-5 text-blue-600 dark:text-blue-400">
                <i class="ph ph-seal-check text-3xl"></i>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">ระบบถูกติดตั้งเรียบร้อยแล้ว</h1>
            <p class="text-slate-500 dark:text-slate-400 mt-2">ติดตั้งเมื่อ: <?= htmlspecialchars($installedAt ?? '-') ?></p>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-8 text-left">
                <div class="p-4 rounded-xl border border-slate-200 dark:border-slate-800">
                    <div class="flex items-center gap-2 mb-1.5">
                        <i class="ph <?= ($hasSuperAdmin ?? false) ? 'ph-check-circle text-emerald-500' : 'ph-warning text-amber-500' ?>"></i>
                        <span class="text-sm font-medium text-slate-800 dark:text-slate-100">บัญชี Super Admin</span>
                    </div>
                    <p class="text-xs text-slate-500 dark:text-slate-400"><?= ($hasSuperAdmin ?? false) ? 'พร้อมใช้งาน' : 'ยังไม่พบบัญชี Super Admin ในระบบ' ?></p>
                </div>

                <div class="p-4 rounded-xl border border-slate-200 dark:border-slate-800">
                    <div class="flex items-center gap-2 mb-1.5">
                        <i class="ph <?= empty($pendingMigrations) ? 'ph-check-circle text-emerald-500' : 'ph-arrow-circle-up text-blue-500' ?>"></i>
                        <span class="text-sm font-medium text-slate-800 dark:text-slate-100">สถานะฐานข้อมูล</span>
                    </div>
                    <p class="text-xs text-slate-500 dark:text-slate-400">
                        <?= empty($pendingMigrations) ? 'ฐานข้อมูลเป็นเวอร์ชันล่าสุดแล้ว' : 'มี ' . count($pendingMigrations) . ' การเปลี่ยนแปลงที่ยังไม่ได้อัปเกรด' ?>
                    </p>
                </div>
            </div>

            <?php if (!empty($pendingMigrations)): ?>
            <div class="mt-8 p-6 rounded-2xl bg-blue-50 dark:bg-blue-950/30 border border-blue-200 dark:border-blue-900/50 text-left">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-100 dark:bg-blue-900/50 flex items-center justify-center shrink-0 text-blue-600 dark:text-blue-400">
                        <i class="ph ph-arrow-circle-up text-2xl"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-base font-semibold text-slate-900 dark:text-white">มีไฟล์ฐานข้อมูลชุดใหม่รอการอัปเกรด (<?= count($pendingMigrations) ?> ไฟล์)</h3>
                        <p class="text-xs text-slate-600 dark:text-slate-400 mt-1">สามารถกดอัปเกรดฐานข้อมูลผ่านหน้าเว็บได้ทันทีโดยไม่สูญเสียข้อมูลเดิม</p>

                        <div class="mt-3 max-h-36 overflow-y-auto bg-white dark:bg-slate-900 rounded-xl p-3 border border-blue-100 dark:border-blue-900/30 text-xs font-mono space-y-1">
                            <?php foreach ($pendingMigrations as $pm): ?>
                            <div class="text-slate-600 dark:text-slate-400 flex items-center gap-1.5">
                                <i class="ph ph-file text-blue-500"></i><?= htmlspecialchars($pm) ?>
                            </div>
                            <?php endforeach; ?>
                        </div>

                        <?php if (!($isSuperAdmin ?? false)): ?>
                        <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <input type="text" id="upgrade_admin_username" placeholder="Username หรือ Email Super Admin" class="px-3.5 py-2 rounded-xl text-xs bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <input type="password" id="upgrade_admin_password" placeholder="รหัสผ่าน Super Admin" class="px-3.5 py-2 rounded-xl text-xs bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <?php endif; ?>

                        <div class="mt-4 flex items-center justify-end">
                            <button type="button" id="btn-run-upgrade" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl text-xs font-medium transition-colors shadow-sm flex items-center gap-2">
                                <i class="ph ph-play"></i>อัปเกรดฐานข้อมูลเดี๋ยวนี้
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <div class="mt-8 flex items-center justify-center gap-3">
                <a href="/login" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl text-sm font-medium transition-colors shadow-sm inline-flex items-center">
                    <i class="ph ph-sign-in mr-2 text-lg"></i>ไปที่หน้าเข้าสู่ระบบ
                </a>
                <a href="/dashboard" class="bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 px-6 py-2.5 rounded-xl text-sm font-medium transition-colors inline-flex items-center">
                    <i class="ph ph-gauge mr-2 text-lg"></i>ไปที่แดชบอร์ด
                </a>
            </div>
        </div>
    </main>

    <footer class="py-6 text-center text-xs text-slate-400 dark:text-slate-500">
        PHP UMS - Standard Core System &copy; <?= date('Y') ?> — Web Installer
    </footer>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var btnUpgrade = document.getElementById('btn-run-upgrade');
        if (btnUpgrade) {
            btnUpgrade.addEventListener('click', function() {
                var btn = this;
                var usernameEl = document.getElementById('upgrade_admin_username');
                var passwordEl = document.getElementById('upgrade_admin_password');

                var payload = {};
                if (usernameEl && passwordEl) {
                    if (!usernameEl.value || !passwordEl.value) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'กรุณากรอกข้อมูล',
                            text: 'กรุณาระบุ Username และ Password ของ Super Admin เพื่อยืนยันสิทธิ์'
                        });
                        return;
                    }
                    payload.admin_username = usernameEl.value;
                    payload.admin_password = passwordEl.value;
                }

                btn.disabled = true;
                btn.innerHTML = '<i class="ph ph-circle-notch animate-spin"></i> กำลังอัปเกรด...';

                fetch('/install/upgrade', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                })
                .then(function(r) { return r.json(); })
                .then(function(res) {
                    if (res && res.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'อัปเกรดสำเร็จ!',
                            text: res.message,
                            confirmButtonText: 'ตกลง'
                        }).then(function() {
                            window.location.reload();
                        });
                    } else {
                        btn.disabled = false;
                        btn.innerHTML = '<i class="ph ph-play"></i> อัปเกรดฐานข้อมูลเดี๋ยวนี้';
                        Swal.fire({
                            icon: 'error',
                            title: 'การอัปเกรดล้มเหลว',
                            text: res ? res.message : 'ไม่สามารถอัปเกรดได้'
                        });
                    }
                })
                .catch(function(err) {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="ph ph-play"></i> อัปเกรดฐานข้อมูลเดี๋ยวนี้';
                    Swal.fire({
                        icon: 'error',
                        title: 'เกิดข้อผิดพลาด',
                        text: (err && err.message) ? err.message : 'ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้'
                    });
                });
            });
        }
    });
    </script>
</body>
</html>
