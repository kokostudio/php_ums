<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ใบเสร็จรับเงิน - <?= htmlspecialchars($bill['bill_number']) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <style>
        body { font-family: 'Prompt', monospace, sans-serif; }
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; padding: 0 !important; }
            .receipt-box { box-shadow: none !important; border: none !important; width: 100% !important; max-width: 80mm !important; }
        }
    </style>
</head>
<body class="bg-slate-100 min-h-screen flex flex-col items-center justify-center p-4">

    <!-- Print Control Buttons -->
    <div class="no-print mb-4 flex items-center space-x-2">
        <button onclick="window.print()" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-medium shadow text-sm flex items-center gap-1.5">
            <i class="ph ph-printer text-base"></i>
            <span>พิมพ์ใบเสร็จ</span>
        </button>
        <button onclick="window.close()" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-xl font-medium text-sm">
            <span>ปิด</span>
        </button>
    </div>

    <!-- Receipt Container (80mm Paper Spec) -->
    <div class="receipt-box bg-white w-full max-w-[340px] rounded-2xl shadow-xl border border-slate-200 p-6 text-slate-800 text-xs">
        <div class="text-center pb-4 border-b border-dashed border-slate-300">
            <h1 class="text-lg font-black tracking-tight uppercase"><?= htmlspecialchars(\App\Core\Settings::get('site_name', 'CORE RESTAURANT')) ?></h1>
            <p class="text-[11px] text-slate-400 mt-0.5">ใบเสร็จรับเงิน / Receipt</p>
            <p class="text-[11px] text-slate-500 font-mono mt-1">#<?= htmlspecialchars($bill['bill_number']) ?></p>
        </div>

        <div class="py-3 border-b border-dashed border-slate-300 space-y-1 text-[11px] text-slate-600">
            <div class="flex justify-between">
                <span>โต๊ะ:</span>
                <span class="font-bold text-slate-800"><?= htmlspecialchars($bill['table_number']) ?> (<?= htmlspecialchars($bill['zone'] ?? 'Main') ?>)</span>
            </div>
            <div class="flex justify-between">
                <span>วันที่:</span>
                <span><?= date('d/m/Y H:i', strtotime($bill['paid_at'])) ?></span>
            </div>
            <div class="flex justify-between">
                <span>แคชเชียร์:</span>
                <span><?= htmlspecialchars($bill['cashier_name'] ?? 'Staff') ?></span>
            </div>
        </div>

        <!-- Itemized List -->
        <div class="py-3 border-b border-dashed border-slate-300 space-y-2">
            <div class="flex justify-between font-bold text-slate-700 text-[11px] pb-1 border-b border-slate-100">
                <span>รายการ</span>
                <span>จำนวน x ราคา</span>
                <span class="text-right">รวม (บาท)</span>
            </div>
            <?php foreach ($items as $item): ?>
                <div class="text-[11px]">
                    <div class="flex justify-between">
                        <span class="font-semibold flex-1 pr-2"><?= htmlspecialchars($item['name_th']) ?></span>
                        <span class="text-slate-500 whitespace-nowrap"><?= (int)$item['quantity'] ?> x <?= number_format((float)$item['unit_price'], 2) ?></span>
                        <span class="w-16 text-right font-mono font-bold"><?= number_format((float)$item['subtotal'], 2) ?></span>
                    </div>
                    <?php if (!empty($item['options'])): ?>
                        <div class="text-[10px] text-slate-400 pl-2">
                            <?= implode(', ', array_map(fn($o) => htmlspecialchars($o['option_name'] ?? ''), $item['options'])) ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Financial Summary -->
        <div class="py-3 border-b border-dashed border-slate-300 space-y-1 text-xs">
            <div class="flex justify-between text-slate-600">
                <span>ยอดรวมค่าอาหาร:</span>
                <span class="font-mono"><?= number_format((float)$bill['subtotal_amount'], 2) ?> บาท</span>
            </div>
            <?php if ((float)$bill['discount_amount'] > 0): ?>
                <div class="flex justify-between text-rose-600">
                    <span>ส่วนลด:</span>
                    <span class="font-mono">-<?= number_format((float)$bill['discount_amount'], 2) ?> บาท</span>
                </div>
            <?php endif; ?>
            <div class="flex justify-between items-baseline pt-2 border-t border-slate-200 text-sm font-black text-slate-900">
                <span>ยอดสุทธิ:</span>
                <span class="text-base font-mono"><?= number_format((float)$bill['grand_total'], 2) ?> บาท</span>
            </div>
            <div class="flex justify-between text-[11px] text-slate-500 pt-1">
                <span>วิธีชำระเงิน:</span>
                <span class="font-semibold uppercase"><?= $bill['payment_method'] ?></span>
            </div>
        </div>

        <!-- Footer Notice -->
        <div class="pt-4 text-center text-[10px] text-slate-400 space-y-1">
            <p>ขอบคุณที่ใช้บริการ / Thank you!</p>
            <p>โปรดเก็บใบเสร็จไว้เป็นหลักฐาน</p>
        </div>
    </div>

</body>
</html>
