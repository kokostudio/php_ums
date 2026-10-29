<!DOCTYPE html>
<html lang="th" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ตัวช่วยติดตั้งระบบ | PHP UMS - Standard Core System</title>
    <!-- Google Fonts: Prompt -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400&display=swap" rel="stylesheet">
    <!-- Phosphor Icons -->
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="/css/sweetalert2-theme.css">
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Prompt', 'sans-serif'],
                    },
                    colors: {
                        primary: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            200: '#bfdbfe',
                            300: '#93c5fd',
                            400: '#60a5fa',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            800: '#1e40af',
                            900: '#1e3a8a',
                        },
                        sidebar: '#0f172a'
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Prompt', sans-serif; }
        .step-transition { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #3b82f680; border-radius: 9999px; }
        ::-webkit-scrollbar-thumb:hover { background: #2563eb; }
    </style>
    <script>
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
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

    <!-- Top Navigation -->
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

    <main class="max-w-4xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-10 flex-1">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-slate-900 dark:text-white">ตัวช่วยติดตั้งระบบ</h1>
            <p class="text-slate-500 dark:text-slate-400 mt-2">ติดตั้งระบบจัดการผู้ใช้งานและสิทธิ์ (PHP UMS Core) บนเซิร์ฟเวอร์ของคุณ</p>
        </div>

        <!-- Step Indicator -->
        <div class="flex items-center justify-center mb-10">
            <div class="flex items-center" id="step-indicator">
                <div class="flex items-center step-dot" data-step="1">
                    <div class="w-10 h-10 rounded-full bg-blue-600 text-white flex items-center justify-center text-sm font-semibold shadow-md relative z-10">1</div>
                    <span class="ml-2 text-sm font-medium text-blue-600 dark:text-blue-400 hidden sm:block">ตรวจสภาพแวดล้อม</span>
                </div>
                <div class="w-16 sm:w-24 h-0.5 bg-slate-300 dark:bg-slate-700 mx-1"></div>
                <div class="flex items-center step-dot" data-step="2">
                    <div class="w-10 h-10 rounded-full bg-slate-200 dark:bg-slate-700 text-slate-500 dark:text-slate-400 flex items-center justify-center text-sm font-semibold relative z-10">2</div>
                    <span class="ml-2 text-sm font-medium text-slate-500 dark:text-slate-400 hidden sm:block">ฐานข้อมูล</span>
                </div>
                <div class="w-16 sm:w-24 h-0.5 bg-slate-300 dark:bg-slate-700 mx-1"></div>
                <div class="flex items-center step-dot" data-step="3">
                    <div class="w-10 h-10 rounded-full bg-slate-200 dark:bg-slate-700 text-slate-500 dark:text-slate-400 flex items-center justify-center text-sm font-semibold relative z-10">3</div>
                    <span class="ml-2 text-sm font-medium text-slate-500 dark:text-slate-400 hidden sm:block">ผู้ดูแลระบบ</span>
                </div>
            </div>
        </div>

        <!-- ===== Step 1: Requirements ===== -->
        <div id="stepContent-1" class="step-transition bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 p-6 md:p-8">
            <h2 class="text-lg font-semibold text-slate-900 dark:text-white mb-6 flex items-center"><i class="ph ph-list-checks mr-2 text-blue-500"></i>ตรวจสอบความพร้อมของระบบ</h2>

            <div class="space-y-3 mb-8">
                <div class="flex items-center justify-between p-3.5 rounded-xl border <?= $phpPass ? 'border-emerald-200 bg-emerald-50 dark:border-emerald-800 dark:bg-emerald-900/20' : 'border-red-200 bg-red-50 dark:border-red-800 dark:bg-red-900/20' ?>">
                    <div class="flex items-center"><i class="ph <?= $phpPass ? 'ph-check-circle text-emerald-500' : 'ph-x-circle text-red-500' ?> text-xl mr-2"></i><span class="text-sm">เวอร์ชัน PHP (ต้องการ 8.2 ขึ้นไป แนะนำ 8.4)</span></div>
                    <span class="text-sm font-mono font-medium <?= $phpPass ? 'text-emerald-700 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' ?>"><?= $phpVersion ?></span>
                </div>

                <?php foreach ($extensionStatus as $ext => $info): ?>
                <div class="flex items-center justify-between p-3.5 rounded-xl border <?= $info['pass'] ? 'border-emerald-200 bg-emerald-50 dark:border-emerald-800 dark:bg-emerald-900/20' : 'border-red-200 bg-red-50 dark:border-red-800 dark:bg-red-900/20' ?>">
                    <div class="flex items-center"><i class="ph <?= $info['pass'] ? 'ph-check-circle text-emerald-500' : 'ph-x-circle text-red-500' ?> text-xl mr-2"></i><span class="text-sm"><?= $info['label'] ?></span></div>
                    <span class="text-xs font-medium <?= $info['pass'] ? 'text-emerald-700 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' ?>"><?= $info['pass'] ? 'พร้อมใช้' : 'ไม่พบ Extension' ?></span>
                </div>
                <?php endforeach; ?>

                <?php foreach ($dirStatus as $name => $info): ?>
                <div class="flex items-center justify-between p-3.5 rounded-xl border <?= $info['pass'] ? 'border-emerald-200 bg-emerald-50 dark:border-emerald-800 dark:bg-emerald-900/20' : 'border-red-200 bg-red-50 dark:border-red-800 dark:bg-red-900/20' ?>">
                    <div class="flex items-center"><i class="ph <?= $info['pass'] ? 'ph-check-circle text-emerald-500' : 'ph-x-circle text-red-500' ?> text-xl mr-2"></i><span class="text-sm font-mono"><?= $info['path'] ?></span></div>
                    <span class="text-xs font-medium <?= $info['pass'] ? 'text-emerald-700 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' ?>"><?= $info['pass'] ? 'เขียนได้' : 'ไม่สามารถเขียนได้ (แก้สิทธิ์เป็น 755 หรือ 775)' ?></span>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="flex justify-end">
                <button type="button" id="btn-to-step-2" <?= $allPassed ? '' : 'disabled' ?> class="bg-blue-600 hover:bg-blue-700 <?= $allPassed ? '' : 'opacity-50 cursor-not-allowed' ?> disabled:cursor-not-allowed text-white px-6 py-2.5 rounded-xl text-sm font-medium transition-colors shadow-sm flex items-center">
                    ดำเนินการต่อ<i class="ph ph-arrow-right ml-2 text-lg"></i>
                </button>
            </div>
        </div>

        <!-- ===== Step 2: Database ===== -->
        <div id="stepContent-2" class="step-transition bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 p-6 md:p-8 hidden">
            <h2 class="text-lg font-semibold text-slate-900 dark:text-white mb-6 flex items-center"><i class="ph ph-database mr-2 text-blue-500"></i>ตั้งค่าฐานข้อมูล</h2>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">ชื่อ Host</label>
                    <input type="text" id="db_host" value="<?= htmlspecialchars($defaultHost) ?>" class="w-full px-4 py-2.5 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-colors" placeholder="127.0.0.1">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Port</label>
                    <input type="number" id="db_port" value="<?= htmlspecialchars($defaultPort) ?>" class="w-full px-4 py-2.5 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-colors" placeholder="3306">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">ชื่อฐานข้อมูล (เช่น php_ums)</label>
                    <input type="text" id="db_name" value="<?= htmlspecialchars($defaultDb) ?>" class="w-full px-4 py-2.5 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-colors" placeholder="php_ums">
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">ชื่อผู้ใช้ฐานข้อมูล</label>
                        <input type="text" id="db_user" value="<?= htmlspecialchars($defaultUser) ?>" class="w-full px-4 py-2.5 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-colors" placeholder="root">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">รหัสผ่านฐานข้อมูล</label>
                        <input type="password" id="db_pass" class="w-full px-4 py-2.5 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-colors" placeholder="">
                    </div>
                </div>
            </div>

            <div class="flex justify-between mt-8">
                <button type="button" class="back-to-1 px-5 py-2.5 rounded-xl text-sm font-medium text-slate-600 dark:text-slate-300 border border-slate-300 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors flex items-center"><i class="ph ph-arrow-left mr-2 text-lg"></i>ย้อนกลับ</button>
                <div class="flex items-center gap-3">
                    <button type="button" id="btn-test-db" class="px-5 py-2.5 rounded-xl text-sm font-medium text-blue-700 dark:text-blue-400 border border-blue-300 dark:border-blue-800 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors flex items-center"><i class="ph ph-plugs mr-2 text-lg"></i>ทดสอบเชื่อมต่อ</button>
                    <button type="button" id="btn-to-step-3" disabled class="bg-blue-600 hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed text-white px-6 py-2.5 rounded-xl text-sm font-medium transition-colors shadow-sm flex items-center">ดำเนินการต่อ<i class="ph ph-arrow-right ml-2 text-lg"></i></button>
                </div>
            </div>
        </div>

        <!-- ===== Step 3: Admin Account ===== -->
        <div id="stepContent-3" class="step-transition bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 p-6 md:p-8 hidden">
            <h2 class="text-lg font-semibold text-slate-900 dark:text-white mb-6 flex items-center"><i class="ph ph-shield-user mr-2 text-blue-500"></i>สร้างบัญชีผู้ดูแลระบบสูงสุด</h2>

            <div class="space-y-4 mb-6">
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">ชื่อระบบ (แสดงบนหน้าเว็บ)</label>
                    <input type="text" id="app_name" value="PHP UMS - Standard Core System" class="w-full px-4 py-2.5 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-colors">
                </div>
                <div>
                    <input type="hidden" id="app_url" value="<?= htmlspecialchars($defaultUrl) ?>">
                    <input type="hidden" id="app_env" value="production">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">ชื่อผู้ใช้ของ Super Admin</label>
                    <input type="text" id="admin_username" value="superadmin" class="w-full px-4 py-2.5 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-colors">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">อีเมลของ Super Admin</label>
                    <input type="email" id="admin_email" value="admin@example.com" class="w-full px-4 py-2.5 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-colors">
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">รหัสผ่าน (6 ตัวอักษรขึ้นไป)</label>
                        <input type="password" id="admin_password" class="w-full px-4 py-2.5 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-colors">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">ยืนยันรหัสผ่าน</label>
                        <input type="password" id="admin_password_confirmation" class="w-full px-4 py-2.5 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-colors">
                    </div>
                </div>

                <div class="p-4 rounded-xl bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 flex items-start gap-2.5">
                    <i class="ph ph-warning text-amber-500 text-lg mt-0.5 shrink-0"></i>
                    <p class="text-xs text-amber-700 dark:text-amber-400">เมื่อติดตั้งเสร็จ ระบบจะสร้างไฟล์ล็อก (<span class="font-mono">storage/installed.lock</span>) เพื่อป้องกันการติดตั้งซ้ำ</p>
                </div>
            </div>

            <div class="flex justify-between">
                <button type="button" class="back-to-2 px-5 py-2.5 rounded-xl text-sm font-medium text-slate-600 dark:text-slate-300 border border-slate-300 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors flex items-center"><i class="ph ph-arrow-left mr-2 text-lg"></i>ย้อนกลับ</button>
                <button type="button" id="btn-install" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl text-sm font-medium transition-colors shadow-sm flex items-center"><i class="ph ph-rocket-launch mr-2 text-lg"></i>ติดตั้งระบบ</button>
            </div>
        </div>
    </main>

    <footer class="py-6 text-center text-xs text-slate-400 dark:text-slate-500">
        PHP UMS - Standard Core System &copy; <?= date('Y') ?> — Web Installer
    </footer>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        function setActiveStep(n) {
            document.querySelectorAll('.step-dot').forEach(function(dot) {
                var idx = parseInt(dot.dataset.step);
                var circle = dot.querySelector('div');
                var label = dot.querySelector('span');
                if (idx < n || idx === n) {
                    circle.className = 'w-10 h-10 rounded-full bg-blue-600 text-white flex items-center justify-center text-sm font-semibold shadow-md relative z-10';
                    label.className = 'ml-2 text-sm font-medium text-blue-600 dark:text-blue-400 hidden sm:block';
                } else {
                    circle.className = 'w-10 h-10 rounded-full bg-slate-200 dark:bg-slate-700 text-slate-500 dark:text-slate-400 flex items-center justify-center text-sm font-semibold relative z-10';
                    label.className = 'ml-2 text-sm font-medium text-slate-500 dark:text-slate-400 hidden sm:block';
                }
            });
            [1, 2, 3].forEach(function(i) {
                var el = document.getElementById('stepContent-' + i);
                if (el) {
                    el.classList.toggle('hidden', i !== n);
                }
            });
        }

        var btnToStep2 = document.getElementById('btn-to-step-2');
        if (btnToStep2) {
            btnToStep2.addEventListener('click', function() { setActiveStep(2); });
        }

        document.querySelectorAll('.back-to-1').forEach(function(btn) {
            btn.addEventListener('click', function() { setActiveStep(1); });
        });

        var btnToStep3 = document.getElementById('btn-to-step-3');
        if (btnToStep3) {
            btnToStep3.addEventListener('click', function() { setActiveStep(3); });
        }

        document.querySelectorAll('.back-to-2').forEach(function(btn) {
            btn.addEventListener('click', function() { setActiveStep(2); });
        });

        var btnTestDb = document.getElementById('btn-test-db');
        if (btnTestDb) {
            btnTestDb.addEventListener('click', function() {
                var btn = this;
                btn.disabled = true;
                btn.innerHTML = '<i class="ph ph-circle-notch animate-spin mr-2 text-lg"></i>กำลังทดสอบ...';

                fetch('/install/test-db', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        host: document.getElementById('db_host').value,
                        port: document.getElementById('db_port').value,
                        database: document.getElementById('db_name').value,
                        username: document.getElementById('db_user').value,
                        password: document.getElementById('db_pass').value
                    })
                })
                .then(function(r) { return r.json(); })
                .then(function(res) {
                    if (res.success) {
                        var nextBtn = document.getElementById('btn-to-step-3');
                        if (nextBtn) {
                            nextBtn.disabled = false;
                        }
                        Swal.fire({ toast: true, position: 'top', showConfirmButton: false, timer: 3000, timerProgressBar: true, icon: 'success', title: res.message });
                    } else {
                        Swal.fire({ icon: 'error', title: 'การเชื่อมต่อล้มเหลว', text: res.message });
                    }
                })
                .catch(function() {
                    Swal.fire({ icon: 'error', title: 'เกิดข้อผิดพลาด', text: 'ไม่สามารถติดต่อเซิร์ฟเวอร์ได้' });
                })
                .finally(function() {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="ph ph-plugs mr-2 text-lg"></i>ทดสอบเชื่อมต่อ';
                });
            });
        }

        var btnInstall = document.getElementById('btn-install');
        if (btnInstall) {
            btnInstall.addEventListener('click', function() {
                var pass1 = document.getElementById('admin_password').value;
                var pass2 = document.getElementById('admin_password_confirmation').value;

                if (pass1.length < 6) {
                    Swal.fire({ icon: 'error', title: 'รหัสผ่านสั้นเกินไป', text: 'รหัสผ่านต้องมีความยาวอย่างน้อย 6 ตัวอักษร' });
                    return;
                }
                if (pass1 !== pass2) {
                    Swal.fire({ icon: 'error', title: 'รหัสผ่านไม่ตรงกัน', text: 'กรุณาตรวจสอบการยืนยันรหัสผ่านอีกครั้ง' });
                    return;
                }

                var btn = this;
                btn.disabled = true;
                btn.innerHTML = '<i class="ph ph-circle-notch animate-spin mr-2 text-lg"></i>กำลังติดตั้ง... อย่าปิดหน้านี้';

                fetch('/install/process', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        db_host: document.getElementById('db_host').value,
                        db_port: document.getElementById('db_port').value,
                        db_name: document.getElementById('db_name').value,
                        db_user: document.getElementById('db_user').value,
                        db_pass: document.getElementById('db_pass').value,
                        app_name: document.getElementById('app_name').value,
                        app_url: document.getElementById('app_url').value,
                        app_env: document.getElementById('app_env').value,
                        admin_username: document.getElementById('admin_username').value,
                        admin_email: document.getElementById('admin_email').value,
                        admin_password: pass1,
                        admin_password_confirmation: pass2
                    })
                })
                .then(async function(r) {
                    const text = await r.text();
                    let res;
                    try {
                        res = JSON.parse(text);
                    } catch (e) {
                        throw new Error(text ? text.replace(/<[^>]*>?/gm, ' ').substring(0, 300) : 'Invalid server response');
                    }
                    if (!r.ok && !res.message) {
                        res.message = 'HTTP error ' + r.status;
                    }
                    return res;
                })
                .then(function(res) {
                    if (res && res.success) {
                        window.location.href = res.redirect || '/install/complete';
                    } else {
                        btn.disabled = false;
                        btn.innerHTML = '<i class="ph ph-rocket-launch mr-2 text-lg"></i>ติดตั้งระบบ';
                        Swal.fire({ icon: 'error', title: 'การติดตั้งล้มเหลว', text: res ? res.message : 'ไม่สามารถติดตั้งระบบได้' });
                    }
                })
                .catch(function(err) {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="ph ph-rocket-launch mr-2 text-lg"></i>ติดตั้งระบบ';
                    Swal.fire({ icon: 'error', title: 'เกิดข้อผิดพลาด', text: (err && err.message) ? err.message : 'ไม่สามารถติดต่อเซิร์ฟเวอร์ได้' });
                });
            });
        }

        setActiveStep(1);
    });
    </script>
</body>
</html>
