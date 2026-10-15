<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-slate-100 flex items-center gap-2">
                <i class="ph ph-squares-four text-primary-500"></i>
                <span><?= __('pos.table_management') ?></span>
            </h1>
            <p class="text-sm text-gray-500 dark:text-slate-400 mt-1">
                จัดการผังโต๊ะอาหาร สร้าง QR Code ประจำโต๊ะ และตรวจสอบสถานะโต๊ะแบบ Real-time
            </p>
        </div>
        <div class="flex items-center gap-2">
            <button onclick="openAddTableModal()" class="px-4 py-2 bg-sidebar text-white rounded-lg shadow hover:opacity-90 transition-all flex items-center gap-2 text-sm font-medium">
                <i class="ph ph-plus-circle text-lg"></i>
                <span><?= __('pos.add_table') ?></span>
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-slate-800 p-4 rounded-xl border border-gray-100 dark:border-slate-700/60 shadow-sm flex items-center gap-3">
            <div class="w-12 h-12 rounded-lg bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center text-2xl">
                <i class="ph ph-squares-four"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500 dark:text-slate-400">โต๊ะทั้งหมด</p>
                <p class="text-xl font-bold text-gray-800 dark:text-slate-100"><?= (int)$stats['total'] ?> โต๊ะ</p>
            </div>
        </div>
        <div class="bg-white dark:bg-slate-800 p-4 rounded-xl border border-gray-100 dark:border-slate-700/60 shadow-sm flex items-center gap-3">
            <div class="w-12 h-12 rounded-lg bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-2xl">
                <i class="ph ph-check-circle"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500 dark:text-slate-400"><?= __('pos.status_available') ?></p>
                <p class="text-xl font-bold text-emerald-600 dark:text-emerald-400"><?= (int)$stats['available'] ?> โต๊ะ</p>
            </div>
        </div>
        <div class="bg-white dark:bg-slate-800 p-4 rounded-xl border border-gray-100 dark:border-slate-700/60 shadow-sm flex items-center gap-3">
            <div class="w-12 h-12 rounded-lg bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 flex items-center justify-center text-2xl">
                <i class="ph ph-fork-knife"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500 dark:text-slate-400"><?= __('pos.status_occupied') ?></p>
                <p class="text-xl font-bold text-amber-600 dark:text-amber-400"><?= (int)$stats['occupied'] ?> โต๊ะ</p>
            </div>
        </div>
        <div class="bg-white dark:bg-slate-800 p-4 rounded-xl border border-gray-100 dark:border-slate-700/60 shadow-sm flex items-center gap-3">
            <div class="w-12 h-12 rounded-lg bg-rose-50 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 flex items-center justify-center text-2xl">
                <i class="ph ph-bell-ringing"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500 dark:text-slate-400"><?= __('pos.status_billing') ?></p>
                <p class="text-xl font-bold text-rose-600 dark:text-rose-400"><?= (int)$stats['billing'] ?> โต๊ะ</p>
            </div>
        </div>
    </div>

    <!-- Table Grid Layout -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-100 dark:border-slate-700/60 shadow-sm p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-lg font-bold text-gray-800 dark:text-slate-100 flex items-center gap-2">
                <i class="ph ph-grid-four text-primary-500"></i>
                <span>ผังโต๊ะในร้าน</span>
            </h2>
            <div class="text-xs text-gray-400">
                คลิกที่โต๊ะเพื่อดูตัวเลือกหรือพิมพ์ QR Code
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            <?php foreach ($tables as $t): ?>
                <?php
                    $isAvailable = $t['status'] === 'available';
                    $isOccupied  = $t['status'] === 'occupied';
                    $isBilling   = $t['status'] === 'billing';

                    $borderColor = $isAvailable ? 'border-emerald-200 dark:border-emerald-800/40' : ($isBilling ? 'border-rose-400 dark:border-rose-700 animate-pulse' : 'border-amber-300 dark:border-amber-700/50');
                    $badgeBg     = $isAvailable ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300' : ($isBilling ? 'bg-rose-100 text-rose-700 dark:bg-rose-900/60 dark:text-rose-200' : 'bg-amber-50 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300');
                    $statusText  = $isAvailable ? __('pos.status_available') : ($isBilling ? __('pos.status_billing') : __('pos.status_occupied'));
                ?>
                <div class="rounded-xl border-2 <?= $borderColor ?> bg-slate-50/50 dark:bg-slate-800/60 p-4 transition-all hover:shadow-md flex flex-col justify-between">
                    <div>
                        <div class="flex items-start justify-between">
                            <div>
                                <span class="text-xs font-semibold px-2 py-0.5 rounded-full <?= $badgeBg ?>">
                                    <?= $statusText ?>
                                </span>
                                <h3 class="text-2xl font-black text-gray-800 dark:text-slate-100 mt-2">
                                    <?= htmlspecialchars($t['table_number']) ?>
                                </h3>
                                <p class="text-xs text-gray-400 dark:text-slate-500 mt-0.5">
                                    โซน: <?= htmlspecialchars($t['zone'] ?? 'Main') ?> • <?= (int)$t['seat_capacity'] ?> ที่นั่ง
                                </p>
                            </div>
                            <div class="w-10 h-10 rounded-lg bg-white dark:bg-slate-700 shadow-sm flex items-center justify-center text-slate-700 dark:text-slate-200 cursor-pointer hover:bg-slate-100" onclick="previewQr('<?= htmlspecialchars($t['table_number']) ?>', '<?= $t['qr_token'] ?>')" title="ดู QR Code">
                                <i class="ph ph-qr-code text-2xl"></i>
                            </div>
                        </div>

                        <!-- Current Dining Info -->
                        <?php if (!$isAvailable): ?>
                            <div class="mt-4 pt-3 border-t border-gray-200/60 dark:border-slate-700 text-xs space-y-1">
                                <div class="flex justify-between text-gray-500 dark:text-slate-400">
                                    <span>ออเดอร์สะสม:</span>
                                    <span class="font-bold text-gray-700 dark:text-slate-200"><?= (int)$t['order_count'] ?> รายการ</span>
                                </div>
                                <div class="flex justify-between text-gray-700 dark:text-slate-200 font-medium">
                                    <span>ยอดรวมปัจจุบัน:</span>
                                    <span class="font-bold text-primary-600 dark:text-primary-400"><?= number_format((float)$t['total_amount'], 2) ?> บาท</span>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="mt-4 pt-3 border-t border-dashed border-gray-200 dark:border-slate-700 text-xs text-gray-400 italic">
                                พร้อมรับลูกค้าใหม่
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Action Buttons -->
                    <div class="mt-4 pt-3 border-t border-gray-100 dark:border-slate-700/60 flex items-center justify-between gap-1">
                        <a href="/pos/tables/print-qr/<?= $t['id'] ?>" target="_blank" class="px-2.5 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300 rounded-lg text-xs font-medium flex items-center gap-1 transition-colors" title="พิมพ์การ์ด QR Code สำหรับตั้งโต๊ะ">
                            <i class="ph ph-printer"></i>
                            <span>พิมพ์ QR</span>
                        </a>

                        <div class="flex items-center gap-1">
                            <?php if (!$isAvailable): ?>
                                <button type="button" onclick="confirmResetTable(<?= $t['id'] ?>, '<?= htmlspecialchars($t['table_number']) ?>')" class="px-2 py-1.5 text-xs text-amber-600 dark:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-900/30 rounded-lg transition-colors" title="รีเซ็ตโต๊ะเป็นโต๊ะว่าง">
                                    <i class="ph ph-arrows-counter-clockwise text-base"></i>
                                </button>
                            <?php endif; ?>

                            <button type="button" onclick="openEditTableModal(<?= htmlspecialchars(json_encode($t)) ?>)" class="px-2 py-1.5 text-xs text-gray-500 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700 rounded-lg transition-colors" title="แก้ไขโต๊ะ">
                                <i class="ph ph-pencil-simple text-base"></i>
                            </button>

                            <button type="button" onclick="confirmDeleteTable(<?= $t['id'] ?>, '<?= htmlspecialchars($t['table_number']) ?>')" class="px-2 py-1.5 text-xs text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-900/30 rounded-lg transition-colors" title="ลบโต๊ะ">
                                <i class="ph ph-trash text-base"></i>
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Modal: QR Code Preview -->
<div id="qrModal" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-800 rounded-2xl max-w-sm w-full p-6 text-center shadow-2xl border border-gray-100 dark:border-slate-700">
        <h3 class="text-lg font-bold text-gray-800 dark:text-slate-100" id="qrModalTitle">QR Code ประจำโต๊ะ</h3>
        <p class="text-xs text-gray-400 mt-1">สแกนเพื่อสั่งอาหารผ่านหน้าจอมือถือ</p>

        <div class="my-6 p-4 bg-slate-50 dark:bg-slate-900 rounded-xl flex items-center justify-center">
            <img id="qrModalImg" src="" alt="QR Code" class="w-56 h-56 object-contain">
        </div>

        <div class="flex items-center gap-2">
            <a id="qrModalPrintBtn" href="#" target="_blank" class="flex-1 py-2.5 bg-sidebar text-white rounded-xl text-sm font-medium hover:opacity-90 transition-all flex items-center justify-center gap-2">
                <i class="ph ph-printer text-base"></i>
                <span>พิมพ์การ์ด</span>
            </a>
            <button onclick="closeQrModal()" class="flex-1 py-2.5 bg-gray-100 dark:bg-slate-700 text-gray-700 dark:text-slate-300 rounded-xl text-sm font-medium hover:bg-gray-200 transition-all">
                ปิด
            </button>
        </div>
    </div>
