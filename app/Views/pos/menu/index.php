<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-slate-100 flex items-center gap-2">
                <i class="ph ph-bowl-food text-primary-500"></i>
                <span><?= __('pos.menu_management') ?></span>
            </h1>
            <p class="text-sm text-gray-500 dark:text-slate-400 mt-1">
                จัดการรายการอาหาร หมวดหมู่ ราคา และสลับสถานะของหมดได้ทันที
            </p>
        </div>
        <div class="flex items-center gap-2">
            <button onclick="openCategoryModal()" class="px-4 py-2 bg-gray-100 dark:bg-slate-700 text-gray-700 dark:text-slate-200 rounded-lg hover:bg-gray-200 transition-all flex items-center gap-2 text-sm font-medium">
                <i class="ph ph-folder-plus text-lg"></i>
                <span>จัดการหมวดหมู่</span>
            </button>
            <button onclick="openAddMenuModal()" class="px-4 py-2 bg-sidebar text-white rounded-lg shadow hover:opacity-90 transition-all flex items-center gap-2 text-sm font-medium">
                <i class="ph ph-plus-circle text-lg"></i>
                <span><?= __('pos.add_menu') ?></span>
            </button>
        </div>
    </div>

    <!-- Category Filter Tabs -->
    <div class="flex items-center gap-2 overflow-x-auto pb-2 border-b border-gray-100 dark:border-slate-800">
        <button onclick="filterCategory('all')" class="cat-pill px-4 py-1.5 rounded-full text-xs font-semibold bg-sidebar text-white shadow-sm transition-all" data-category="all">
            ทั้งหมด (<?= count($menuItems) ?>)
        </button>
        <?php foreach ($categories as $cat): ?>
            <button onclick="filterCategory(<?= $cat['id'] ?>)" class="cat-pill px-4 py-1.5 rounded-full text-xs font-semibold bg-gray-100 dark:bg-slate-800 text-gray-600 dark:text-slate-300 hover:bg-gray-200 dark:hover:bg-slate-700 transition-all" data-category="<?= $cat['id'] ?>">
                <?= htmlspecialchars($cat['name_th']) ?> (<?= (int)$cat['item_count'] ?>)
            </button>
        <?php endforeach; ?>
    </div>

    <!-- Menu Items Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5" id="menuGrid">
        <?php foreach ($menuItems as $item): ?>
            <div class="menu-card bg-white dark:bg-slate-800 rounded-2xl border border-gray-100 dark:border-slate-700/60 shadow-sm overflow-hidden flex flex-col justify-between transition-all hover:shadow-md" data-cat-id="<?= $item['category_id'] ?>">
                <div>
                    <!-- Food Image Container -->
                    <div class="relative h-44 bg-slate-100 dark:bg-slate-900 overflow-hidden">
                        <?php if (!empty($item['image_url'])): ?>
                            <img src="<?= htmlspecialchars($item['image_url']) ?>" alt="<?= htmlspecialchars($item['name_th']) ?>" class="w-full h-full object-cover transition-transform duration-300 hover:scale-105">
                        <?php else: ?>
                            <div class="w-full h-full flex items-center justify-center text-slate-300 dark:text-slate-600 text-4xl">
                                <i class="ph ph-cooking-pot"></i>
                            </div>
                        <?php endif; ?>

                        <!-- Recommended Badge -->
                        <?php if ($item['is_recommended']): ?>
                            <span class="absolute top-2 left-2 px-2.5 py-1 bg-amber-500 text-white text-[10px] font-bold uppercase rounded-lg shadow-md flex items-center gap-1">
                                <i class="ph ph-star-fill text-xs"></i> เมนูแนะนำ
                            </span>
                        <?php endif; ?>

                        <!-- Availability Badge -->
                        <span id="stock-badge-<?= $item['id'] ?>" class="absolute top-2 right-2 px-2.5 py-1 text-[10px] font-bold rounded-lg shadow-md <?= $item['is_available'] ? 'bg-emerald-500 text-white' : 'bg-rose-500 text-white' ?>">
                            <?= $item['is_available'] ? 'มีจำหน่าย' : 'ของหมด' ?>
                        </span>
                    </div>

                    <!-- Content -->
                    <div class="p-4">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <span class="text-[11px] text-gray-400 dark:text-slate-500 uppercase tracking-wider font-semibold">
                                    <?= htmlspecialchars($item['category_name_th'] ?? '') ?>
                                </span>
                                <h3 class="text-base font-bold text-gray-800 dark:text-slate-100 mt-0.5 line-clamp-1">
                                    <?= htmlspecialchars($item['name_th']) ?>
                                </h3>
                                <p class="text-xs text-gray-400 dark:text-slate-500 italic line-clamp-1">
                                    <?= htmlspecialchars($item['name_en'] ?? '') ?>
                                </p>
                            </div>
                            <div class="text-right whitespace-nowrap">
                                <span class="text-lg font-black text-primary-600 dark:text-primary-400">
                                    <?= number_format((float)$item['price'], 2) ?>
                                </span>
                                <span class="text-xs text-gray-400">บาท</span>
                            </div>
                        </div>

                        <?php if (!empty($item['description_th'])): ?>
                            <p class="text-xs text-gray-500 dark:text-slate-400 mt-2 line-clamp-2">
                                <?= htmlspecialchars($item['description_th']) ?>
                            </p>
                        <?php endif; ?>

                        <?php if ((int)$item['options_count'] > 0): ?>
                            <div class="mt-3 flex items-center gap-1 text-[11px] text-primary-600 dark:text-primary-400 font-medium">
                                <i class="ph ph-sliders-horizontal"></i>
                                <span>มี <?= (int)$item['options_count'] ?> ตัวเลือกเพิ่มเติม</span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Card Footer Controls -->
                <div class="p-4 pt-0 border-t border-gray-50 dark:border-slate-700/50 mt-2 flex items-center justify-between">
                    <!-- Toggle In-Stock Button -->
                    <button type="button" onclick="toggleStock(<?= $item['id'] ?>)" id="stock-btn-<?= $item['id'] ?>" class="text-xs font-semibold px-3 py-1.5 rounded-lg border transition-all flex items-center gap-1.5 <?= $item['is_available'] ? 'border-gray-200 dark:border-slate-700 text-gray-600 dark:text-slate-300 hover:bg-rose-50 hover:text-rose-600 hover:border-rose-200' : 'border-emerald-200 bg-emerald-50 text-emerald-700 dark:bg-emerald-950 dark:border-emerald-800' ?>">
                        <i class="ph ph-power text-sm"></i>
                        <span><?= $item['is_available'] ? 'กดเมื่อหมด' : 'เปิดขาย' ?></span>
                    </button>

                    <!-- Edit / Delete -->
                    <div class="flex items-center gap-1">
                        <button type="button" onclick="openEditMenuModal(<?= htmlspecialchars(json_encode($item)) ?>)" class="p-1.5 text-gray-400 hover:text-gray-700 dark:hover:text-slate-200 rounded-lg transition-colors">
                            <i class="ph ph-pencil-simple text-base"></i>
                        </button>
                        <button type="button" onclick="confirmDeleteMenu(<?= $item['id'] ?>, '<?= htmlspecialchars($item['name_th']) ?>')" class="p-1.5 text-gray-400 hover:text-rose-500 rounded-lg transition-colors">
                            <i class="ph ph-trash text-base"></i>
                        </button>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Modal: Add / Edit Menu Item -->
