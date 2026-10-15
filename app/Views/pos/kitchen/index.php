<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-slate-100 flex items-center gap-2">
                <i class="ph ph-cooking-pot text-amber-500"></i>
                <span><?= __('pos.kitchen_display') ?></span>
                <span id="activeOrderBadge" class="ml-2 px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800 dark:bg-amber-900/60 dark:text-amber-200">
                    <?= count($orders) ?> ออเดอร์
                </span>
            </h1>
            <p class="text-sm text-gray-500 dark:text-slate-400 mt-1">
                จอแสดงรายการอาหารสำหรับพ่อครัวและบาร์น้ำ อัปเดตอัตโนมัติแบบ Real-time
            </p>
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-emerald-50 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                <span>เชื่อมต่อแบบ Real-time</span>
            </span>
            <button onclick="refreshOrders()" class="p-2 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-lg text-gray-600 dark:text-slate-300 hover:bg-gray-50" title="รีเฟรช">
                <i class="ph ph-arrows-clockwise text-lg"></i>
            </button>
        </div>
    </div>

    <!-- Kitchen Orders Board -->
    <div id="kitchenBoard" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
        <?php if (empty($orders)): ?>
            <div class="col-span-full py-16 text-center bg-white dark:bg-slate-800 rounded-2xl border border-gray-100 dark:border-slate-700/60 shadow-sm" id="emptyNotice">
                <div class="w-16 h-16 mx-auto mb-3 rounded-full bg-emerald-50 dark:bg-emerald-950 flex items-center justify-center text-emerald-500 text-3xl">
                    <i class="ph ph-check"></i>
                </div>
                <h3 class="text-base font-bold text-gray-700 dark:text-slate-200">ยังไม่มีรายการอาหารที่รอปรุง</h3>
                <p class="text-xs text-gray-400 mt-1">เมื่อมีลูกค้าสแกนสั่งจากโต๊ะ ออเดอร์จะปรากฏที่นี่ทันที</p>
            </div>
        <?php else: ?>
            <?php foreach ($orders as $o): ?>
                <?php
                    $elapsed = (int)$o['elapsed_minutes'];
                    $timeBadge = $elapsed >= 25 ? 'bg-rose-500 text-white animate-pulse' : ($elapsed >= 15 ? 'bg-amber-500 text-white' : 'bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-200');
                ?>
                <div class="order-card bg-white dark:bg-slate-800 rounded-2xl border-2 border-amber-300/60 dark:border-amber-700/50 shadow-sm overflow-hidden flex flex-col justify-between" id="order-card-<?= $o['id'] ?>">
                    <div>
                        <!-- Order Ticket Header -->
                        <div class="p-4 bg-amber-50/60 dark:bg-amber-950/30 border-b border-amber-100 dark:border-amber-900/40 flex items-center justify-between">
                            <div>
                                <div class="flex items-center gap-1.5 mb-1">
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full <?= $o['order_type'] === 'takeaway' ? 'bg-orange-500 text-white' : 'bg-emerald-600 text-white' ?>">
                                        <?= $o['order_type'] === 'takeaway' ? '🛍️ สั่งกลับบ้าน (QSR)' : '🍽️ ทานที่ร้าน' ?>
                                    </span>
                                    <span class="text-xs font-mono text-gray-500 dark:text-slate-400">#<?= htmlspecialchars($o['order_number']) ?></span>
                                </div>
                                <h3 class="text-2xl font-black text-gray-800 dark:text-slate-100">
                                    <?= !empty($o['queue_number']) ? 'คิว ' . htmlspecialchars($o['queue_number']) : 'โต๊ะ ' . htmlspecialchars($o['table_number']) ?>
                                </h3>
                            </div>
                            <div class="text-right">
                                <span class="px-2 py-1 rounded-md text-xs font-bold font-mono <?= $timeBadge ?>">
                                    <?= $elapsed ?> นาทีที่แล้ว
                                </span>
                            </div>
                        </div>

                        <!-- Customer Overall Note -->
                        <?php if (!empty($o['customer_note'])): ?>
                            <div class="px-4 py-2 bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-300 text-xs flex items-center gap-1.5 border-b border-rose-100 dark:border-rose-900/50 font-medium">
                                <i class="ph ph-warning-circle text-base"></i>
                                <span>หมายเหตุ: <?= htmlspecialchars($o['customer_note']) ?></span>
                            </div>
                        <?php endif; ?>

                        <!-- Items List -->
                        <div class="p-4 space-y-3">
                            <?php foreach ($o['items'] as $item): ?>
                                <?php
                                    $kStatus = $item['kitchen_status'];
                                    $itemBadge = $kStatus === 'ready' ? 'bg-emerald-100 text-emerald-800 border-emerald-300' : ($kStatus === 'cooking' ? 'bg-blue-100 text-blue-800 border-blue-300' : 'bg-slate-100 text-slate-700 border-slate-200');
                                    $itemText = $kStatus === 'ready' ? 'พร้อมเสิร์ฟ' : ($kStatus === 'cooking' ? 'กำลังปรุง' : 'รอทำ');
                                ?>
                                <div class="p-2.5 rounded-xl border border-gray-100 dark:border-slate-700/80 bg-slate-50/50 dark:bg-slate-700/30 flex items-start justify-between gap-2" id="item-row-<?= $item['id'] ?>">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2">
                                            <span class="w-6 h-6 rounded-md bg-amber-500 text-white font-black text-xs flex items-center justify-center flex-shrink-0">
                                                <?= (int)$item['quantity'] ?>x
                                            </span>
                                            <span class="font-bold text-sm text-gray-800 dark:text-slate-100">
                                                <?= htmlspecialchars($item['name_th']) ?>
                                            </span>
                                        </div>

                                        <!-- Options & Notes -->
                                        <?php if (!empty($item['options'])): ?>
                                            <div class="ml-8 mt-1 flex flex-wrap gap-1">
                                                <?php foreach ($item['options'] as $opt): ?>
                                                    <span class="text-[10px] px-1.5 py-0.5 rounded bg-gray-200 dark:bg-slate-600 text-gray-700 dark:text-slate-200 font-medium">
                                                        <?= htmlspecialchars($opt['option_name'] ?? '') ?>
                                                    </span>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>

                                        <?php if (!empty($item['special_note'])): ?>
                                            <div class="ml-8 mt-1 text-xs text-rose-500 font-medium flex items-center gap-1">
                                                <i class="ph ph-chat-text"></i>
                                                <span><?= htmlspecialchars($item['special_note']) ?></span>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Status Actions -->
                                    <div class="flex flex-col items-end gap-1 flex-shrink-0">
                                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full border <?= $itemBadge ?>" id="status-pill-<?= $item['id'] ?>">
                                            <?= $itemText ?>
                                        </span>

                                        <div class="flex items-center gap-1 mt-1">
                                            <?php if ($kStatus === 'pending'): ?>
                                                <button type="button" onclick="updateKitchenStatus(<?= $item['id'] ?>, 'cooking')" class="px-2 py-1 text-[11px] font-semibold bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors">
                                                    เริ่มทำ
                                                </button>
                                            <?php elseif ($kStatus === 'cooking'): ?>
                                                <button type="button" onclick="updateKitchenStatus(<?= $item['id'] ?>, 'ready')" class="px-2 py-1 text-[11px] font-semibold bg-emerald-600 text-white rounded hover:bg-emerald-700 transition-colors">
                                                    เสร็จแล้ว
                                                </button>
                                            <?php elseif ($kStatus === 'ready'): ?>
                                                <button type="button" onclick="updateKitchenStatus(<?= $item['id'] ?>, 'served')" class="px-2 py-1 text-[11px] font-semibold bg-slate-700 text-white rounded hover:bg-slate-800 transition-colors">
                                                    เสิร์ฟแล้ว
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<script>
let lastOrderCount = <?= count($orders) ?>;

