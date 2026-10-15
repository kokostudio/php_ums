<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>สั่งอาหาร - โต๊ะ <?= htmlspecialchars($table['table_number']) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { font-family: 'Prompt', sans-serif; -webkit-tap-highlight-color: transparent; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 pb-28 antialiased">

    <!-- Top Sticky Mobile Header -->
    <header class="sticky top-0 z-40 bg-white/90 backdrop-blur-md border-b border-slate-200/80 px-4 py-3 shadow-sm">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-2.5">
                <div class="w-10 h-10 rounded-xl bg-amber-500 text-white flex items-center justify-center font-black text-lg shadow-md shadow-amber-500/20">
                    <i class="ph ph-fork-knife"></i>
                </div>
                <div>
                    <div class="flex items-center gap-1.5">
                        <h1 class="text-base font-extrabold text-slate-900 leading-tight">โต๊ะ <?= htmlspecialchars($table['table_number']) ?></h1>
                        <span class="text-[10px] px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 font-bold border border-emerald-200">
                            <?= htmlspecialchars($table['zone'] ?? 'Main') ?>
                        </span>
                    </div>
                    <p class="text-[11px] text-slate-400">สแกนสั่งอาหารผ่านโต๊ะ</p>
                </div>
            </div>

            <!-- Quick Action Links -->
            <div class="flex items-center space-x-1">
                <a href="/order/track/<?= $qr_token ?>" class="px-2.5 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold flex items-center gap-1 transition-colors relative" title="ติดตามสถานะครัว">
                    <i class="ph ph-hourglass-high text-sm"></i>
                    <span>สถานะครัว</span>
                    <?php if ($orderedCount > 0): ?>
                        <span class="w-2 h-2 rounded-full bg-amber-500 absolute -top-0.5 -right-0.5 animate-pulse"></span>
                    <?php endif; ?>
                </a>

                <a href="/order/bill/<?= $qr_token ?>" class="px-2.5 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold flex items-center gap-1 transition-colors" title="สรุปยอดเงิน">
                    <i class="ph ph-receipt text-sm"></i>
                    <span>ยอดเงิน</span>
                </a>
            </div>
        </div>

        <!-- Search Bar -->
        <div class="pt-2 pb-1">
            <div class="relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                    <i class="ph ph-magnifying-glass text-base"></i>
                </span>
                <input type="text" id="menuSearchInput" oninput="filterMenuItems(this.value)" placeholder="ค้นหาเมนูอาหาร..." class="w-full pl-9 pr-8 py-2 bg-slate-100/90 hover:bg-slate-100 focus:bg-white border border-transparent focus:border-amber-400 rounded-xl text-xs font-medium placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-amber-500/20 transition-all">
                <button type="button" id="clearSearchBtn" onclick="clearMenuSearch()" class="hidden absolute inset-y-0 right-0 flex items-center pr-2.5 text-slate-400 hover:text-slate-600">
                    <i class="ph ph-x-circle text-base"></i>
                </button>
            </div>
        </div>

        <!-- Category Horizontal Slider -->
        <div class="flex items-center space-x-2 overflow-x-auto no-scrollbar pt-2 pb-1">
            <button onclick="scrollToCat('all')" class="cat-tab px-3.5 py-1.5 rounded-full text-xs font-bold whitespace-nowrap bg-slate-900 text-white transition-all shadow-sm" data-id="all">
                ทั้งหมด
            </button>
            <?php foreach ($categories as $cat): ?>
                <?php if (!empty($cat['items'])): ?>
                    <button onclick="scrollToCat('cat-<?= $cat['id'] ?>')" class="cat-tab px-3.5 py-1.5 rounded-full text-xs font-bold whitespace-nowrap bg-slate-100 text-slate-600 hover:bg-slate-200 transition-all" data-id="cat-<?= $cat['id'] ?>">
                        <?= htmlspecialchars($cat['name_th']) ?>
                    </button>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </header>

    <!-- Main Menu Content -->
    <main class="px-4 py-4 space-y-6 max-w-lg mx-auto">
        <?php foreach ($categories as $cat): ?>
            <?php if (!empty($cat['items'])): ?>
                <section id="cat-<?= $cat['id'] ?>" class="category-section scroll-mt-28 space-y-3">
                    <div class="flex items-center justify-between">
                        <h2 class="text-base font-extrabold text-slate-800 flex items-center gap-2">
                            <span class="w-2 h-4 rounded-full bg-amber-500 inline-block"></span>
                            <span><?= htmlspecialchars($cat['name_th']) ?></span>
                        </h2>
                        <span class="text-xs text-slate-400 font-medium"><?= count($cat['items']) ?> รายการ</span>
                    </div>

                    <div class="space-y-3">
                        <?php foreach ($cat['items'] as $item): ?>
                            <div class="menu-item-card bg-white rounded-2xl border border-slate-100 p-3 shadow-sm flex items-center gap-3 transition-transform active:scale-[0.99]" data-search-term="<?= htmlspecialchars(mb_strtolower($item['name_th'] . ' ' . ($item['name_en'] ?? '') . ' ' . ($item['description_th'] ?? ''))) ?>">
                                <!-- Food Image -->
                                <div class="w-24 h-24 rounded-xl overflow-hidden flex-shrink-0 bg-slate-100 relative">
                                    <?php if (!empty($item['image_url'])): ?>
                                        <img src="<?= htmlspecialchars($item['image_url']) ?>" alt="<?= htmlspecialchars($item['name_th']) ?>" class="w-full h-full object-cover">
                                    <?php else: ?>
                                        <div class="w-full h-full flex items-center justify-center text-slate-300 text-3xl">
                                            <i class="ph ph-bowl-food"></i>
                                        </div>
                                    <?php endif; ?>

                                    <?php if ($item['is_recommended']): ?>
                                        <span class="absolute top-1 left-1 px-1.5 py-0.5 rounded bg-amber-500 text-white text-[9px] font-black uppercase">
                                            แนะนำ
                                        </span>
                                    <?php endif; ?>
                                </div>

                                <!-- Food Info -->
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-sm font-bold text-slate-900 truncate">
                                        <?= htmlspecialchars($item['name_th']) ?>
                                    </h3>
                                    <?php if (!empty($item['name_en'])): ?>
                                        <p class="text-[11px] text-slate-400 truncate italic">
                                            <?= htmlspecialchars($item['name_en']) ?>
                                        </p>
                                    <?php endif; ?>

                                    <?php if (!empty($item['description_th'])): ?>
                                        <p class="text-[11px] text-slate-500 line-clamp-1 mt-0.5">
                                            <?= htmlspecialchars($item['description_th']) ?>
                                        </p>
                                    <?php endif; ?>

                                    <div class="mt-2 flex items-center justify-between">
                                        <div>
                                            <span class="text-base font-extrabold text-slate-900">
                                                <?= number_format((float)$item['price'], 2) ?>
                                            </span>
                                            <span class="text-xs text-slate-400">บาท</span>
                                        </div>

                                        <button type="button" onclick="openItemOptions(<?= htmlspecialchars(json_encode($item)) ?>)" class="px-3 py-1.5 bg-amber-500 active:bg-amber-600 text-white text-xs font-bold rounded-xl shadow-md shadow-amber-500/20 flex items-center gap-1 transition-all">
                                            <i class="ph ph-plus-bold"></i>
                                            <span>สั่ง</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endif; ?>
        <?php endforeach; ?>

        <!-- Empty Search Result State -->
        <div id="emptySearchMessage" class="hidden text-center py-12 bg-white rounded-3xl p-6 border border-slate-100 shadow-sm space-y-2">
            <div class="w-12 h-12 mx-auto rounded-full bg-slate-100 text-slate-400 flex items-center justify-center text-2xl">
                <i class="ph ph-magnifying-glass"></i>
            </div>
            <h3 class="text-sm font-bold text-slate-700">ไม่พบเมนูที่คุณค้นหา</h3>
            <p class="text-xs text-slate-400">ลองค้นหาด้วยคำอื่น หรือกดล้างการค้นหาเพื่อดูเมนูทั้งหมด</p>
            <button type="button" onclick="clearMenuSearch()" class="mt-2 inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold transition-all">
                <i class="ph ph-arrow-counter-clockwise"></i>
                <span>ดูเมนูทั้งหมด</span>
            </button>
        </div>
    </main>

    <!-- Bottom Floating Cart Bar -->
    <div id="cartFloatingBar" class="hidden fixed bottom-4 inset-x-4 max-w-lg mx-auto z-40">
        <button type="button" onclick="openCartDrawer()" class="w-full bg-slate-900 text-white p-4 rounded-2xl shadow-xl flex items-center justify-between transition-transform active:scale-95">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-amber-500 flex items-center justify-center font-black text-lg relative">
                    <i class="ph ph-shopping-cart-simple"></i>
                    <span id="cartCountBadge" class="absolute -top-1 -right-1 w-5 h-5 bg-rose-500 text-white rounded-full text-[10px] font-bold flex items-center justify-center border-2 border-slate-900">
                        0
                    </span>
                </div>
                <div class="text-left">
                    <p class="text-xs text-slate-400">ตะกร้าอาหารของคุณ</p>
                    <p class="text-sm font-bold" id="cartBarTotal">0.00 บาท</p>
                </div>
            </div>

            <div class="flex items-center space-x-1 text-amber-400 font-bold text-sm">
                <span>ดูรายการ</span>
                <i class="ph ph-caret-right-bold"></i>
            </div>
        </button>
    </div>

    <!-- Modal: Item Options & Customize -->
    <div id="optionsModal" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-end sm:items-center justify-center p-0 sm:p-4">
        <div class="bg-white rounded-t-3xl sm:rounded-3xl max-w-md w-full max-h-[85vh] overflow-y-auto p-5 shadow-2xl flex flex-col justify-between animate-in slide-in-from-bottom duration-200">
            <div>
                <div class="flex items-start justify-between pb-3 border-b border-slate-100">
                    <div class="pr-4">
                        <h3 class="text-lg font-bold text-slate-900" id="optItemName">ชื่ออาหาร</h3>
                        <p class="text-sm font-extrabold text-amber-600" id="optItemBasePrice">0.00 บาท</p>
                    </div>
                    <button onclick="closeItemOptions()" class="w-8 h-8 rounded-full bg-slate-100 text-slate-500 flex items-center justify-center text-lg">&times;</button>
                </div>

                <!-- Dynamic Options Group Container -->
                <div id="optionsContainer" class="py-3 space-y-4">
                    <!-- Injected via JS -->
                </div>

                <!-- Special Note Textarea -->
                <div class="pt-2">
                    <label class="block text-xs font-bold text-slate-600 mb-1">หมายเหตุเพิ่มเติม (ถ้ามี):</label>
                    <input type="text" id="optSpecialNote" placeholder="เช่น เผ็ดน้อย, ไม่ใส่กระเทียม, น้ำซุปแยก..." class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-amber-500">
                </div>
            </div>

            <div class="pt-5 border-t border-slate-100 mt-4 flex items-center justify-between gap-3">
                <!-- Quantity Selector -->
                <div class="flex items-center bg-slate-100 rounded-xl p-1 space-x-2">
                    <button type="button" onclick="changeQty(-1)" class="w-8 h-8 rounded-lg bg-white shadow-sm flex items-center justify-center text-slate-700 font-bold text-base active:bg-slate-200">-</button>
                    <span id="optQty" class="w-6 text-center font-bold text-sm text-slate-800">1</span>
                    <button type="button" onclick="changeQty(1)" class="w-8 h-8 rounded-lg bg-white shadow-sm flex items-center justify-center text-slate-700 font-bold text-base active:bg-slate-200">+</button>
                </div>

                <!-- Add to cart CTA -->
                <button type="button" onclick="confirmAddToCart()" class="flex-1 py-3 bg-amber-500 active:bg-amber-600 text-white rounded-xl font-bold text-sm shadow-md shadow-amber-500/20 flex items-center justify-center gap-1.5 transition-all">
                    <i class="ph ph-shopping-bag-open"></i>
                    <span>เพิ่มลงตะกร้า • </span>
                    <span id="optTotalCalc">0.00 บาท</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Drawer: Cart Review & Submit -->
    <div id="cartDrawer" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-end justify-center">
        <div class="bg-white rounded-t-3xl max-w-lg w-full max-h-[85vh] overflow-y-auto p-5 shadow-2xl flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div>
                        <h3 class="text-lg font-bold text-slate-900">รายการในตะกร้า</h3>
                        <p class="text-xs text-slate-400">โต๊ะ <?= htmlspecialchars($table['table_number']) ?> (<?= htmlspecialchars($table['zone'] ?? 'Main') ?>)</p>
                    </div>
                    <button onclick="closeCartDrawer()" class="w-8 h-8 rounded-full bg-slate-100 text-slate-500 flex items-center justify-center text-lg">&times;</button>
                </div>

                <!-- Cart Items List -->
                <div id="cartItemsList" class="py-3 space-y-3 max-h-60 overflow-y-auto pr-1">
                    <!-- Injected via JS -->
                </div>

                <!-- Dine-in vs Takeaway Selector -->
                <div class="pt-3 pb-2">
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">รูปแบบการรับประทาน:</label>
                    <div class="grid grid-cols-2 gap-2">
                        <label class="flex items-center justify-center gap-2 p-2.5 rounded-xl border border-slate-200 cursor-pointer text-xs font-bold has-[:checked]:border-amber-500 has-[:checked]:bg-amber-50 text-slate-700">
                            <input type="radio" name="cartOrderType" value="dine_in" checked class="text-amber-500 focus:ring-amber-500">
                            <span>🍽️ ทานที่ร้าน</span>
                        </label>
                        <label class="flex items-center justify-center gap-2 p-2.5 rounded-xl border border-slate-200 cursor-pointer text-xs font-bold has-[:checked]:border-amber-500 has-[:checked]:bg-amber-50 text-slate-700">
                            <input type="radio" name="cartOrderType" value="takeaway" class="text-amber-500 focus:ring-amber-500">
                            <span>🛍️ สั่งกลับบ้าน</span>
                        </label>
                    </div>
                </div>

                <!-- Customer overall note -->
                <div class="pt-2">
                    <label class="block text-xs font-bold text-slate-600 mb-1">หมายเหตุสำหรับออเดอร์นี้:</label>
                    <textarea id="cartOrderNote" rows="2" placeholder="เช่น ขอจานชาม ช้อนส้อมเพิ่ม หรือห่อแยกน้ำ..." class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-amber-500"></textarea>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 mt-4 space-y-3">
                <div class="flex justify-between items-baseline">
                    <span class="text-sm text-slate-500">ยอดรวมทั้งสิ้น:</span>
                    <span class="text-2xl font-black text-slate-900" id="cartDrawerTotal">0.00 บาท</span>
                </div>

                <button type="button" onclick="submitOrderToKitchen()" id="btnSubmitOrder" class="w-full py-3.5 bg-emerald-600 active:bg-emerald-700 text-white rounded-2xl font-bold text-base shadow-lg shadow-emerald-600/30 flex items-center justify-center gap-2 transition-all">
                    <i class="ph ph-paper-plane-tilt text-xl"></i>
                    <span>ยืนยันสั่งอาหาร (ส่งเข้าครัว)</span>
                </button>
            </div>
        </div>
    </div>

<script>
const QR_TOKEN = '<?= $qr_token ?>';
let cart = JSON.parse(localStorage.getItem('restaurant_cart_' + QR_TOKEN) || '[]');
let currentCustomizingItem = null;
let currentQty = 1;

document.addEventListener('DOMContentLoaded', () => {
    updateCartUI();
    
    // Check if search parameter exists in URL
    const urlParams = new URLSearchParams(window.location.search);
    const searchParam = urlParams.get('search');
    if (searchParam) {
        const input = document.getElementById('menuSearchInput');
        if (input) {
            input.value = searchParam;
            filterMenuItems(searchParam);
        }
    }
});

function filterMenuItems(query) {
    const q = (query || '').trim().toLowerCase();
    const clearBtn = document.getElementById('clearSearchBtn');
    if (clearBtn) {
        if (q.length > 0) clearBtn.classList.remove('hidden');
        else clearBtn.classList.add('hidden');
    }

    let matchCount = 0;
    document.querySelectorAll('.category-section').forEach(section => {
        let sectionVisibleItems = 0;
        section.querySelectorAll('.menu-item-card').forEach(card => {
            const term = card.getAttribute('data-search-term') || '';
            if (!q || term.includes(q)) {
                card.classList.remove('hidden');
                sectionVisibleItems++;
                matchCount++;
            } else {
                card.classList.add('hidden');
            }
        });

        if (sectionVisibleItems === 0) {
            section.classList.add('hidden');
        } else {
            section.classList.remove('hidden');
        }
    });

    const emptySearchMsg = document.getElementById('emptySearchMessage');
    if (emptySearchMsg) {
        if (matchCount === 0 && q.length > 0) {
            emptySearchMsg.classList.remove('hidden');
        } else {
            emptySearchMsg.classList.add('hidden');
        }
    }
}

function clearMenuSearch() {
    const input = document.getElementById('menuSearchInput');
    if (input) {
        input.value = '';
        filterMenuItems('');
        input.focus();
    }
}

function scrollToCat(id) {
    document.querySelectorAll('.cat-tab').forEach(tab => {
        if (tab.getAttribute('data-id') === id) {
            tab.className = 'cat-tab px-3.5 py-1.5 rounded-full text-xs font-bold whitespace-nowrap bg-slate-900 text-white transition-all shadow-sm';
        } else {
            tab.className = 'cat-tab px-3.5 py-1.5 rounded-full text-xs font-bold whitespace-nowrap bg-slate-100 text-slate-600 hover:bg-slate-200 transition-all';
        }
    });

    if (id === 'all') {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    } else {
        const el = document.getElementById(id);
        if (el) el.scrollIntoView({ behavior: 'smooth' });
    }
}

function openItemOptions(item) {
    currentCustomizingItem = item;
    currentQty = 1;
    document.getElementById('optItemName').textContent = item.name_th;
    document.getElementById('optItemBasePrice').textContent = parseFloat(item.price).toFixed(2) + ' บาท';
    document.getElementById('optQty').textContent = '1';
    document.getElementById('optSpecialNote').value = '';

    const container = document.getElementById('optionsContainer');
    container.innerHTML = '';

    if (item.options && item.options.length > 0) {
        // Group options by group_name
        const groups = {};
        item.options.forEach(opt => {
            if (!groups[opt.group_name]) groups[opt.group_name] = [];
            groups[opt.group_name].push(opt);
        });

        for (const [groupName, opts] of Object.entries(groups)) {
            let groupHtml = `<div class="space-y-1.5"><p class="text-xs font-bold text-slate-700">${groupName}</p><div class="space-y-1">`;
            opts.forEach((opt, idx) => {
                const extra = parseFloat(opt.extra_price) > 0 ? ` (+${parseFloat(opt.extra_price).toFixed(2)} บาท)` : '';
                const isRadio = opt.is_required == 1;
                const inputType = isRadio ? 'radio' : 'checkbox';
                const inputName = isRadio ? `opt_group_${groupName}` : `opt_check_${opt.id}`;
                const checked = isRadio && idx === 0 ? 'checked' : '';

                groupHtml += `
                    <label class="flex items-center justify-between p-2.5 rounded-xl border border-slate-200 hover:bg-slate-50 text-xs cursor-pointer has-[:checked]:border-amber-500 has-[:checked]:bg-amber-50/50">
                        <div class="flex items-center gap-2">
                            <input type="${inputType}" name="${inputName}" value="${opt.id}" data-price="${opt.extra_price}" data-name="${opt.option_name}" ${checked} onchange="calcOptionsPrice()" class="text-amber-500 focus:ring-amber-500">
                            <span class="font-medium text-slate-800">${opt.option_name}</span>
                        </div>
                        <span class="text-slate-400 font-semibold">${extra}</span>
                    </label>
                `;
            });
            groupHtml += '</div></div>';
            container.innerHTML += groupHtml;
        }
    }

    calcOptionsPrice();
    document.getElementById('optionsModal').classList.remove('hidden');
}

function closeItemOptions() {
    document.getElementById('optionsModal').classList.add('hidden');
}

function changeQty(delta) {
    currentQty = Math.max(1, currentQty + delta);
    document.getElementById('optQty').textContent = currentQty;
    calcOptionsPrice();
}

function calcOptionsPrice() {
    if (!currentCustomizingItem) return;
    let unit = parseFloat(currentCustomizingItem.price);

    const checkedInputs = document.querySelectorAll('#optionsContainer input:checked');
    checkedInputs.forEach(inp => {
        unit += parseFloat(inp.getAttribute('data-price') || 0);
    });

    const total = unit * currentQty;
    document.getElementById('optTotalCalc').textContent = total.toFixed(2) + ' บาท';
}

function confirmAddToCart() {
    if (!currentCustomizingItem) return;

    const selectedOptions = [];
    document.querySelectorAll('#optionsContainer input:checked').forEach(inp => {
        selectedOptions.push({
            id: inp.value,
            option_name: inp.getAttribute('data-name'),
            extra_price: parseFloat(inp.getAttribute('data-price') || 0)
        });
    });

    const specialNote = document.getElementById('optSpecialNote').value.trim();

    // Check if duplicate in cart with same options and note
    const cartItem = {
        cart_item_id: Date.now() + '_' + Math.random().toString(36).substr(2, 4),
        id: currentCustomizingItem.id,
        name: currentCustomizingItem.name_th,
        base_price: parseFloat(currentCustomizingItem.price),
        qty: currentQty,
        options: selectedOptions,
        special_note: specialNote
    };

    cart.push(cartItem);
    saveCart();
    closeItemOptions();
    updateCartUI();

    const Toast = Swal.mixin({
        toast: true,
        position: 'bottom',
        showConfirmButton: false,
        timer: 1500
    });
    Toast.fire({ icon: 'success', title: 'เพิ่มลงตะกร้าแล้ว' });
}

function saveCart() {
    localStorage.setItem('restaurant_cart_' + QR_TOKEN, JSON.stringify(cart));
}

function updateCartUI() {
    let count = 0;
    let total = 0;

    cart.forEach(item => {
        count += item.qty;
        let unit = item.base_price;
        if (item.options) {
            item.options.forEach(o => unit += o.extra_price);
        }
        total += unit * item.qty;
    });

    const bar = document.getElementById('cartFloatingBar');
    if (count > 0) {
        bar.classList.remove('hidden');
        document.getElementById('cartCountBadge').textContent = count;
        document.getElementById('cartBarTotal').textContent = total.toFixed(2) + ' บาท';
        document.getElementById('cartDrawerTotal').textContent = total.toFixed(2) + ' บาท';
    } else {
        bar.classList.add('hidden');
    }
}

function openCartDrawer() {
    const list = document.getElementById('cartItemsList');
    list.innerHTML = '';

    if (cart.length === 0) {
        list.innerHTML = '<p class="text-center text-xs text-slate-400 py-6">ไม่มีรายการในตะกร้า</p>';
    } else {
        cart.forEach((it, idx) => {
            let unit = it.base_price;
            let optNames = [];
            if (it.options) {
                it.options.forEach(o => {
                    unit += o.extra_price;
                    optNames.push(o.option_name);
                });
            }
            const sub = unit * it.qty;

            list.innerHTML += `
                <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-between">
                    <div class="flex-1 pr-2">
                        <h4 class="font-bold text-sm text-slate-900">${it.name}</h4>
                        ${optNames.length > 0 ? `<p class="text-[10px] text-slate-400">${optNames.join(', ')}</p>` : ''}
                        ${it.special_note ? `<p class="text-[10px] text-rose-500">หมายเหตุ: ${it.special_note}</p>` : ''}
                        <p class="text-xs font-bold text-slate-700 mt-0.5">${sub.toFixed(2)} บาท</p>
                    </div>
                    <div class="flex items-center space-x-2">
                        <button type="button" onclick="updateCartItemQty(${idx}, -1)" class="w-7 h-7 rounded-lg bg-white border border-slate-200 flex items-center justify-center font-bold text-slate-700 text-xs">-</button>
                        <span class="text-xs font-bold w-4 text-center">${it.qty}</span>
                        <button type="button" onclick="updateCartItemQty(${idx}, 1)" class="w-7 h-7 rounded-lg bg-white border border-slate-200 flex items-center justify-center font-bold text-slate-700 text-xs">+</button>
                        <button type="button" onclick="removeCartItem(${idx})" class="w-7 h-7 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center text-xs ml-1">
                            <i class="ph ph-trash"></i>
                        </button>
                    </div>
                </div>
            `;
        });
    }

    document.getElementById('cartDrawer').classList.remove('hidden');
}

function closeCartDrawer() {
    document.getElementById('cartDrawer').classList.add('hidden');
}

function updateCartItemQty(idx, delta) {
    if (!cart[idx]) return;
    cart[idx].qty += delta;
    if (cart[idx].qty <= 0) {
        cart.splice(idx, 1);
    }
    saveCart();
    updateCartUI();
    openCartDrawer();
}

function removeCartItem(idx) {
    cart.splice(idx, 1);
    saveCart();
    updateCartUI();
    openCartDrawer();
}

function submitOrderToKitchen() {
    if (cart.length === 0) return;

    const note = document.getElementById('cartOrderNote').value.trim();
    const orderType = document.querySelector('input[name="cartOrderType"]:checked')?.value || 'dine_in';
    const btn = document.getElementById('btnSubmitOrder');
    btn.disabled = true;
    btn.innerHTML = '<i class="ph ph-spinner animate-spin text-xl"></i> <span>กำลังส่งออเดอร์...</span>';

    fetch('/order/submit/' + QR_TOKEN, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ items: cart, note: note, order_type: orderType })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            // Clear cart
            cart = [];
            saveCart();
            updateCartUI();
            closeCartDrawer();

            const successMsg = orderType === 'takeaway' 
                ? 'ออเดอร์กลับบ้านส่งเข้าครัวแล้ว กรุณารอฟังเสียงเรียกคิวที่เคาน์เตอร์' 
                : 'ออเดอร์ของคุณถูกส่งไปยังห้องครัวเรียบร้อยแล้ว';

            Swal.fire({
                title: 'สั่งอาหารสำเร็จ!',
                text: successMsg,
                icon: 'success',
                confirmButtonText: 'ติดตามสถานะอาหาร',
                confirmButtonColor: '#059669',
            }).then(() => {
                window.location.href = '/order/track/' + QR_TOKEN;
            });
        } else {
            alert(data.message || 'เกิดข้อผิดพลาด');
            btn.disabled = false;
            btn.innerHTML = '<i class="ph ph-paper-plane-tilt text-xl"></i> <span>ยืนยันสั่งอาหาร (ส่งเข้าครัว)</span>';
        }
    })
    .catch(err => {
        alert('เกิดข้อผิดพลาดในการเชื่อมต่อ');
        btn.disabled = false;
        btn.innerHTML = '<i class="ph ph-paper-plane-tilt text-xl"></i> <span>ยืนยันสั่งอาหาร (ส่งเข้าครัว)</span>';
    });
}
</script>
</body>
</html>
