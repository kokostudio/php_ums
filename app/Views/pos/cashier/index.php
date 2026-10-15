<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-slate-100 flex items-center gap-2">
                <i class="ph ph-receipt text-emerald-500"></i>
                <span><?= __('pos.cashier_billing') ?></span>
            </h1>
            <p class="text-sm text-gray-500 dark:text-slate-400 mt-1">
                ตรวจสอบสถานะโต๊ะที่กำลังทาน เช็คบิล คิดเงิน และออกใบเสร็จ
            </p>
        </div>
    </div>

    <!-- Active Tables for Checkout Grid -->
    <div>
        <h2 class="text-base font-bold text-gray-700 dark:text-slate-200 mb-3 flex items-center gap-2">
            <i class="ph ph-squares-four text-primary-500"></i>
            <span>โต๊ะที่กำลังใช้งาน / รอชำระเงิน</span>
        </h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            <?php 
                $activeCount = 0;
                foreach ($tables as $t): 
                    if ($t['status'] === 'available') continue;
                    $activeCount++;
                    $isBilling = $t['status'] === 'billing';
            ?>
                <div class="bg-white dark:bg-slate-800 rounded-2xl border-2 <?= $isBilling ? 'border-rose-500 dark:border-rose-600 shadow-rose-100 dark:shadow-rose-950/50 animate-pulse' : 'border-amber-300 dark:border-amber-700/60' ?> shadow-md p-5 flex flex-col justify-between cursor-pointer hover:scale-[1.02] transition-transform" onclick="openCheckoutModal(<?= $t['id'] ?>, '<?= htmlspecialchars($t['table_number']) ?>')">
                    <div>
                        <div class="flex items-start justify-between">
                            <div>
                                <span class="text-xs font-bold px-2 py-0.5 rounded-full <?= $isBilling ? 'bg-rose-100 text-rose-700 dark:bg-rose-900/60 dark:text-rose-200' : 'bg-amber-100 text-amber-800 dark:bg-amber-900/60 dark:text-amber-200' ?>">
                                    <?= $isBilling ? '🔔 ลูกค้าขอเช็คบิล' : 'กำลังรับประทาน' ?>
                                </span>
                                <h3 class="text-3xl font-black text-gray-800 dark:text-slate-100 mt-2">
                                    โต๊ะ <?= htmlspecialchars($t['table_number']) ?>
                                </h3>
                                <p class="text-xs text-gray-400 mt-0.5">
                                    นั่งมาแล้ว: <?= (int)($t['dining_minutes'] ?? 0) ?> นาที
                                </p>
                            </div>
                            <div class="w-12 h-12 rounded-xl bg-slate-50 dark:bg-slate-700 flex items-center justify-center text-primary-600 dark:text-primary-400 text-2xl">
                                <i class="ph ph-receipt"></i>
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-t border-gray-100 dark:border-slate-700 flex justify-between items-baseline">
                            <span class="text-xs text-gray-400">ยอดรวมปัจจุบัน:</span>
                            <span class="text-xl font-black text-primary-600 dark:text-primary-400">
                                <?= number_format((float)$t['total_amount'], 2) ?> <span class="text-xs font-normal text-gray-400">บาท</span>
                            </span>
                        </div>
                    </div>

                    <button type="button" class="mt-4 w-full py-2 bg-sidebar text-white rounded-xl text-xs font-bold shadow hover:opacity-90 flex items-center justify-center gap-1.5 transition-all">
                        <i class="ph ph-credit-card"></i>
                        <span>เช็คบิลโต๊ะนี้</span>
                    </button>
                </div>
            <?php endforeach; ?>

            <?php if ($activeCount === 0): ?>
                <div class="col-span-full py-12 text-center bg-white dark:bg-slate-800 rounded-2xl border border-gray-100 dark:border-slate-700/60 shadow-sm">
                    <div class="w-12 h-12 mx-auto mb-2 rounded-full bg-emerald-50 dark:bg-emerald-950 flex items-center justify-center text-emerald-500 text-2xl">
                        <i class="ph ph-check"></i>
                    </div>
                    <p class="text-sm font-bold text-gray-700 dark:text-slate-200">ไม่มีโต๊ะที่ค้างชำระเงินในขณะนี้</p>
                    <p class="text-xs text-gray-400 mt-0.5">ทุกโต๊ะอยู่ในสถานะโต๊ะว่างพร้อมรับลูกค้า</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Recent Bills Table -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-100 dark:border-slate-700/60 shadow-sm p-6">
        <h2 class="text-base font-bold text-gray-800 dark:text-slate-100 mb-4 flex items-center gap-2">
            <i class="ph ph-clock-counter-clockwise text-primary-500"></i>
            <span>ประวัติใบเสร็จล่าสุด</span>
        </h2>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600 dark:text-slate-300">
                <thead class="bg-slate-50 dark:bg-slate-700/40 text-xs uppercase text-gray-400">
                    <tr>
                        <th class="py-3 px-4">เลขที่บิล</th>
                        <th class="py-3 px-4">โต๊ะ</th>
                        <th class="py-3 px-4">เวลาชำระเงิน</th>
                        <th class="py-3 px-4">วิธีชำระ</th>
                        <th class="py-3 px-4 text-right">ยอดสุทธิ (บาท)</th>
                        <th class="py-3 px-4 text-center">พิมพ์สลิป</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                    <?php if (empty($recentBills)): ?>
                        <tr>
                            <td colspan="6" class="py-6 text-center text-xs text-gray-400 italic">ยังไม่มีประวัติการชำระเงิน</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($recentBills as $bill): ?>
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-700/30">
                                <td class="py-3 px-4 font-mono font-bold text-gray-800 dark:text-slate-100">
                                    <?= htmlspecialchars($bill['bill_number']) ?>
                                </td>
                                <td class="py-3 px-4 font-semibold">
                                    โต๊ะ <?= htmlspecialchars($bill['table_number']) ?>
                                </td>
                                <td class="py-3 px-4 text-xs text-gray-400">
                                    <?= date('d/m/Y H:i', strtotime($bill['paid_at'])) ?>
                                </td>
                                <td class="py-3 px-4">
                                    <span class="px-2 py-0.5 rounded text-xs font-medium bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-200">
                                        <?= $bill['payment_method'] === 'promptpay' ? 'PromptPay' : ($bill['payment_method'] === 'cash' ? 'เงินสด' : 'บัตรเครดิต') ?>
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-right font-bold text-emerald-600 dark:text-emerald-400">
                                    <?= number_format((float)$bill['grand_total'], 2) ?> บาท
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <a href="/pos/cashier/receipt/<?= $bill['id'] ?>" target="_blank" class="p-1.5 text-gray-400 hover:text-primary-600 transition-colors inline-block" title="พิมพ์ใบเสร็จ">
                                        <i class="ph ph-printer text-lg"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal: Checkout & Payment -->