// Web Audio API Beep Sound for new orders
function playAlertBeep() {
    try {
        const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
        const osc = audioCtx.createOscillator();
        const gain = audioCtx.createGain();
        osc.connect(gain);
        gain.connect(audioCtx.destination);
        osc.type = 'sine';
        osc.frequency.value = 880;
        gain.gain.setValueAtTime(0.3, audioCtx.currentTime);
        gain.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 0.6);
        osc.start();
        osc.stop(audioCtx.currentTime + 0.6);
    } catch (e) {
        console.log('Audio alert error:', e);
    }
}

function updateKitchenStatus(itemId, status) {
    const formData = new FormData();
    formData.append('item_id', itemId);
    formData.append('status', status);

    fetch('/pos/kitchen/update-status', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            refreshOrders();
        }
    });
}

function refreshOrders() {
    fetch('/pos/kitchen/active-orders')
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            document.getElementById('activeOrderBadge').textContent = data.count + ' ออเดอร์';
            if (data.count > lastOrderCount) {
                playAlertBeep();
            }
            lastOrderCount = data.count;
            renderBoard(data.orders);
        }
    });
}

function renderBoard(orders) {
    const board = document.getElementById('kitchenBoard');
    if (!orders || orders.length === 0) {
        board.innerHTML = `
            <div class="col-span-full py-16 text-center bg-white dark:bg-slate-800 rounded-2xl border border-gray-100 dark:border-slate-700/60 shadow-sm">
                <div class="w-16 h-16 mx-auto mb-3 rounded-full bg-emerald-50 dark:bg-emerald-950 flex items-center justify-center text-emerald-500 text-3xl">
                    <i class="ph ph-check"></i>
                </div>
                <h3 class="text-base font-bold text-gray-700 dark:text-slate-200">ยังไม่มีรายการอาหารที่รอปรุง</h3>
                <p class="text-xs text-gray-400 mt-1">เมื่อมีลูกค้าสแกนสั่งจากโต๊ะ ออเดอร์จะปรากฏที่นี่ทันที</p>
            </div>
        `;
        return;
    }

    let html = '';
    orders.forEach(o => {
        const elapsed = o.elapsed_minutes || 0;
        const timeBadge = elapsed >= 25 ? 'bg-rose-500 text-white animate-pulse' : (elapsed >= 15 ? 'bg-amber-500 text-white' : 'bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-200');

        let noteHtml = o.customer_note ? `
            <div class="px-4 py-2 bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-300 text-xs flex items-center gap-1.5 border-b border-rose-100 dark:border-rose-900/50 font-medium">
                <i class="ph ph-warning-circle text-base"></i>
                <span>หมายเหตุ: ${escapeHtml(o.customer_note)}</span>
            </div>
        ` : '';

        let itemsHtml = '';
        o.items.forEach(it => {
            const kStatus = it.kitchen_status;
            const itemBadge = kStatus === 'ready' ? 'bg-emerald-100 text-emerald-800 border-emerald-300' : (kStatus === 'cooking' ? 'bg-blue-100 text-blue-800 border-blue-300' : 'bg-slate-100 text-slate-700 border-slate-200');
            const itemText = kStatus === 'ready' ? 'พร้อมเสิร์ฟ' : (kStatus === 'cooking' ? 'กำลังปรุง' : 'รอทำ');

            let btnHtml = '';
            if (kStatus === 'pending') {
                btnHtml = `<button type="button" onclick="updateKitchenStatus(${it.id}, 'cooking')" class="px-2 py-1 text-[11px] font-semibold bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors">เริ่มทำ</button>`;
            } else if (kStatus === 'cooking') {
                btnHtml = `<button type="button" onclick="updateKitchenStatus(${it.id}, 'ready')" class="px-2 py-1 text-[11px] font-semibold bg-emerald-600 text-white rounded hover:bg-emerald-700 transition-colors">เสร็จแล้ว</button>`;
            } else if (kStatus === 'ready') {
                btnHtml = `<button type="button" onclick="updateKitchenStatus(${it.id}, 'served')" class="px-2 py-1 text-[11px] font-semibold bg-slate-700 text-white rounded hover:bg-slate-800 transition-colors">เสิร์ฟแล้ว</button>`;
            }

            let optsHtml = '';
            if (it.options && it.options.length > 0) {
                optsHtml = '<div class="ml-8 mt-1 flex flex-wrap gap-1">' + it.options.map(opt => `<span class="text-[10px] px-1.5 py-0.5 rounded bg-gray-200 dark:bg-slate-600 text-gray-700 dark:text-slate-200 font-medium">${escapeHtml(opt.option_name || '')}</span>`).join('') + '</div>';
            }

            let specialHtml = it.special_note ? `<div class="ml-8 mt-1 text-xs text-rose-500 font-medium flex items-center gap-1"><i class="ph ph-chat-text"></i><span>${escapeHtml(it.special_note)}</span></div>` : '';

            itemsHtml += `
                <div class="p-2.5 rounded-xl border border-gray-100 dark:border-slate-700/80 bg-slate-50/50 dark:bg-slate-700/30 flex items-start justify-between gap-2">
                    <div class="flex-1">
                        <div class="flex items-center gap-2">
                            <span class="w-6 h-6 rounded-md bg-amber-500 text-white font-black text-xs flex items-center justify-center flex-shrink-0">${it.quantity}x</span>
                            <span class="font-bold text-sm text-gray-800 dark:text-slate-100">${escapeHtml(it.name_th)}</span>
                        </div>
                        ${optsHtml}
                        ${specialHtml}
                    </div>
                    <div class="flex flex-col items-end gap-1 flex-shrink-0">
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full border ${itemBadge}">${itemText}</span>
                        <div class="flex items-center gap-1 mt-1">${btnHtml}</div>
                    </div>
                </div>
            `;
        });

        html += `
            <div class="order-card bg-white dark:bg-slate-800 rounded-2xl border-2 border-amber-300/60 dark:border-amber-700/50 shadow-sm overflow-hidden flex flex-col justify-between">
                <div>
                    <div class="p-4 bg-amber-50/60 dark:bg-amber-950/30 border-b border-amber-100 dark:border-amber-900/40 flex items-center justify-between">
                        <div>
                            <div class="flex items-center gap-1.5 mb-1">
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full ${o.order_type === 'takeaway' ? 'bg-orange-500 text-white' : 'bg-emerald-600 text-white'}">
                                    ${o.order_type === 'takeaway' ? '🛍️ สั่งกลับบ้าน (QSR)' : '🍽️ ทานที่ร้าน'}
                                </span>
                                <span class="text-xs font-mono text-gray-500 dark:text-slate-400">#${escapeHtml(o.order_number)}</span>
                            </div>
                            <h3 class="text-2xl font-black text-gray-800 dark:text-slate-100">
                                ${o.queue_number ? 'คิว ' + escapeHtml(o.queue_number) : 'โต๊ะ ' + escapeHtml(o.table_number)}
                            </h3>
                        </div>
                        <div class="text-right">
                            <span class="px-2 py-1 rounded-md text-xs font-bold font-mono ${timeBadge}">${elapsed} นาทีที่แล้ว</span>
                        </div>
                    </div>
                    ${noteHtml}
                    <div class="p-4 space-y-3">${itemsHtml}</div>
                </div>
            </div>
        `;
    });

    board.innerHTML = html;
}

function escapeHtml(str) {
    if (!str) return '';
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
}

// Auto polling every 4 seconds
setInterval(refreshOrders, 4000);
</script>