<div id="menuModal" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-800 rounded-2xl max-w-lg w-full max-h-[90vh] overflow-y-auto p-6 shadow-2xl border border-gray-100 dark:border-slate-700">
        <h3 class="text-lg font-bold text-gray-800 dark:text-slate-100 mb-4" id="menuModalTitle">เพิ่มเมนูอาหารใหม่</h3>
        
        <form id="menuForm" method="POST" action="/pos/menu/create" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= \App\Core\Security::csrf() ?>">
            <div class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-slate-300 mb-1">หมวดหมู่อาหาร <span class="text-rose-500">*</span></label>
                        <select name="category_id" id="modalMenuCategory" required class="w-full px-3 py-2 bg-gray-50 dark:bg-slate-700/50 border border-gray-200 dark:border-slate-600 rounded-lg text-sm text-gray-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-primary-500">
                            <option value="">-- เลือกหมวดหมู่ --</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name_th']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-slate-300 mb-1">ราคา (บาท) <span class="text-rose-500">*</span></label>
                        <input type="number" step="0.5" name="price" id="modalMenuPrice" required min="0" placeholder="เช่น 69" class="w-full px-3 py-2 bg-gray-50 dark:bg-slate-700/50 border border-gray-200 dark:border-slate-600 rounded-lg text-sm text-gray-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-primary-500">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-slate-300 mb-1">ชื่ออาหาร (ภาษาไทย) <span class="text-rose-500">*</span></label>
                    <input type="text" name="name_th" id="modalMenuNameTh" required placeholder="เช่น ข้าวกะเพราหมูกรอบ" class="w-full px-3 py-2 bg-gray-50 dark:bg-slate-700/50 border border-gray-200 dark:border-slate-600 rounded-lg text-sm text-gray-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-slate-300 mb-1">ชื่ออาหาร (English)</label>
                    <input type="text" name="name_en" id="modalMenuNameEn" placeholder="e.g. Crispy Pork Basil Rice" class="w-full px-3 py-2 bg-gray-50 dark:bg-slate-700/50 border border-gray-200 dark:border-slate-600 rounded-lg text-sm text-gray-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-slate-300 mb-1">รายละเอียด / ส่วนผสม</label>
                    <textarea name="description_th" id="modalMenuDescTh" rows="2" placeholder="รสชาติเผ็ดจัดจ้าน เสิร์ฟพร้อมน้ำซุป..." class="w-full px-3 py-2 bg-gray-50 dark:bg-slate-700/50 border border-gray-200 dark:border-slate-600 rounded-lg text-sm text-gray-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-primary-500"></textarea>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-slate-300 mb-1">URL รูปภาพ หรืออัปโหลดไฟล์</label>
                    <input type="url" name="image_url" id="modalMenuImageUrl" placeholder="https://example.com/photo.jpg" class="w-full px-3 py-2 bg-gray-50 dark:bg-slate-700/50 border border-gray-200 dark:border-slate-600 rounded-lg text-sm text-gray-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-primary-500 mb-2">
                    <input type="file" name="image_file" accept="image/*" class="w-full text-xs text-gray-500 dark:text-slate-400 file:mr-2 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                </div>

                <div class="flex items-center gap-6 pt-2">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_available" id="modalMenuIsAvailable" value="1" checked class="rounded text-primary-600 focus:ring-primary-500">
                        <span class="text-sm text-gray-700 dark:text-slate-300">มีจำหน่าย</span>
                    </label>

                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_recommended" id="modalMenuIsRecommended" value="1" class="rounded text-primary-600 focus:ring-primary-500">
                        <span class="text-sm text-gray-700 dark:text-slate-300">เมนูแนะนำ</span>
                    </label>
                </div>
            </div>

            <div class="mt-6 flex items-center justify-end gap-2">
                <button type="button" onclick="closeMenuModal()" class="px-4 py-2 bg-gray-100 dark:bg-slate-700 text-gray-700 dark:text-slate-300 rounded-lg text-sm font-medium hover:bg-gray-200">
                    ยกเลิก
                </button>
                <button type="submit" class="px-5 py-2 bg-sidebar text-white rounded-lg text-sm font-medium hover:opacity-90">
                    บันทึก
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Category Management -->
<div id="categoryModal" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-800 rounded-2xl max-w-md w-full p-6 shadow-2xl border border-gray-100 dark:border-slate-700">
        <h3 class="text-lg font-bold text-gray-800 dark:text-slate-100 mb-4">จัดการหมวดหมู่อาหาร</h3>

        <!-- Add Category Form -->
        <form method="POST" action="/pos/menu/categories/create" class="mb-6 pb-4 border-b border-gray-100 dark:border-slate-700">
            <input type="hidden" name="csrf_token" value="<?= \App\Core\Security::csrf() ?>">
            <div class="space-y-3">
                <input type="text" name="name_th" required placeholder="ชื่อหมวดหมู่ (เช่น ก๋วยเตี๋ยว, เมนูเส้น)" class="w-full px-3 py-2 bg-gray-50 dark:bg-slate-700/50 border border-gray-200 dark:border-slate-600 rounded-lg text-sm text-gray-800 dark:text-slate-100">
                <input type="text" name="name_en" placeholder="Category Name (EN)" class="w-full px-3 py-2 bg-gray-50 dark:bg-slate-700/50 border border-gray-200 dark:border-slate-600 rounded-lg text-sm text-gray-800 dark:text-slate-100">
                <button type="submit" class="w-full py-2 bg-sidebar text-white rounded-lg text-sm font-medium hover:opacity-90">
                    + เพิ่มหมวดหมู่
                </button>
            </div>
        </form>

        <!-- Existing Categories List -->
        <div class="space-y-2 max-h-60 overflow-y-auto">
            <p class="text-xs text-gray-400 font-semibold mb-2">หมวดหมู่ที่มีอยู่:</p>
            <?php foreach ($categories as $cat): ?>
                <div class="flex items-center justify-between p-2.5 bg-slate-50 dark:bg-slate-700/40 rounded-lg text-sm">
                    <span class="font-medium text-gray-700 dark:text-slate-200"><?= htmlspecialchars($cat['name_th']) ?> (<?= (int)$cat['item_count'] ?> รายการ)</span>
                    <button type="button" onclick="confirmDeleteCategory(<?= $cat['id'] ?>, '<?= htmlspecialchars($cat['name_th']) ?>')" class="text-rose-500 hover:text-rose-700">
                        <i class="ph ph-trash"></i>
                    </button>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="mt-6 text-right">
            <button type="button" onclick="closeCategoryModal()" class="px-4 py-2 bg-gray-100 dark:bg-slate-700 text-gray-700 dark:text-slate-300 rounded-lg text-sm font-medium hover:bg-gray-200">
                ปิด
            </button>
        </div>
    </div>