</div>

<!-- Modal: Add / Edit Table -->
<div id="tableModal" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-800 rounded-2xl max-w-md w-full p-6 shadow-2xl border border-gray-100 dark:border-slate-700">
        <h3 class="text-lg font-bold text-gray-800 dark:text-slate-100 mb-4" id="tableModalTitle">เพิ่มโต๊ะใหม่</h3>
        
        <form id="tableForm" method="POST" action="/pos/tables/create">
            <input type="hidden" name="csrf_token" value="<?= \App\Core\Security::csrf() ?>">
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-slate-300 mb-1">หมายเลขโต๊ะ / ชื่อโต๊ะ <span class="text-rose-500">*</span></label>
                    <input type="text" name="table_number" id="modalTableNumber" required placeholder="เช่น T01, A1, โต๊ะ 1" class="w-full px-3 py-2 bg-gray-50 dark:bg-slate-700/50 border border-gray-200 dark:border-slate-600 rounded-lg text-sm text-gray-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-slate-300 mb-1">โซนที่นั่ง</label>
                    <input type="text" name="zone" id="modalZone" placeholder="เช่น ในร้าน, ระเบียง, ชั้น 2" class="w-full px-3 py-2 bg-gray-50 dark:bg-slate-700/50 border border-gray-200 dark:border-slate-600 rounded-lg text-sm text-gray-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-slate-300 mb-1">จำนวนที่นั่ง</label>
                    <input type="number" name="seat_capacity" id="modalCapacity" min="1" max="50" value="4" class="w-full px-3 py-2 bg-gray-50 dark:bg-slate-700/50 border border-gray-200 dark:border-slate-600 rounded-lg text-sm text-gray-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>

                <div id="activeToggleContainer" class="hidden">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_active" id="modalIsActive" value="1" class="rounded text-primary-600 focus:ring-primary-500">
                        <span class="text-sm text-gray-700 dark:text-slate-300">เปิดใช้งานโต๊ะนี้</span>
                    </label>
                </div>
            </div>

            <div class="mt-6 flex items-center justify-end gap-2">
                <button type="button" onclick="closeTableModal()" class="px-4 py-2 bg-gray-100 dark:bg-slate-700 text-gray-700 dark:text-slate-300 rounded-lg text-sm font-medium hover:bg-gray-200">
                    ยกเลิก
                </button>
                <button type="submit" class="px-5 py-2 bg-sidebar text-white rounded-lg text-sm font-medium hover:opacity-90">
                    บันทึก
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function previewQr(tableNumber, token) {
    document.getElementById('qrModalTitle').textContent = 'QR Code - โต๊ะ ' + tableNumber;
    document.getElementById('qrModalImg').src = '/pos/tables/qr/' + token;
    document.getElementById('qrModal').classList.remove('hidden');
}

