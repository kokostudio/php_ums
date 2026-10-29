<!DOCTYPE html>
<html lang="th" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ติดตั้งสำเร็จ | PHP UMS - Standard Core System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <script src="https://cdn.tailwindcss.com"></script>
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
    </script>
</head>
<body class="bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-100 min-h-full flex flex-col justify-between antialiased transition-colors duration-200">

    <nav class="bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 transition-colors">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center">
            <div class="flex items-center">
                <div class="w-9 h-9 rounded-lg bg-blue-600 flex items-center justify-center mr-3 shadow-sm text-white">
                    <i class="ph ph-cube text-xl"></i>
                </div>
                <span class="font-semibold text-slate-800 dark:text-slate-100">PHP UMS - Standard Core System</span>
            </div>
        </div>
    </nav>

    <main class="max-w-4xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-12 flex-1">
        <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 p-6 md:p-10 text-center">
            <div class="w-20 h-20 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center mx-auto mb-6 text-blue-600 dark:text-blue-400">
                <i class="ph ph-confetti text-4xl"></i>
            </div>
            <h1 class="text-3xl font-bold text-slate-900 dark:text-white">ติดตั้งระบบสำเร็จ!</h1>
            <p class="text-slate-500 dark:text-slate-400 mt-2">ระบบของคุณพร้อมใช้งานแล้ว</p>

            <div class="max-w-md mx-auto mt-8 text-left space-y-3">
                <div class="p-4 rounded-xl border border-slate-200 dark:border-slate-800 flex items-center justify-between">
                    <span class="text-sm text-slate-500 dark:text-slate-400">ชื่อระบบ</span>
                    <span class="text-sm font-semibold text-slate-800 dark:text-slate-100"><?= htmlspecialchars($summary['app_name'] ?? '') ?></span>
                </div>
                <div class="p-4 rounded-xl border border-slate-200 dark:border-slate-800 flex items-center justify-between">
                    <span class="text-sm text-slate-500 dark:text-slate-400">URL ของระบบ</span>
                    <span class="text-sm font-mono font-medium text-slate-800 dark:text-slate-100"><?= htmlspecialchars($summary['app_url'] ?? '') ?></span>
                </div>
                <div class="p-4 rounded-xl border border-slate-200 dark:border-slate-800 flex items-center justify-between">
                    <span class="text-sm text-slate-500 dark:text-slate-400">บัญชีผู้ดูแลระบบ</span>
                    <span class="text-sm font-semibold text-slate-800 dark:text-slate-100"><?= htmlspecialchars($summary['admin_username'] ?? '') ?></span>
                </div>
            </div>

            <div class="p-4 rounded-xl bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 max-w-md mx-auto mt-6 text-left flex items-start gap-2.5">
                <i class="ph ph-lock-key text-amber-500 text-lg mt-0.5 shrink-0"></i>
                <p class="text-xs text-amber-700 dark:text-amber-400"><strong>เพื่อความปลอดภัย:</strong> ลบไฟล์ <span class="font-mono">install.php</span> ที่ root และ <span class="font-mono">public/install.php</span> ออกได้หลังการติดตั้ง — ตัวติดตั้งจะถูกปิดการทำงานโดยไฟล์ล็อกอยู่แล้ว</p>
            </div>

            <div class="mt-8">
                <a href="/login" class="inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-xl text-sm font-medium transition-colors shadow-md">
                    <i class="ph ph-sign-in mr-2 text-lg"></i>เข้าสู่ระบบเลย
                </a>
            </div>
        </div>
    </main>

    <footer class="py-6 text-center text-xs text-slate-400 dark:text-slate-500">
        PHP UMS - Standard Core System &copy; <?= date('Y') ?> — Web Installer
    </footer>
</body>
</html>
