<div class="space-y-6">
    <!-- Header with Date Filters -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-slate-100 flex items-center gap-2">
                <i class="ph ph-chart-line-up text-primary-500"></i>
                <span>รายงานยอดขายและสถิติอาหาร</span>
            </h1>
            <p class="text-sm text-gray-500 dark:text-slate-400 mt-1">
                สรุปข้อมูลยอดขาย วิเคราะห์เมนูขายดี และสถิติการชำระเงิน
            </p>
        </div>

        <!-- Date Range Filter Form -->
        <form method="GET" action="/pos/reports" class="flex flex-wrap items-center gap-2">
            <div class="flex items-center gap-1.5 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 px-3 py-1.5 rounded-xl shadow-sm text-xs">
                <span class="text-gray-400">วันที่:</span>
                <input type="date" name="from_date" value="<?= htmlspecialchars($fromDate) ?>" class="bg-transparent text-gray-700 dark:text-slate-200 focus:outline-none">
                <span class="text-gray-400">ถึง</span>
                <input type="date" name="to_date" value="<?= htmlspecialchars($toDate) ?>" class="bg-transparent text-gray-700 dark:text-slate-200 focus:outline-none">
            </div>
            <button type="submit" class="px-4 py-2 bg-sidebar text-white rounded-xl text-xs font-semibold shadow hover:opacity-90 flex items-center gap-1">
                <i class="ph ph-funnel"></i>
                <span>กรองข้อมูล</span>
            </button>
            <a href="/pos/reports?from_date=<?= date('Y-m-d') ?>&to_date=<?= date('Y-m-d') ?>" class="px-3 py-2 bg-gray-100 dark:bg-slate-800 text-gray-600 dark:text-slate-300 rounded-xl text-xs font-semibold hover:bg-gray-200">
                วันนี้
            </a>
            <a href="/pos/reports?from_date=<?= date('Y-m-d', strtotime('-6 days')) ?>&to_date=<?= date('Y-m-d') ?>" class="px-3 py-2 bg-gray-100 dark:bg-slate-800 text-gray-600 dark:text-slate-300 rounded-xl text-xs font-semibold hover:bg-gray-200">
                7 วันล่าสุด
            </a>
        </form>
    </div>

    <!-- 4 Key Stat Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-slate-800 p-5 rounded-2xl border border-gray-100 dark:border-slate-700/60 shadow-sm flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 dark:bg-emerald-950 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-2xl">
                <i class="ph ph-coins"></i>
            </div>
            <div>
                <p class="text-xs text-gray-400 dark:text-slate-500">ยอดขายรวม</p>
                <p class="text-xl font-black text-gray-800 dark:text-slate-100">
                    <?= number_format((float)$stats['total_sales'], 2) ?> <span class="text-xs font-normal text-gray-400">บาท</span>
                </p>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-800 p-5 rounded-2xl border border-gray-100 dark:border-slate-700/60 shadow-sm flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl bg-blue-50 dark:bg-blue-950 text-blue-600 dark:text-blue-400 flex items-center justify-center text-2xl">
                <i class="ph ph-receipt"></i>
            </div>
            <div>
                <p class="text-xs text-gray-400 dark:text-slate-500">จำนวนบิลทั้งหมด</p>
                <p class="text-xl font-black text-gray-800 dark:text-slate-100">
                    <?= (int)$stats['total_bills'] ?> <span class="text-xs font-normal text-gray-400">บิล</span>
                </p>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-800 p-5 rounded-2xl border border-gray-100 dark:border-slate-700/60 shadow-sm flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl bg-amber-50 dark:bg-amber-950 text-amber-600 dark:text-amber-400 flex items-center justify-center text-2xl">
                <i class="ph ph-calculator"></i>
            </div>
            <div>
                <p class="text-xs text-gray-400 dark:text-slate-500">ยอดเฉลี่ยต่อบิล</p>
                <p class="text-xl font-black text-gray-800 dark:text-slate-100">
                    <?= number_format((float)$stats['avg_bill'], 2) ?> <span class="text-xs font-normal text-gray-400">บาท</span>
                </p>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-800 p-5 rounded-2xl border border-gray-100 dark:border-slate-700/60 shadow-sm flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl bg-purple-50 dark:bg-purple-950 text-purple-600 dark:text-purple-400 flex items-center justify-center text-2xl">
                <i class="ph ph-cooking-pot"></i>
            </div>
            <div>
                <p class="text-xs text-gray-400 dark:text-slate-500">อาหารที่เสิร์ฟไปแล้ว</p>
                <p class="text-xl font-black text-gray-800 dark:text-slate-100">
                    <?= (int)$stats['total_dishes'] ?> <span class="text-xs font-normal text-gray-400">จาน</span>
                </p>
            </div>
        </div>
    </div>

    <!-- Top Selling Dishes & Payment Breakdown -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Top 5 Best Sellers -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-100 dark:border-slate-700/60 shadow-sm p-6">
            <h2 class="text-base font-bold text-gray-800 dark:text-slate-100 mb-4 flex items-center gap-2">
                <i class="ph ph-trophy text-amber-500"></i>
                <span>5 อันดับเมนูขายดีที่สุด</span>
            </h2>

            <?php if (empty($topItems)): ?>
                <p class="text-xs text-gray-400 text-center py-10">ยังไม่มีข้อมูลการขายในรอบที่เลือก</p>
            <?php else: ?>
                <div class="space-y-3">
                    <?php 
                        $rank = 1;
                        foreach ($topItems as $item): 
                            $rankColor = $rank === 1 ? 'bg-amber-500 text-white' : ($rank === 2 ? 'bg-slate-300 text-slate-800' : ($rank === 3 ? 'bg-amber-700 text-white' : 'bg-slate-100 text-slate-600'));
                    ?>
                        <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50/60 dark:bg-slate-700/30 border border-gray-100 dark:border-slate-700/60">
                            <div class="flex items-center space-x-3">
                                <span class="w-6 h-6 rounded-full <?= $rankColor ?> flex items-center justify-center font-bold text-xs">
                                    <?= $rank++ ?>
                                </span>
                                <div>
                                    <h4 class="text-sm font-bold text-gray-800 dark:text-slate-100">
                                        <?= htmlspecialchars($item['name_th']) ?>
                                    </h4>
                                    <p class="text-xs text-gray-400">
                                        ขายได้: <span class="font-bold text-gray-700 dark:text-slate-300"><?= (int)$item['sold_qty'] ?></span> จาน
                                    </p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="text-sm font-black text-primary-600 dark:text-primary-400">
                                    <?= number_format((float)$item['total_revenue'], 2) ?>
                                </span>
                                <span class="text-[11px] text-gray-400 block">บาท</span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Payment Method Breakdown -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-100 dark:border-slate-700/60 shadow-sm p-6">
            <h2 class="text-base font-bold text-gray-800 dark:text-slate-100 mb-4 flex items-center gap-2">
                <i class="ph ph-wallet text-emerald-500"></i>
                <span>สัดส่วนช่องทางชำระเงิน</span>
            </h2>

            <?php if (empty($paymentBreakdown)): ?>
                <p class="text-xs text-gray-400 text-center py-10">ยังไม่มีข้อมูลการชำระเงินในรอบที่เลือก</p>
            <?php else: ?>
                <div class="space-y-4">
                    <?php 
                        $totalSalesAmount = (float)$stats['total_sales'] ?: 1;
                        foreach ($paymentBreakdown as $pay): 
                            $percent = round(($pay['total_amount'] / $totalSalesAmount) * 100, 1);
                            $payLabel = $pay['payment_method'] === 'promptpay' ? 'PromptPay (สแกนจ่าย)' : ($pay['payment_method'] === 'cash' ? 'เงินสด' : 'บัตรเครดิต');
                            $payIcon = $pay['payment_method'] === 'promptpay' ? 'ph-qr-code text-blue-500' : ($pay['payment_method'] === 'cash' ? 'ph-money text-emerald-500' : 'ph-credit-card text-purple-500');
                    ?>
                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between text-xs">
                                <span class="font-bold text-gray-700 dark:text-slate-200 flex items-center gap-1.5">
                                    <i class="ph <?= $payIcon ?> text-base"></i>
                                    <span><?= $payLabel ?></span>
                                    <span class="text-gray-400 font-normal">(<?= (int)$pay['bill_count'] ?> บิล)</span>
                                </span>
                                <span class="font-bold text-gray-800 dark:text-slate-100">
                                    <?= number_format((float)$pay['total_amount'], 2) ?> บาท (<?= $percent ?>%)
                                </span>
                            </div>
                            <div class="w-full bg-slate-100 dark:bg-slate-700 h-2.5 rounded-full overflow-hidden">
                                <div class="bg-primary-500 h-full rounded-full transition-all duration-500" style="width: <?= $percent ?>%"></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Completed Bills Detail Table -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-100 dark:border-slate-700/60 shadow-sm p-6">
        <h2 class="text-base font-bold text-gray-800 dark:text-slate-100 mb-4 flex items-center gap-2">
            <i class="ph ph-receipt text-primary-500"></i>
            <span>รายการบิลที่ปิดการขายแล้วในช่วงเวลานี้</span>
        </h2>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600 dark:text-slate-300">
                <thead class="bg-slate-50 dark:bg-slate-700/40 text-xs uppercase text-gray-400">
                    <tr>
                        <th class="py-3 px-4">เลขที่บิล</th>
                        <th class="py-3 px-4">โต๊ะ / จุดสั่ง</th>
                        <th class="py-3 px-4">เวลาชำระเงิน</th>
                        <th class="py-3 px-4">วิธีชำระ</th>
                        <th class="py-3 px-4 text-right">ยอดสุทธิ (บาท)</th>
                        <th class="py-3 px-4 text-center">ดูสลิป</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                    <?php if (empty($bills)): ?>
                        <tr>
                            <td colspan="6" class="py-8 text-center text-xs text-gray-400 italic">ไม่พบข้อมูลบิลในช่วงเวลานี้</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($bills as $b): ?>
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-700/30">
                                <td class="py-3 px-4 font-mono font-bold text-gray-800 dark:text-slate-100">
                                    <?= htmlspecialchars($b['bill_number']) ?>
                                </td>
                                <td class="py-3 px-4 font-semibold">
                                    โต๊ะ <?= htmlspecialchars($b['table_number']) ?>
                                </td>
                                <td class="py-3 px-4 text-xs text-gray-400">
                                    <?= date('d/m/Y H:i', strtotime($b['paid_at'])) ?>
                                </td>
                                <td class="py-3 px-4">
                                    <span class="px-2 py-0.5 rounded text-xs font-medium bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-200">
                                        <?= $b['payment_method'] === 'promptpay' ? 'PromptPay' : ($b['payment_method'] === 'cash' ? 'เงินสด' : 'บัตรเครดิต') ?>
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-right font-black text-emerald-600 dark:text-emerald-400">
                                    <?= number_format((float)$b['grand_total'], 2) ?> บาท
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <a href="/pos/cashier/receipt/<?= $b['id'] ?>" target="_blank" class="p-1.5 text-gray-400 hover:text-primary-600 transition-colors inline-block" title="พิมพ์ใบเสร็จ">
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
