# Project Guidelines & Architecture (Standard Core System)

## 1. System Overview & Core Philosophy
ระบบนี้ออกแบบมาเพื่อเป็น **"ระบบแกนหลัก (Core System) มาตรฐาน"** สำหรับนำไปต่อยอดและเชื่อมต่อกับระบบอื่นๆ ในอนาคต
- **Security First:** เน้นความปลอดภัยสูงสุดในทุกมิติของการพัฒนา
- **Core as a Manager:** ตัวระบบหลักทำหน้าที่เป็นศูนย์กลาง (Manager) คอยควบคุมและเชื่อมต่อกับระบบอื่นๆ ผ่านรูปแบบ Module
- **Highly Modular:** ระบบอื่นๆ ที่จะเข้ามาเสริมจะถูกสร้างเป็น Module แยกอิสระและนำมาเชื่อมต่อ (Plug-in) เข้ากับแกนหลัก

## 2. Core Features (ฟีเจอร์ของระบบแกนหลัก)
ระบบแกนหลักต้องเตรียมพร้อมและประกอบด้วยระบบพื้นฐาน ดังนี้:
1. **User Management:** ระบบจัดการผู้ใช้งาน
2. **Role-Based Access Control (RBAC) & Permissions:** ระบบจัดการบทบาทและสิทธิ์การเข้าถึงอย่างละเอียด
3. **User Profile:** ระบบจัดการข้อมูลส่วนตัวของผู้ใช้งาน
4. **Activity Log:** ระบบบันทึกประวัติการกระทำต่างๆ ของผู้ใช้ในระบบ
5. **Audit Log:** ระบบบันทึกการเปลี่ยนแปลงของข้อมูลระดับลึกเพื่อการตรวจสอบย้อนหลัง
6. **File/Field Management:** ระบบจัดการไฟล์หรือระบบ Field ยืดหยุ่นที่พร้อมรองรับการขยายตัว
7. **System Settings:** ระบบตั้งค่าพื้นฐานของเว็บไซต์ (เช่น ชื่อเว็บ, โลโก้, Favicon, Footer, พื้นหลัง)

## 3. Tech Stack
- **Backend:** PHP 8.4+, Pure PHP MVC (PSR-4 Standard)
- **Database:** MySQL (Strictly use PDO พร้อม Prepared Statements เพื่อความปลอดภัย)
- **Frontend/UI:** DataTables (Server-side), Axios, SweetAlert2
- **Typography:** Google Font **Prompt** (wght 300–700) — รองรับทั้งภาษาไทยและอังกฤษ ใช้เป็นมาตรฐานเดียวของทั้งระบบ

## 4. Directory Structure & Architecture (STRICT RULES)
- **Shared Hosting Compatibility:** โครงสร้างต้องรองรับการ Deploy บน Shared Hosting (เช่น DirectAdmin, cPanel) โดยจะมีการตั้งค่า `.htaccess` ที่ Root เพื่อชี้การทำงานไปที่โฟลเดอร์ `public/` อย่างปลอดภัย
- **Core Folder:** ต้องใช้โฟลเดอร์ `/app` เป็นหลักในการพัฒนา **ห้าม** ใช้หรือสร้างโฟลเดอร์ `/src` เด็ดขาด
- **Modular Architecture:** 
  - ระบบ Core หลัก (User, Role, Log) จะทำงานเป็นตัวจัดการส่วนกลาง
  - ส่วนขยายหรือระบบต่อยอดอื่นๆ ต้องถูกแยกย่อยเป็นโมดูลอิสระ (Independent Module Folders) ภายใน `/app/Modules` เช่น `/app/Modules/Inventory`, `/app/Modules/CRM`
- **Controllers & Services:** ห้ามเขียน Business Logic ที่ซับซ้อนทิ้งไว้ใน Default Controllers ให้แยกการทำงานไปไว้ที่ Services, Actions หรือ Repositories แทนเพื่อให้สามารถนำไปใช้ซ้ำ (Reusable) ได้