<div id="checkoutModal" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-800 rounded-3xl max-w-lg w-full max-h-[90vh] overflow-y-auto p-6 shadow-2xl border border-gray-100 dark:border-slate-700 flex flex-col justify-between">
        <div>
            <div class="flex items-center justify-between pb-3 border-b border-gray-100 dark:border-slate-700">
                <div>
                    <h3 class="text-xl font-bold text-gray-800 dark:text-slate-100" id="modalCheckoutTitle">เช็คบิล</h3>
                    <p class="text-xs text-gray-400" id="modalCheckoutZone">โซน: Main</p>
                </div>
                <button onclick="closeCheckoutModal()" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
            </div>

            <!-- Items Ordered List -->
            <div class="my-4 max-h-56 overflow-y-auto space-y-2 pr-1" id="checkoutItemsList">
                <!-- Injected via JS -->
            </div>

            <!-- Financial Calculation Form -->
            <form id="checkoutForm" method="POST" action="/pos/cashier/checkout" class="space-y-3 pt-3 border-t border-gray-100 dark:border-slate-700">
                <input type="hidden" name="csrf_token" value="<?= \App\Core\Security::csrf() ?>">
                <input type="hidden" name="session_id" id="modalSessionId" value="">

                <div class="flex justify-between text-sm text-gray-500 dark:text-slate-400">
                    <span>ยอดรวมค่าอาหาร:</span>
                    <span class="font-bold text-gray-800 dark:text-slate-100" id="calcSubtotalText">0.00 บาท</span>
                </div>

                <div class="flex items-center justify-between gap-4 text-sm">
                    <span class="text-gray-500 dark:text-slate-400">ส่วนลด (บาท):</span>
                    <input type="number" step="0.5" min="0" name="discount_amount" id="calcDiscountInput" value="0" oninput="recalcGrandTotal()" class="w-32 px-3 py-1 bg-gray-50 dark:bg-slate-700 border border-gray-200 dark:border-slate-600 rounded-lg text-right text-sm text-gray-800 dark:text-slate-100">
                </div>

                <div class="flex justify-between items-baseline pt-2 border-t border-gray-100 dark:border-slate-700 text-base font-bold">
                    <span class="text-gray-800 dark:text-slate-100">ยอดชำระสุทธิ:</span>
                    <span class="text-2xl font-black text-emerald-600 dark:text-emerald-400" id="calcGrandTotalText">0.00 บาท</span>
                </div>

                <!-- Payment Method Radios -->
                <div class="pt-3">
                    <label class="block text-xs font-semibold text-gray-500 dark:text-slate-400 mb-2 uppercase">เลือกวิธีชำระเงิน:</label>
                    <div class="grid grid-cols-3 gap-2">
                        <label class="flex flex-col items-center justify-center p-3 rounded-xl border border-gray-200 dark:border-slate-700 cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-700/50 has-[:checked]:border-primary-500 has-[:checked]:bg-primary-50 dark:has-[:checked]:bg-primary-950/40">
                            <input type="radio" name="payment_method" value="cash" checked onchange="togglePromptPayPreview()" class="hidden">
                            <i class="ph ph-money text-2xl text-slate-700 dark:text-slate-200"></i>
                            <span class="text-xs font-bold mt-1 text-slate-700 dark:text-slate-200">เงินสด</span>
                        </label>
                        <label class="flex flex-col items-center justify-center p-3 rounded-xl border border-gray-200 dark:border-slate-700 cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-700/50 has-[:checked]:border-primary-500 has-[:checked]:bg-primary-50 dark:has-[:checked]:bg-primary-950/40">
                            <input type="radio" name="payment_method" value="promptpay" onchange="togglePromptPayPreview()" class="hidden">
                            <i class="ph ph-qr-code text-2xl text-slate-700 dark:text-slate-200"></i>
                            <span class="text-xs font-bold mt-1 text-slate-700 dark:text-slate-200">PromptPay</span>
                        </label>
                        <label class="flex flex-col items-center justify-center p-3 rounded-xl border border-gray-200 dark:border-slate-700 cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-700/50 has-[:checked]:border-primary-500 has-[:checked]:bg-primary-50 dark:has-[:checked]:bg-primary-950/40">
                            <input type="radio" name="payment_method" value="credit_card" onchange="togglePromptPayPreview()" class="hidden">
                            <i class="ph ph-credit-card text-2xl text-slate-700 dark:text-slate-200"></i>
                            <span class="text-xs font-bold mt-1 text-slate-700 dark:text-slate-200">บัตร</span>
                        </label>
                    </div>
                </div>

                <!-- Cashier PromptPay QR Code Display Box -->
                <div id="cashierPromptPayPreview" class="hidden text-center p-3 bg-slate-50 dark:bg-slate-700/40 rounded-2xl border border-dashed border-slate-300 dark:border-slate-600 space-y-2">
                    <p class="text-xs font-bold text-slate-700 dark:text-slate-200 flex items-center justify-center gap-1">
                        <i class="ph ph-qr-code text-primary-500 text-base"></i>
                        <span>สแกน QR PromptPay เพื่อชำระเงิน</span>
                    </p>
                    <div class="bg-white p-2 rounded-xl inline-block shadow-md">
                        <img id="cashierPromptPayQrImg" src="" alt="PromptPay QR" class="w-40 h-40 object-contain mx-auto">
                    </div>
                    <p class="text-[11px] font-semibold text-emerald-600 dark:text-emerald-400" id="cashierPromptPayAmountText">ยอดชำระ: 0.00 บาท</p>
                </div>

                <div class="mt-6 flex items-center justify-end gap-2 pt-2">
                    <button type="button" onclick="closeCheckoutModal()" class="px-4 py-2.5 bg-gray-100 dark:bg-slate-700 text-gray-700 dark:text-slate-300 rounded-xl text-sm font-medium hover:bg-gray-200">
                        ยกเลิก
                    </button>
                    <button type="submit" class="flex-1 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm font-bold shadow flex items-center justify-center gap-2 transition-all">
                        <i class="ph ph-check-circle text-lg"></i>
                        <span>รับชำระเงินและปิดโต๊ะ</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let currentSubtotal = 0;
