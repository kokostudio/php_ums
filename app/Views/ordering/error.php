<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ไม่พบข้อมูลโต๊ะอาหาร</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <style>body { font-family: 'Prompt', sans-serif; }</style>
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl p-8 max-w-sm w-full text-center shadow-xl border border-slate-100">
        <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-rose-50 text-rose-500 flex items-center justify-center text-3xl">
            <i class="ph ph-warning-circle"></i>
        </div>
        <h1 class="text-lg font-bold text-slate-800">ไม่สามารถเข้าถึงโต๊ะอาหารได้</h1>
        <p class="text-xs text-slate-500 mt-2 leading-relaxed">
            <?= htmlspecialchars($message ?? 'ไม่พบข้อมูลโต๊ะ หรือ QR Code นี้หมดอายุแล้ว กรุณาสแกน QR Code ประจำโต๊ะอาหารใหม่อีกครั้ง') ?>
        </p>
        <p class="text-[11px] text-slate-400 mt-4 pt-4 border-t border-slate-100">
            หากพบปัญหา โปรดติดต่อพนักงานที่ให้บริการในร้าน
        </p>
    </div>
</body>
</html>