## 5. Coding Standards & Security Workflow
- ยึดมาตรฐานการเขียนโค้ด **PSR-4** อย่างเคร่งครัด
- **Security in Mind:** คำนึงถึงความปลอดภัยของระบบเป็นสำคัญในทุกขั้นตอนการเขียนโค้ด (ป้องกัน SQL Injection, XSS, CSRF อย่างเข้มงวด)
- ต้องอ้างอิงและรักษาโครงสร้างโค้ด (Coding Patterns) ให้เป็นมาตรฐานเดียวกันทั้งระบบ Core และ Modules
- ทุกครั้งที่เริ่ม Module หรือฟีเจอร์ใหม่ ให้วิเคราะห์และวางแผน Architecture (รวมถึงผลกระทบด้านความปลอดภัย) ส่งมาให้ตรวจก่อนเริ่มเขียนโค้ดจริง
- **Changelog:** การปรับปรุงฟีเจอร์หลัก (Core Features) ต้องมีการอัปเดตบันทึกการเปลี่ยนแปลงลงในไฟล์ `CHANGELOG.md` เสมอ **(โดยให้บันทึกรายละเอียดเป็นภาษาไทยเท่านั้น)**

## 6. Performance & Data Handling (ประสิทธิภาพและการจัดการข้อมูล)
- **High Performance & Big Data:** ระบบต้องถูกออกแบบมาให้ทำงานรวดเร็วและรองรับการโหลดข้อมูลจำนวนมหาศาล (Large Datasets) ได้โดยไม่กระทบต่อประสบการณ์ผู้ใช้
  - ทางฝั่ง Backend (Database & PHP) ให้ใช้งาน Server-Side Processing เสมอเวลาเรียกข้อมูลตาราง (เช่น DataTables)
  - วางโครงสร้าง Index ของ Database ให้เหมาะสมกับ Query ที่มีการค้นหาบ่อยครั้ง
- **Client-Side Caching (LocalStorage):** 
  - ให้ประยุกต์ใช้ **LocalStorage / SessionStorage** ฝั่ง Frontend เพื่อลดภาระการโหลดข้อมูลซ้ำซ้อนจากฝั่ง Server สำหรับข้อมูลที่ไม่มีการเปลี่ยนแปลงบ่อย (Static/Reference Data)
  - ออกแบบกลไก Cache Invalidation ที่ชาญฉลาด (อัปเดต LocalStorage ใหม่ทันทีเมื่อมีการอัปเดตข้อมูลจาก API)

## 7. Security Standards: ID Encryption (Hashids)
- **ID Obfuscation:** ห้ามเปิดเผย Sequential ID หรือ Primary Key ของฐานข้อมูล (เช่น 1, 2, 3) ให้กับผู้ใช้งานผ่าน UI หรือ URL อย่างเด็ดขาด เพื่อป้องกัน Insecure Direct Object Reference (IDOR) และ Enumeration Attacks
- **Standard Implementation:** ต้องใช้คลาส `\App\Core\Security::encodeId($id)` เพื่อแปลง ID เป็น Hash 8 หลัก (เช่น `GnpPaz9q`) ก่อนส่งออกไปที่ UI (ตาราง, URL, หรือ Value ใน Dropdown)
- **Data Retrieval:** เมื่อรับค่ามาจาก URL หรือ Form (เช่น `$_POST['id']` หรือ `$hash` ใน Route) ต้องทำการถอดรหัสด้วย `\App\Core\Security::decodeId($hash)` ทันที และตรวจสอบความถูกต้อง (Validate) ก่อนนำไป Query ฐานข้อมูล

