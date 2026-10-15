<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>สรุปยอดเงิน - โต๊ะ <?= htmlspecialchars($table['table_number']) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { font-family: 'Prompt', sans-serif; -webkit-tap-highlight-color: transparent; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 pb-28 antialiased">

    <!-- Top Sticky Mobile Header -->
    <header class="sticky top-0 z-40 bg-white/90 backdrop-blur-md border-b border-slate-200/80 px-4 py-3 shadow-sm">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-2">
                <a href="/order/table/<?= $qr_token ?>" class="w-9 h-9 rounded-xl bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-700">
                    <i class="ph ph-arrow-left text-lg"></i>
                </a>
                <div>
                    <h1 class="text-base font-extrabold text-slate-900 leading-tight">สรุปยอดเงินค่าอาหาร</h1>
                    <p class="text-[11px] text-slate-400">โต๊ะ <?= htmlspecialchars($table['table_number']) ?> (<?= htmlspecialchars($table['zone'] ?? 'Main') ?>)</p>
                </div>
            </div>

            <!-- Table Status Badge -->
            <span id="tableStatusPill" class="text-[11px] font-bold px-2.5 py-1 rounded-full <?= $table['status'] === 'billing' ? 'bg-rose-100 text-rose-700 animate-pulse' : 'bg-slate-100 text-slate-700' ?>">
                <?= $table['status'] === 'billing' ? '🔔 กำลังรอเช็คบิล' : 'กำลังรับประทาน' ?>
            </span>
        </div>
    </header>

    <main class="px-4 py-5 space-y-5 max-w-lg mx-auto">
        <!-- Bill Overview Card -->
        <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm text-center">
            <div class="w-12 h-12 mx-auto mb-2 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-2xl shadow-inner">
                <i class="ph ph-receipt"></i>
            </div>
            <p class="text-xs uppercase tracking-wider font-bold text-slate-400">ยอดชำระสุทธิปัจจุบัน</p>
            <h2 class="text-3xl font-black text-slate-900 mt-1">
                <?= number_format((float)$subtotal, 2) ?> <span class="text-sm font-semibold text-slate-400">บาท</span>
            </h2>
            <p class="text-[11px] text-slate-400 mt-1">
                สั่งอาหารทั้งหมด <?= (int)$totalItemsCount ?> จาน • เวลาเริ่ม: <?= date('H:i', strtotime($session['opened_at'])) ?> น.
            </p>

            <!-- Call Waiter / Request Bill CTA -->
            <div class="mt-5 pt-4 border-t border-slate-100 flex items-center gap-2">
                <button type="button" onclick="callWaiter('waiter')" class="flex-1 py-2.5 bg-slate-100 hover:bg-slate-200 active:bg-slate-300 text-slate-700 rounded-xl text-xs font-bold flex items-center justify-center gap-1.5 transition-all">
                    <i class="ph ph-bell text-base"></i>
                    <span>เรียกพนักงาน</span>
                </button>
                <button type="button" onclick="callWaiter('bill')" class="flex-1 py-2.5 bg-rose-600 hover:bg-rose-700 active:bg-rose-800 text-white rounded-xl text-xs font-bold shadow-md shadow-rose-600/20 flex items-center justify-center gap-1.5 transition-all">
                    <i class="ph ph-credit-card text-base"></i>
                    <span>ขอเช็คบิล</span>
                </button>
            </div>
        </div>

        <!-- Itemized Food Breakdown -->
        <div class="bg-white rounded-3xl p-5 border border-slate-100 shadow-sm space-y-3">
            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider pb-2 border-b border-slate-100">
                รายละเอียดรายการอาหารทั้งหมด
            </h3>

            <?php if (empty($orders)): ?>
                <p class="text-center text-xs text-slate-400 py-4">ยังไม่มีรายการสั่งอาหาร</p>
            <?php else: ?>
                <div class="space-y-3">
                    <?php foreach ($orders as $o): ?>
                        <?php foreach ($o['items'] as $item): ?>
                            <?php if ($item['kitchen_status'] !== 'cancelled'): ?>
                                <div class="flex items-start justify-between text-xs py-1">
                                    <div class="flex items-start gap-2">
                                        <span class="w-5 h-5 rounded-md bg-slate-100 text-slate-700 font-bold flex items-center justify-center text-[10px] flex-shrink-0">
                                            <?= (int)$item['quantity'] ?>x
                                        </span>
                                        <div>
                                            <p class="font-bold text-slate-800"><?= htmlspecialchars($item['name_th']) ?></p>
                                            <?php if (!empty($item['options'])): ?>
                                                <p class="text-[10px] text-slate-400">
                                                    <?= implode(', ', array_map(fn($x) => htmlspecialchars($x['option_name'] ?? ''), $item['options'])) ?>
                                                </p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="text-right whitespace-nowrap pl-2">
                                        <span class="font-mono font-bold text-slate-800">
                                            <?= number_format((float)$item['subtotal'], 2) ?>
                                        </span>
                                        <span class="text-[10px] text-slate-400">บาท</span>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                </div>

                <div class="pt-3 border-t border-slate-100 space-y-1.5 text-xs">
                    <div class="flex justify-between text-slate-500">
                        <span>ยอดรวมค่าอาหาร:</span>
                        <span class="font-mono"><?= number_format((float)$subtotal, 2) ?> บาท</span>
                    </div>
                    <div class="flex justify-between text-slate-500">
                        <span>ภาษีมูลค่าเพิ่ม (VAT 7% รวมในราคาแล้ว):</span>
                        <span class="font-mono"><?= number_format((float)($subtotal * 7 / 107), 2) ?> บาท</span>
                    </div>
                    <div class="flex justify-between items-baseline pt-2 border-t border-slate-100 text-sm font-black text-slate-900">
                        <span>ยอดรวมสุทธิ:</span>
                        <span class="text-xl font-black text-emerald-600 font-mono"><?= number_format((float)$subtotal, 2) ?> บาท</span>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- PromptPay / Payment Info Card -->
        <div class="bg-gradient-to-br from-slate-900 to-slate-800 text-white rounded-3xl p-6 shadow-xl space-y-4 text-center">
            <div class="w-10 h-10 mx-auto rounded-xl bg-white/10 flex items-center justify-center text-xl text-amber-400">
                <i class="ph ph-qr-code"></i>
            </div>
            <div>
                <h4 class="text-sm font-bold">สแกนชำระเงินผ่านพร้อมเพย์ (PromptPay)</h4>
                <p class="text-xs text-slate-400 mt-1">สแกน QR Code ด้านล่างเพื่อชำระเงิน หรือแจ้งพนักงานหากต้องการชำระด้วยเงินสด</p>
            </div>
            <?php if ($subtotal > 0): ?>
                <div class="bg-white p-3 rounded-2xl inline-block shadow-lg mx-auto">
                    <img src="/order/promptpay-qr/<?= $qr_token ?>?t=<?= time() ?>" alt="PromptPay QR" class="w-48 h-48 mx-auto object-contain">
                    <div class="mt-2 pt-2 border-t border-slate-100">
                        <span class="text-xs font-bold text-slate-700">ยอดชำระ:</span>
                        <span class="text-sm font-black text-emerald-600 font-mono"><?= number_format((float)$subtotal, 2) ?></span>
                        <span class="text-xs text-slate-500">บาท</span>
                    </div>
                </div>
                <p class="text-[11px] text-amber-300/90 font-medium">
                    💡 โอนเงินแล้วกดปุ่ม <span class="underline">"ขอเช็คบิล"</span> เพื่อให้พนักงานออกใบเสร็จ
                </p>
            <?php else: ?>
                <div class="inline-block px-3 py-1.5 rounded-full bg-white/10 text-xs font-mono text-amber-300">
                    ยังไม่มียอดที่ต้องชำระ
                </div>
            <?php endif; ?>
        </div>
    </main>

    <!-- Bottom Navigation Bar -->
    <div class="fixed bottom-0 inset-x-0 bg-white/95 backdrop-blur-md border-t border-slate-200 px-6 py-3 flex items-center justify-between z-40 max-w-lg mx-auto">
        <a href="/order/table/<?= $qr_token ?>" class="flex flex-col items-center text-slate-400 hover:text-amber-600 transition-colors">
            <i class="ph ph-bowl-food text-2xl"></i>
            <span class="text-[10px] font-bold mt-0.5">เมนูอาหาร</span>
        </a>

        <a href="/order/track/<?= $qr_token ?>" class="flex flex-col items-center text-slate-400 hover:text-amber-600 transition-colors">
            <i class="ph ph-hourglass-high text-2xl"></i>
            <span class="text-[10px] font-bold mt-0.5">ติดตามอาหาร</span>
        </a>

        <a href="/order/bill/<?= $qr_token ?>" class="flex flex-col items-center text-amber-600 font-bold">
            <i class="ph ph-receipt text-2xl"></i>
            <span class="text-[10px] mt-0.5">สรุปยอดเงิน</span>
        </a>
    </div>

<script>
const QR_TOKEN = '<?= $qr_token ?>';

function callWaiter(actionType) {
    const isBill = actionType === 'bill';
    const title = isBill ? 'ต้องการขอเช็คบิล?' : 'ต้องการเรียกพนักงาน?';
    const text = isBill ? 'ระบบจะส่งสัญญาณไปยังเคาน์เตอร์แคชเชียร์เพื่อเตรียมใบเสร็จ' : 'พนักงานจะเดินมาให้บริการที่โต๊ะของคุณ';

    Swal.fire({
        title: title,
        text: text,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: isBill ? '#e11d48' : '#3b82f6',
        cancelButtonColor: '#94a3b8',
        confirmButtonText: 'ยืนยัน',
        cancelButtonText: 'ยกเลิก'
    }).then((result) => {
        if (result.isConfirmed) {
            const formData = new FormData();
            formData.append('action', actionType);

            fetch('/order/call-waiter/' + QR_TOKEN, {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'เรียบร้อย!',
                        text: data.message,
                        icon: 'success',
                        confirmButtonColor: '#059669'
                    });
                    if (isBill) {
                        const pill = document.getElementById('tableStatusPill');
                        pill.className = 'text-[11px] font-bold px-2.5 py-1 rounded-full bg-rose-100 text-rose-700 animate-pulse';
                        pill.textContent = '🔔 กำลังรอเช็คบิล';
                    }
                }
            });
        }
    });
}
</script>
</body>
</html>