</div>

<script>
function filterCategory(catId) {
    document.querySelectorAll('.cat-pill').forEach(btn => {
        if (btn.getAttribute('data-category') == catId) {
            btn.className = 'cat-pill px-4 py-1.5 rounded-full text-xs font-semibold bg-sidebar text-white shadow-sm transition-all';
        } else {
            btn.className = 'cat-pill px-4 py-1.5 rounded-full text-xs font-semibold bg-gray-100 dark:bg-slate-800 text-gray-600 dark:text-slate-300 hover:bg-gray-200 dark:hover:bg-slate-700 transition-all';
        }
    });

    document.querySelectorAll('.menu-card').forEach(card => {
        if (catId === 'all' || card.getAttribute('data-cat-id') == catId) {
            card.style.display = 'flex';
        } else {
            card.style.display = 'none';
        }
    });
}

function toggleStock(itemId) {
    fetch('/pos/menu/' + itemId + '/toggle-stock', {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            const badge = document.getElementById('stock-badge-' + itemId);
            const btn = document.getElementById('stock-btn-' + itemId);
            if (data.is_available == 1) {
                badge.className = 'absolute top-2 right-2 px-2.5 py-1 text-[10px] font-bold rounded-lg shadow-md bg-emerald-500 text-white';
                badge.textContent = 'มีจำหน่าย';
                btn.className = 'text-xs font-semibold px-3 py-1.5 rounded-lg border transition-all flex items-center gap-1.5 border-gray-200 dark:border-slate-700 text-gray-600 dark:text-slate-300 hover:bg-rose-50 hover:text-rose-600 hover:border-rose-200';
                btn.innerHTML = '<i class="ph ph-power text-sm"></i> <span>กดเมื่อหมด</span>';
            } else {
                badge.className = 'absolute top-2 right-2 px-2.5 py-1 text-[10px] font-bold rounded-lg shadow-md bg-rose-500 text-white';
                badge.textContent = 'ของหมด';
                btn.className = 'text-xs font-semibold px-3 py-1.5 rounded-lg border transition-all flex items-center gap-1.5 border-emerald-200 bg-emerald-50 text-emerald-700 dark:bg-emerald-950 dark:border-emerald-800';
                btn.innerHTML = '<i class="ph ph-power text-sm"></i> <span>เปิดขาย</span>';
            }

            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 2000
            });
            Toast.fire({ icon: 'success', title: data.message });
        }
    });
}