## 8. URL & Routing Standards (Clean URL)
- **ห้ามใช้ Query String** สำหรับ Navigation หลักของระบบ เช่น `/settings?tab=general` ถือเป็น pattern ที่ไม่ดี
- **ใช้ Clean URL เสมอ:** ทุก section/tab/state ที่ผู้ใช้งานสามารถ navigate ไปได้ ต้องมี URL ของตัวเอง เช่น `/settings/general`, `/settings/login`
- **Route Parameter Pattern:** ใช้ `{param}` ใน Router สำหรับ dynamic segment เช่น `/settings/{tab}`, `/users/{id}/edit`
- **Controller Parameter Matching (PHP 8+):** ตัวแปรรับค่าในเมธอดของ Controller ต้องมีชื่อตรงกับตัวแปรใน Route เสมอ (เช่น Route `{id}` -> `public function edit($id)`) เพื่อป้องกัน Named Parameter Error จาก `call_user_func_array` ใน PHP 8+
- **POST Action URL:** Form ที่ POST ข้อมูลต้องส่งไปที่ URL ที่ตรงกับ resource เช่น `POST /settings/general` แทน `POST /settings?tab=general`
- **Controller Whitelisting:** Controller ที่รับ segment จาก URL ต้อง whitelist ค่าที่ยอมรับได้ก่อนเสมอ เช่น `$tab = in_array($tab, ['general', 'login']) ? $tab : 'general';` เพื่อป้องกันการ manipulate URL

## 9. UI & Typography Standards
- **Font:** ใช้ **Google Font Prompt** เป็นมาตรฐานเดียวทั้งระบบ — ทั้ง layout `app.php` และ `auth.php`
  - Load: `https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap`
  - Tailwind config: `fontFamily: { sans: ['Prompt', 'sans-serif'] }`
  - CSS: `body { font-family: 'Prompt', sans-serif; }`
- **Dark Mode:** ใช้ class-based (`darkMode: 'class'`) เก็บ preference ใน `localStorage.theme` ทุก view ต้องมี `dark:` variant ครบถ้วน
- **Scrollbar:** Custom thin scrollbar (6px) ใช้ `slate-300` / `slate-700` ตาม mode, hover เป็น `blue-500`
- **Icons:** Phosphor Icons (`https://unpkg.com/@phosphor-icons/web`) — ใช้ class `ph ph-{name}` (outline) และ `ph-fill ph-{name}` (filled)
- **Color System:** Primary `#3b82f6` (blue-500), Secondary `#64748b` (slate-500) — ห้ามใช้สีจากนอก palette โดยไม่มีเหตุผล

## 10. Date & Time Formatting Standards
- **Standard Date Format:** ทุกจุดในระบบที่แสดงผลวันที่ ต้องใช้รูปแบบ **`d/m/Y`** (เช่น `30/12/2026`) เป็นมาตรฐานเดียวกันทั้งหมด
- **Datetime Format:** เมื่อต้องการแสดงเวลาด้วย ให้ใช้รูปแบบ **`d/m/Y H:i`** หรือ **`d/m/Y H:i:s`** (เช่น `30/12/2026 15:30:00`)
- **Backend Standard Helper:** 
  - ใช้ฟังก์ชัน helper `formatDate($datetime, $withTime = false, $withSeconds = false)` จาก `app/helpers.php` เสมอ
  - ห้ามเขียน format อื่น เช่น `M d, Y` หรือ `Y-m-d` บนหน้า UI
- **Frontend / DataTables Standard:**
  - ใช้ฟังก์ชัน JavaScript `formatDate(dateStr, withTime, withSeconds)` ในการ render ข้อมูลคอลัมน์วันที่ใน DataTables เสมอ

## 11. Multi-Language (i18n) Standards
- **Languages Supported:** ภาษาไทย (`th`) เป็นภาษาหลัก (Default) และภาษาอังกฤษ (`en`) เป็นภาษารอง
- **Language Storage:** จัดเก็บตัวเลือกภาษาของผู้ใช้ใน Session `$_SESSION['app_locale']` (fallback เป็น `'th'`)
- **Route Switch:** ใช้ Clean URL `GET /lang/{locale}` เพื่อสลับภาษาและ Redirect กลับหน้าเดิมอัตโนมัติ
- **Database Storage:**
  - ข้อมูลการแปลภาษาทั้งหมดถูกเก็บในตาราง `translations` ในฐานข้อมูล (แทนการใช้ไฟล์)
  - ผู้ดูแลระบบสามารถเพิ่มหรือแก้ไขคำแปลได้ทันทีผ่าน UI หน้า Settings -> Translations โดยไม่ต้องแก้ไขโค้ด