let currentQrToken = '';

function openCheckoutModal(tableId, tableNumber) {
    document.getElementById('modalCheckoutTitle').textContent = 'เช็คบิล - โต๊ะ ' + tableNumber;
    document.getElementById('checkoutItemsList').innerHTML = '<p class="text-center text-xs text-gray-400 py-4">กำลังโหลดข้อมูล...</p>';
    document.getElementById('checkoutModal').classList.remove('hidden');

    // Reset payment method to cash
    const cashRadio = document.querySelector('input[name="payment_method"][value="cash"]');
    if (cashRadio) cashRadio.checked = true;
    togglePromptPayPreview();

    fetch('/pos/cashier/table/' + tableId)
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            document.getElementById('modalSessionId').value = data.session.id;
            document.getElementById('modalCheckoutZone').textContent = 'โซน: ' + (data.session.zone || 'Main');
            currentQrToken = data.session.qr_token || 'qsr';
            currentSubtotal = data.calculated_total || 0;
            document.getElementById('calcSubtotalText').textContent = currentSubtotal.toFixed(2) + ' บาท';
            document.getElementById('calcDiscountInput').value = '0';
            recalcGrandTotal();

            let itemsHtml = '';
            data.orders.forEach(o => {
                o.items.forEach(it => {
                    let optText = it.options && it.options.length > 0 ? it.options.map(x => x.option_name).join(', ') : '';
                    itemsHtml += `
                        <div class="flex items-center justify-between text-xs py-1.5 border-b border-gray-50 dark:border-slate-700/40">
                            <div>
                                <span class="font-bold text-gray-700 dark:text-slate-200">${it.quantity}x ${it.name_th}</span>
                                ${optText ? `<span class="text-gray-400 block text-[10px]">${optText}</span>` : ''}
                            </div>
                            <span class="font-mono text-gray-700 dark:text-slate-200 font-semibold">${parseFloat(it.subtotal).toFixed(2)} บาท</span>
                        </div>
                    `;
                });
            });

            document.getElementById('checkoutItemsList').innerHTML = itemsHtml || '<p class="text-center text-xs text-gray-400 py-4">ไม่มีรายการอาหาร</p>';
        } else {
            alert('ไม่พบข้อมูลการสั่งอาหาร');
            closeCheckoutModal();
        }
    });
}

function recalcGrandTotal() {
    const discount = parseFloat(document.getElementById('calcDiscountInput').value) || 0;
    const grand = Math.max(0, currentSubtotal - discount);
    document.getElementById('calcGrandTotalText').textContent = grand.toFixed(2) + ' บาท';
    document.getElementById('cashierPromptPayAmountText').textContent = 'ยอดชำระ: ' + grand.toFixed(2) + ' บาท';
}

function togglePromptPayPreview() {
    const selectedMethod = document.querySelector('input[name="payment_method"]:checked')?.value;
    const previewBox = document.getElementById('cashierPromptPayPreview');
    const qrImg = document.getElementById('cashierPromptPayQrImg');

    if (selectedMethod === 'promptpay' && currentQrToken) {
        qrImg.src = '/order/promptpay-qr/' + currentQrToken + '?t=' + Date.now();
        previewBox.classList.remove('hidden');
    } else {
        previewBox.classList.add('hidden');
    }
}

function closeCheckoutModal() {
    document.getElementById('checkoutModal').classList.add('hidden');
}
</script>
