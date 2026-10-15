<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code - โต๊ะ <?= htmlspecialchars($table['table_number']) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <style>
        body { font-family: 'Prompt', sans-serif; }
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; padding: 0 !important; }
            .print-card { box-shadow: none !important; border: 2px dashed #cbd5e1 !important; }
        }
    </style>
</head>
<body class="bg-slate-100 min-h-screen flex flex-col items-center justify-center p-6">

    <!-- Print Control Buttons -->
    <div class="no-print mb-6 flex items-center space-x-3">
        <button onclick="window.print()" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl shadow-md font-medium flex items-center space-x-2 transition-all">
            <i class="ph ph-printer text-xl"></i>
            <span>พิมพ์การ์ด QR Code</span>
        </button>
        <button onclick="window.close()" class="px-5 py-2.5 bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-xl font-medium transition-all">
            <span>ปิดหน้านี้</span>
        </button>
    </div>

    <!-- Printable Table Tent Card -->
    <div class="print-card bg-white w-full max-w-sm rounded-3xl shadow-xl border border-slate-200 p-8 text-center flex flex-col items-center">
        <!-- Restaurant Header -->
        <div class="w-12 h-12 bg-amber-500/10 text-amber-600 rounded-2xl flex items-center justify-center mb-3">
            <i class="ph ph-fork-knife text-2xl font-bold"></i>
        </div>
        <h2 class="text-xs uppercase tracking-widest text-slate-400 font-bold mb-1">ยินดีต้อนรับ</h2>
        <h1 class="text-3xl font-extrabold text-slate-800 tracking-tight">โต๊ะ <?= htmlspecialchars($table['table_number']) ?></h1>
        <p class="text-xs text-slate-400 mt-0.5">โซน: <?= htmlspecialchars($table['zone'] ?? 'Main') ?> (<?= (int)$table['seat_capacity'] ?> ที่นั่ง)</p>

        <!-- QR Code Container -->
        <div class="my-6 p-4 bg-slate-50 border-2 border-slate-100 rounded-2xl shadow-inner flex items-center justify-center">
            <div class="w-60 h-60 flex items-center justify-center [&>svg]:w-full [&>svg]:h-full">
                <?= $svgQr ?>
            </div>
        </div>

        <!-- Instructions -->
        <div class="space-y-1.5 text-center">
            <div class="inline-flex items-center space-x-1.5 px-3 py-1 bg-amber-50 text-amber-700 rounded-full text-xs font-semibold">
                <i class="ph ph-scan text-sm"></i>
                <span>สแกน QR เพื่อสั่งอาหาร</span>
            </div>
            <p class="text-xs text-slate-500 pt-2">ไม่ต้องดาวน์โหลดแอป • สั่งง่ายผ่านมือถือ</p>
            <p class="text-[11px] text-slate-400">ติดตามสถานะอาหารและสรุปยอดบิลได้แบบ Real-time</p>
        </div>

        <!-- Direct Link Text for convenience -->
        <div class="mt-6 pt-4 border-t border-slate-100 w-full text-center">
            <p class="text-[10px] text-slate-400 font-mono break-all"><?= htmlspecialchars($orderUrl) ?></p>
        </div>
    </div>

</body>
</html>