- **Translation Usage:**
  - ห้าม Hardcode ข้อความ UI (Labels, Buttons, Breadcrumbs, Flash Messages, Descriptions) ใน Views
  - ต้องเรียกใช้ผ่านฟังก์ชันสากล `__('module.key')` หรือ `__('common.key')` เสมอ
  - รองรับ Parameter แทนที่ placeholder ด้วยเครื่องหมายโคลอน (:) เช่น `__('common.welcome', ['name' => 'Admin'])` จะแทนที่ `:name` ด้วย 'Admin'
- **DataTables Global i18n & DOM:**
  - ห้ามใส่ property `language:` แยกต่างหากใน DataTables initialization ของแต่ละหน้า
  - ระบบมีการตั้งค่า Global Configuration ไว้แล้วใน `app/Views/layouts/app.php` ซึ่งครอบคลุมการแปลภาษา (Th/En) ของ DataTables แบบอัตโนมัติ
  - หากหน้าไหนมีการตั้งค่า DataTables ของตัวเอง (เช่น Custom Search) **ต้องใส่ `dom: 'rt<"flex flex-col md:flex-row justify-between items-center mt-4"lip><"clear">',` เสมอ** เพื่อดัน Length Menu (`l`), Info (`i`), และ Pagination (`p`) ไปอยู่ด้านล่างสุดของตารางให้เป็นมาตรฐานเดียวกันทั้งระบบ

## 12. Dynamic Theme & Tailwind Conventions (UI/UX)
- **Dynamic Palette:** ระบบรองรับการเปลี่ยนธีมแบบ Dynamic โดย `layouts/app.php` จะดึงค่าสีธีมปัจจุบันส่งเข้าไปทับ `primary` ของ Tailwind เสมอ ดังนั้นให้ใช้ class กลุ่ม `primary-*` สำหรับลูกเล่นหรือจุดที่ต้องการเปลี่ยนสีตามธีม
- **Primary Buttons:** ปุ่มกดหลักในแบบฟอร์ม (เช่น Save, Update, Create) **ต้องใช้ class `bg-sidebar` เสมอ** เพื่อให้ปุ่มมีสีตรงกับธีม Sidebar ปัจจุบันอย่างสมบูรณ์แบบ
- **Destructive Buttons:** ปุ่มที่เป็นอันตราย เช่น ลบข้อมูล หรือปิดการใช้งาน (Disable) ให้ใช้สีแดง (เช่น `bg-red-500`) ตามมาตรฐานสากลเสมอ
- **Confirmation Dialogs:** การยืนยันใน Actions ที่อันตราย (เช่น Revoke, Delete) **ห้ามใช้ Native `confirm()` ของเบราว์เซอร์** ให้เรียกใช้ **SweetAlert2 (`Swal.fire`)** เสมอ เพื่อความสวยงามและเป็นมาตรฐาน
- **Active & Hover States (Accents):** สถานะ Active ของแท็บเมนู (Tabs), แถวในตาราง, เมนูย่อย, หรือปุ่มรองต่างๆ ต้องใช้ `text-primary-*` และ `bg-primary-*` (เช่น `hover:bg-primary-50 dark:hover:bg-primary-900/20`) แทนการใช้สีเทา (`slate`) หรือสีฟ้า (`blue`) แบบเจาะจง เพื่อให้ความรู้สึกที่ตอบสนองกับธีมได้อย่างกลมกลืน (Premium Tint)
- **Global Color Scope:** ห้าม Override สีหลักของ Tailwind (เช่น `blue`, `gray`, `slate`) ทั่วทั้งระบบ เพื่อหลีกเลี่ยงผลกระทบต่อ UI ส่วนที่ไม่เกี่ยวข้อง จะมีการแมปแบบ Dynamic เฉพาะสี `primary` และ `sidebar` เท่านั้น
- **Ultra Dark Mode:** สำหรับโหมดกลางคืน (Dark Mode) ในส่วนของ Card หรือ Modal ให้ใช้ Background แบบโปร่งแสงที่ดูลึกและพรีเมียม เช่น `dark:bg-black/20 dark:backdrop-blur-md` หรือ `dark:bg-slate-950`