function openAddMenuModal() {
    document.getElementById('menuModalTitle').textContent = 'เพิ่มเมนูอาหารใหม่';
    document.getElementById('menuForm').action = '/pos/menu/create';
    document.getElementById('modalMenuCategory').value = '';
    document.getElementById('modalMenuPrice').value = '';
    document.getElementById('modalMenuNameTh').value = '';
    document.getElementById('modalMenuNameEn').value = '';
    document.getElementById('modalMenuDescTh').value = '';
    document.getElementById('modalMenuImageUrl').value = '';
    document.getElementById('modalMenuIsAvailable').checked = true;
    document.getElementById('modalMenuIsRecommended').checked = false;
    document.getElementById('menuModal').classList.remove('hidden');
}

function openEditMenuModal(item) {
    document.getElementById('menuModalTitle').textContent = 'แก้ไขเมนู: ' + item.name_th;
    document.getElementById('menuForm').action = '/pos/menu/' + item.id + '/edit';
    document.getElementById('modalMenuCategory').value = item.category_id;
    document.getElementById('modalMenuPrice').value = item.price;
    document.getElementById('modalMenuNameTh').value = item.name_th;
    document.getElementById('modalMenuNameEn').value = item.name_en || '';
    document.getElementById('modalMenuDescTh').value = item.description_th || '';
    document.getElementById('modalMenuImageUrl').value = item.image_url || '';
    document.getElementById('modalMenuIsAvailable').checked = item.is_available == 1;
    document.getElementById('modalMenuIsRecommended').checked = item.is_recommended == 1;
    document.getElementById('menuModal').classList.remove('hidden');
}

function closeMenuModal() {
    document.getElementById('menuModal').classList.add('hidden');
}

function openCategoryModal() {
    document.getElementById('categoryModal').classList.remove('hidden');
}

function closeCategoryModal() {
    document.getElementById('categoryModal').classList.add('hidden');
}

function confirmDeleteMenu(id, name) {
    Swal.fire({
        title: 'ยืนยันลบเมนู ' + name + '?',
        text: 'เมนูนี้จะถูกลบออกจากรายการอย่างถาวร',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#94a3b8',
        confirmButtonText: 'ใช่, ลบเลย',
        cancelButtonText: 'ยกเลิก'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '/pos/menu/' + id + '/delete';
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

function confirmDeleteCategory(id, name) {
    Swal.fire({
        title: 'ยืนยันลบหมวดหมู่ ' + name + '?',
        text: 'เมนูอาหารทั้งหมดที่อยู่ในหมวดหมู่นี้จะถูกลบไปด้วย!',
        icon: 'error',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#94a3b8',
        confirmButtonText: 'ลบหมวดหมู่นี้',
        cancelButtonText: 'ยกเลิก'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '/pos/menu/categories/' + id + '/delete';
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