function closeQrModal() {
    document.getElementById('qrModal').classList.add('hidden');
}

function openAddTableModal() {
    document.getElementById('tableModalTitle').textContent = 'เพิ่มโต๊ะอาหารใหม่';
    document.getElementById('tableForm').action = '/pos/tables/create';
    document.getElementById('modalTableNumber').value = '';
    document.getElementById('modalZone').value = 'Main Zone';
    document.getElementById('modalCapacity').value = '4';
    document.getElementById('activeToggleContainer').classList.add('hidden');
    document.getElementById('tableModal').classList.remove('hidden');
}

function openEditTableModal(table) {
    document.getElementById('tableModalTitle').textContent = 'แก้ไขโต๊ะ ' + table.table_number;
    document.getElementById('tableForm').action = '/pos/tables/' + table.id + '/edit';
    document.getElementById('modalTableNumber').value = table.table_number;
    document.getElementById('modalZone').value = table.zone || '';
    document.getElementById('modalCapacity').value = table.seat_capacity || 4;
    document.getElementById('modalIsActive').checked = table.is_active == 1;
    document.getElementById('activeToggleContainer').classList.remove('hidden');
    document.getElementById('tableModal').classList.remove('hidden');
}

function closeTableModal() {
    document.getElementById('tableModal').classList.add('hidden');
}

function confirmResetTable(id, tableNumber) {
    Swal.fire({
        title: 'ยืนยันรีเซ็ตโต๊ะ ' + tableNumber + '?',
        text: 'การรีเซ็ตจะปิดเซสชันที่เปิดอยู่ และเปลี่ยนสถานะกลับเป็น "โต๊ะว่าง"',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#f59e0b',
        cancelButtonColor: '#94a3b8',
        confirmButtonText: 'ใช่, รีเซ็ตเป็นโต๊ะว่าง',
        cancelButtonText: 'ยกเลิก'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '/pos/tables/' + id + '/reset';
            const csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = 'csrf_token';
            csrf.value = '<?= \App\Core\Security::csrf() ?>';
            form.appendChild(csrf);
            document.body.appendChild(form);
            form.submit();
        }
    });
}

function confirmDeleteTable(id, tableNumber) {
    Swal.fire({
        title: 'ยืนยันลบโต๊ะ ' + tableNumber + '?',
        text: 'ข้อมูลโต๊ะและ QR Code ประจำโต๊ะนี้จะถูกลบออกจากระบบอย่างถาวร',
        icon: 'error',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#94a3b8',
        confirmButtonText: 'ใช่, ลบเลย',
        cancelButtonText: 'ยกเลิก'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '/pos/tables/' + id + '/delete';
            const csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = 'csrf_token';
            csrf.value = '<?= \App\Core\Security::csrf() ?>';
            form.appendChild(csrf);
            document.body.appendChild(form);
            form.submit();
        }
    });
}
</script>