## 13. Pinned Sidebar Menus (Favorites)
- **UI/UX:** แถบเมนูด้านซ้าย (Sidebar) รองรับฟีเจอร์ "เมนูโปรด (Pinned Menus)" โดยจะมีไอคอนหมุดปรากฏขึ้นเมื่อ Hover ที่เมนูย่อย
- **Storage:** รายการเมนูที่ถูกปักหมุดจะถูกจัดเก็บไว้ใน `localStorage` ของเบราว์เซอร์ผู้ใช้ ในตัวแปร `pinnedMenus` (Array of hrefs)
- **Behavior:** เมนูที่ปักหมุดจะถูก Clone ไปแสดงผลในหมวดหมู่พิเศษด้านบนสุดของ Sidebar แบบ Dynamic ผ่าน JavaScript (`layouts/app.php`)
- **Event Bubbling:** ทุกครั้งที่เขียนโค้ดปุ่มซ้อนในลิงก์ (เช่น ปุ่มปักหมุดที่อยู่ใน <a>) ต้องใช้ `event.stopPropagation()` เสมอ เพื่อป้องกันการเด้งเปลี่ยนหน้าเว็บ
- **Tailwind CDN Limits:** ห้ามใช้ Class ที่พึ่งพากลไกกลุ่มที่ซับซ้อน (เช่น `group-hover/menu`) กับ Element ที่ถูกสร้างด้วย JavaScript ภายหลัง (DOM Injection) ให้หันไปเขียน Custom CSS ในบล็อก `<style>` แทน

## 14. Sidebar Behavior (Collapse / Expand)
- **Feature:** Sidebar สามารถย่อ/ขยายได้เพื่อประหยัดพื้นที่ โดยบันทึกสถานะไว้ใน `localStorage.sidebarCollapsed`
- **Structure Rule:** ข้อความที่อยู่ในเมนู Sidebar (เช่น `<a>...</a>`) **ต้องถูกครอบด้วยแท็ก `<span>` เสมอ** (เช่น `<a><i class="..."></i> <span>Dashboard</span></a>`)
- **Reasoning:** เพื่อให้ระบบสามารถใช้ CSS (`html.sidebar-collapsed span { display: none; }`) ซ่อนเฉพาะข้อความและเหลือแต่ไอคอนได้อย่างสวยงาม

## 15. System Font Size Scaling & Real-Time UI
- **Tailwind Rem Scaling:** ระบบรองรับการขยายขนาดตัวหนังสือ 3 ระดับ (14px, 16px, 18px) โดยอาศัยหลักการตั้งค่า `font-size` พื้นฐานที่แท็ก `<html>` (เช่น `<html class="text-[16px]">`)
- **Global Effect:** ด้วยหลักการนี้ ทำให้ค่า `rem` ทั้งหมดใน Tailwind (เช่น p-4, m-2, text-sm) ถูกยืด/หดตามขนาดที่ผู้ใช้เลือกโดยอัตโนมัติอย่างสมบูรณ์แบบ
- **Real-time Active State:** หน้าจอตั้งค่า (Settings UI) ควรใช้ JavaScript (jQuery) ในการปรับปรุง Active Class (`border-primary-xxx`, `bg-primary-xxx`) ทันทีที่ผู้ใช้คลิกเลือก และต้องมีการจำลอง (Preview) ข้อมูลทันทีโดยไม่ต้องรอให้โหลดหน้าใหม่ (เช่นเปลี่ยน Class บน <html> ทันที)

