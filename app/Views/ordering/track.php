<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>ติดตามสถานะอาหาร - โต๊ะ <?= htmlspecialchars($table['table_number']) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <style>
        body { font-family: 'Prompt', sans-serif; -webkit-tap-highlight-color: transparent; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 pb-24 antialiased">

    <!-- Top Sticky Mobile Header -->
    <header class="sticky top-0 z-40 bg-white/90 backdrop-blur-md border-b border-slate-200/80 px-4 py-3 shadow-sm">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-2">
                <a href="/order/table/<?= $qr_token ?>" class="w-9 h-9 rounded-xl bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-700">
                    <i class="ph ph-arrow-left text-lg"></i>
                </a>
                <div>
                    <h1 class="text-base font-extrabold text-slate-900 leading-tight">ติดตามการทำอาหาร</h1>
                    <p class="text-[11px] text-slate-400">โต๊ะ <?= htmlspecialchars($table['table_number']) ?> (<?= htmlspecialchars($table['zone'] ?? 'Main') ?>)</p>
                </div>
            </div>

            <!-- Live badge -->
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                <span>อัปเดตสด</span>
            </span>
        </div>
    </header>

    <main class="px-4 py-5 space-y-6 max-w-lg mx-auto">
        <!-- Status Stepper Card -->
        <div class="bg-white rounded-3xl p-5 border border-slate-100 shadow-sm">
            <h2 class="text-xs uppercase tracking-wider font-bold text-slate-400 mb-4 text-center">สถานะการทำอาหารโดยรวม</h2>
            
            <div class="grid grid-cols-3 gap-2 relative">
                <!-- Step 1 -->
                <div class="flex flex-col items-center text-center">
                    <div class="w-10 h-10 rounded-full bg-emerald-500 text-white flex items-center justify-center text-lg font-bold shadow-md shadow-emerald-500/20 mb-2">
                        <i class="ph ph-check"></i>
                    </div>
                    <span class="text-xs font-bold text-slate-800">ส่งเข้าครัว</span>
                    <span class="text-[10px] text-slate-400">ได้รับออเดอร์</span>
                </div>

                <!-- Step 2 -->
                <div class="flex flex-col items-center text-center">
                    <div id="stepCookingIcon" class="w-10 h-10 rounded-full bg-blue-500 text-white flex items-center justify-center text-lg font-bold shadow-md shadow-blue-500/20 mb-2 animate-pulse">
                        <i class="ph ph-cooking-pot"></i>
                    </div>
                    <span class="text-xs font-bold text-slate-800">กำลังปรุง</span>
                    <span class="text-[10px] text-slate-400">เชฟกำลังทำ</span>
                </div>

                <!-- Step 3 -->
                <div class="flex flex-col items-center text-center">
                    <div id="stepReadyIcon" class="w-10 h-10 rounded-full bg-slate-200 text-slate-500 flex items-center justify-center text-lg font-bold mb-2">
                        <i class="ph ph-bowl-food"></i>
                    </div>
                    <span class="text-xs font-bold text-slate-800">พร้อมเสิร์ฟ</span>
                    <span class="text-[10px] text-slate-400">พนักงานนำส่ง</span>
                </div>
            </div>
        </div>

        <!-- Orders & Items List -->
        <div class="space-y-4" id="ordersContainer">
            <?php if (empty($orders)): ?>
                <div class="text-center py-12 bg-white rounded-3xl p-6 border border-slate-100 shadow-sm">
                    <div class="w-12 h-12 mx-auto mb-3 rounded-full bg-amber-50 text-amber-500 flex items-center justify-center text-2xl">
                        <i class="ph ph-fork-knife"></i>
                    </div>
                    <h3 class="text-sm font-bold text-slate-800">ยังไม่มีรายการสั่งอาหารในรอบนี้</h3>
                    <p class="text-xs text-slate-400 mt-1">กดปุ่มด้านล่างเพื่อเลือกดูเมนูอาหารและสั่งได้เลย</p>
                    <a href="/order/table/<?= $qr_token ?>" class="mt-4 inline-flex items-center gap-1.5 px-4 py-2 bg-amber-500 text-white rounded-xl text-xs font-bold shadow-md">
                        <i class="ph ph-book-open"></i>
                        <span>ดูเมนูอาหาร</span>
                    </a>
                </div>
            <?php else: ?>
                <?php foreach ($orders as $o): ?>
                    <div class="bg-white rounded-3xl p-5 border border-slate-100 shadow-sm space-y-3">
                        <div class="flex items-center justify-between pb-2 border-b border-slate-100 text-xs">
                            <span class="font-mono font-bold text-slate-600">ออเดอร์ #<?= htmlspecialchars($o['order_number']) ?></span>
                            <span class="text-slate-400"><?= (int)$o['elapsed_minutes'] ?> นาทีที่แล้ว</span>
                        </div>

                        <!-- Items in this order -->
                        <div class="space-y-2.5">
                            <?php foreach ($o['items'] as $item): ?>
                                <?php
                                    $st = $item['kitchen_status'];
                                    $badgeColor = $st === 'served' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : ($st === 'ready' ? 'bg-amber-100 text-amber-800 border-amber-300 animate-bounce' : ($st === 'cooking' ? 'bg-blue-50 text-blue-700 border-blue-200' : 'bg-slate-100 text-slate-600 border-slate-200'));
                                    $badgeText  = $st === 'served' ? '✓ เสิร์ฟแล้ว' : ($st === 'ready' ? '🔔 พร้อมเสิร์ฟ' : ($st === 'cooking' ? 'กำลังปรุง...' : 'รอคิวทำ'));
                                ?>
                                <div class="flex items-center justify-between text-xs py-1">
                                    <div class="flex items-start gap-2.5">
                                        <span class="w-5 h-5 rounded-md bg-slate-100 text-slate-700 font-bold flex items-center justify-center text-[11px] flex-shrink-0">
                                            <?= (int)$item['quantity'] ?>x
                                        </span>
                                        <div>
                                            <p class="font-bold text-slate-800"><?= htmlspecialchars($item['name_th']) ?></p>
                                            <?php if (!empty($item['options'])): ?>
                                                <p class="text-[10px] text-slate-400">
                                                    <?= implode(', ', array_map(fn($x) => htmlspecialchars($x['option_name'] ?? ''), $item['options'])) ?>
                                                </p>
                                            <?php endif; ?>
                                            <?php if (!empty($item['special_note'])): ?>
                                                <p class="text-[10px] text-rose-500 font-medium">หมายเหตุ: <?= htmlspecialchars($item['special_note']) ?></p>
                                            <?php endif; ?>
                                            <a href="/order/table/<?= $qr_token ?>?search=<?= urlencode($item['name_th']) ?>" class="inline-flex items-center gap-1 text-[10px] font-bold text-amber-600 bg-amber-50 hover:bg-amber-100 px-2 py-0.5 rounded-md mt-1 transition-colors">
                                                <i class="ph ph-plus-circle"></i>
                                                <span>สั่งจานนี้เพิ่ม</span>
                                            </a>
                                        </div>
                                    </div>

                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold border <?= $badgeColor ?> whitespace-nowrap">
                                        <?= $badgeText ?>
                                    </span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>

    <!-- Bottom Navigation Bar -->
    <div class="fixed bottom-0 inset-x-0 bg-white/95 backdrop-blur-md border-t border-slate-200 px-6 py-3 flex items-center justify-between z-40 max-w-lg mx-auto">
        <a href="/order/table/<?= $qr_token ?>" class="flex flex-col items-center text-slate-400 hover:text-amber-600 transition-colors">
            <i class="ph ph-bowl-food text-2xl"></i>
            <span class="text-[10px] font-bold mt-0.5">เมนูอาหาร</span>
        </a>

        <a href="/order/track/<?= $qr_token ?>" class="flex flex-col items-center text-amber-600 font-bold">
            <i class="ph ph-hourglass-high text-2xl"></i>
            <span class="text-[10px] mt-0.5">ติดตามอาหาร</span>
        </a>

        <a href="/order/bill/<?= $qr_token ?>" class="flex flex-col items-center text-slate-400 hover:text-amber-600 transition-colors">
            <i class="ph ph-receipt text-2xl"></i>
            <span class="text-[10px] font-bold mt-0.5">สรุปยอดเงิน</span>
        </a>
    </div>

<script>
const QR_TOKEN = '<?= $qr_token ?>';

function pollLiveStatus() {
    fetch('/order/api/status/' + QR_TOKEN)
    .then(res => res.json())
    .then(data => {
        if (data.success && data.orders) {
            updateLiveTrackerUI(data.orders);
        }
    })
    .catch(err => console.log('Polling error:', err));
}

function updateLiveTrackerUI(orders) {
    if (!orders || orders.length === 0) return;

    let hasCooking = false;
    let hasReady = false;
    let allServed = true;

    orders.forEach(o => {
        o.items.forEach(it => {
            if (it.kitchen_status === 'cooking') hasCooking = true;
            if (it.kitchen_status === 'ready') hasReady = true;
            if (it.kitchen_status !== 'served') allServed = false;
        });
    });

    const stepCooking = document.getElementById('stepCookingIcon');
    const stepReady = document.getElementById('stepReadyIcon');

    if (hasCooking || hasReady || allServed) {
        stepCooking.className = 'w-10 h-10 rounded-full bg-emerald-500 text-white flex items-center justify-center text-lg font-bold shadow-md shadow-emerald-500/20 mb-2';
        stepCooking.innerHTML = '<i class="ph ph-check"></i>';
    }

    if (hasReady || allServed) {
        stepReady.className = 'w-10 h-10 rounded-full bg-emerald-500 text-white flex items-center justify-center text-lg font-bold shadow-md shadow-emerald-500/20 mb-2';
        stepReady.innerHTML = '<i class="ph ph-check"></i>';
    } else if (hasCooking) {
        stepReady.className = 'w-10 h-10 rounded-full bg-amber-500 text-white flex items-center justify-center text-lg font-bold mb-2 animate-pulse';
        stepReady.innerHTML = '<i class="ph ph-bowl-food"></i>';
    }
}

// Poll every 3.5 seconds
setInterval(pollLiveStatus, 3500);
</script>
</body>
</html>