## 16. PHP 8.1+ Null Safety (Strict Types)
- **String Functions Compatibility:** ใน PHP 8.1+ ฟังก์ชันที่รับพารามิเตอร์เป็นข้อความ (เช่น `htmlspecialchars()`, `preg_match()`) จะโยนข้อผิดพลาด Fatal Error / TypeError หากมีการส่งค่า `NULL` เข้าไป
- **Null Coalescing Operator:** ต้องใช้การเช็คและใส่ค่าเริ่มต้นด้วยโอเปอเรเตอร์ `?? ''` เสมอเวลาแสดงผลหรือใช้งานตัวแปรที่มาจาก Database ซึ่งอาจจะเป็น `NULL` ได้ (เช่น `$session['ip_address'] ?? ''`) เพื่อความสเถียรของระบบ

---

## 🎯 Quick Checklist สำหรับสร้างฟีเจอร์/โมดูลใหม่ (System Standards Checklist)
เพื่อป้องกันการหลุดมาตรฐานของระบบ ทุกครั้งที่สร้างหน้าใหม่ ตารางใหม่ หรือปุ่มใหม่ ให้ตรวจสอบตามรายการต่อไปนี้อย่างเคร่งครัด:

### 1. Routing & Controller (หลังบ้าน)
- [ ] ใช้ **Clean URL** เสมอ (เช่น `/users/create`) ห้ามใช้ Query String เพื่อเปลี่ยนหน้า (เช่น `?page=create`)
- [ ] ชื่อ Parameter ใน Route ต้อง **ตรงกับตัวแปรใน Controller** เสมอ (ป้องกัน PHP 8 Named Argument Error)
- [ ] ป้องกัน CSRF เสมอเมื่อรับ POST (ใช้ `\App\Core\Security::checkCsrf()`)
- [ ] ถอดรหัส ID (`decodeId`) ทันทีเมื่อรับค่าจากหน้าเว็บ และเข้ารหัส ID (`encodeId`) ก่อนส่งออกไปที่หน้าเว็บ

### 2. View & UI (หน้าบ้าน)
- [ ] **ห้าม Hardcode ข้อความภาษา** ให้ใช้ `<?= __('module.key') ?>` ตลอดทั้งหน้า
- [ ] แสดงวันที่ต้องใช้ `formatDate($date)` (ฝั่งเซิร์ฟเวอร์) หรือ `formatDate(data)` (ฝั่ง JS) เท่านั้น (รูปแบบมาตรฐาน `d/m/Y`)
- [ ] **ปุ่ม Primary Action** (เช่น สร้าง, บันทึก) ต้องใช้ Class `bg-sidebar` (เพื่อให้สีเปลี่ยนตามธีมที่ตั้งค่าไว้)
- [ ] **ปุ่มอันตราย** (ลบ, ระงับ) ต้องใช้ `bg-red-500` และต้องแสดงหน้าต่างยืนยันด้วย **SweetAlert2 (`Swal.fire`)** เท่านั้น ห้ามใช้ `confirm()` ปกติ
- [ ] สี Active / Hover ของเมนูหรือแท็บ ให้ใช้กลุ่มสี `primary-*` เท่านั้น (ห้ามใช้สี blue หรือ slate คงที่)

### 3. DataTables (ตารางข้อมูล)
- [ ] ต้องทำเป็น **Server-Side Processing** เสมอ หากมีข้อมูลจำนวนมาก
- [ ] ห้ามใส่การตั้งค่าภาษา (`language`) ยิบย่อยลงไปเอง เพราะระบบจัดการให้ที่ไฟล์ Layout แล้ว
- [ ] **DOM Layout มาตรฐาน:** หากต้องตั้งค่าหน้าตาของ DataTable เอง ต้องใช้ `dom: 'rt<"flex flex-col md:flex-row justify-between items-center mt-4"lip><"clear">',` เสมอ เพื่อจัดระเบียบ Length, Info, Pagination ให้อยู่ด้านล่างตารางอย่างสวยงาม

### 4. Null Safety (PHP 8.1+)
- [ ] ตัวแปรใดที่ดึงจากฐานข้อมูลและอาจมีโอกาสเป็น `null` ต้องครอบด้วย `?? ''` ก่อนนำไปใช้ร่วมกับฟังก์ชันจัดการ String เสมอ
