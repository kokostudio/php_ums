# Project Guidelines & Architecture (Standard Core System)

> [!IMPORTANT]
> **กฎข้อที่ 1: ต้องอ่านกฎทุกครั้งก่อนเริ่มทำงาน (MANDATORY RULES INSPECTION FIRST)**  
> ก่อนเริ่มงาน วิเคราะห์ ออกแบบ เขียนโค้ด หรือปรับปรุงฟีเจอร์ใดๆ นักพัฒนาและ AI Agent **ต้องอ่านและตรวจสอบกฎในไฟล์ `rules.md` และ `GEMINI.md` ทุกครั้งโดยไม่มีข้อยกเว้น** ห้ามเริ่มงานหรือคาดเดาโครงสร้างเองโดยไม่อ่านกฎ เพื่อรักษามาตรฐานสถาปัตยกรรมและความปลอดภัยของระบบ 100%

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

## 6. Performance & Data Handling: Mandatory Server-Side DataTables (ประสิทธิภาพและการจัดการข้อมูลแบบ Server-Side)
- **Mandatory Server-Side Processing (กฎการทำงานของ DataTable ต้องเป็นแบบ Server-Side เสมอ):** ทุกตารางข้อมูลในระบบ (Users, Roles, Activity Logs, Audit Logs, และโมดูลต่อขยายใหม่ทั้งหมด) **ต้องทำงานในรูปแบบ Server-Side Processing (`serverSide: true, processing: true`) เสมอ**
  - **ความสามารถในการรองรับข้อมูลขนาดใหญ่ (Scalability):** เพื่อรองรับการเติบโตของข้อมูลระดับองค์กร ลดภาระหน่วยความจำบนเบราว์เซอร์ของผู้ใช้ และเพิ่มความเร็วในการตอบสนอง โดยการแบ่งหน้า (Pagination), การเรียงลำดับ (Sorting), และการค้นหา (Searching) ต้องถูกประมวลผลบนฐานข้อมูล MySQL โดยตรงผ่านคำสั่ง SQL ที่มีประสิทธิภาพ
  - **การประมวลผลผ่าน `DataTableHelper`:** Controller ต้องมีเมธอด Endpoint สำหรับส่งข้อมูล JSON ให้ตาราง (เช่น `public function data()`) และเรียกใช้ `\App\Core\DataTableHelper::process()` พร้อม Prepared Statements ป้องกัน SQL Injection
  - **ความปลอดภัยของ ID (Hashids):** ข้อมูลทุกแถวที่ส่งออกไปใน JSON จะต้องเข้ารหัส Primary Key เป็น Hash 8 หลักด้วย `\App\Core\Security::encodeId($id)` เสมอ (เช่น `$row['hash_id']`) ห้ามส่ง Sequential ID สดๆ ออกไปที่เบราว์เซอร์
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
- **Form Submit Loading:** ระบบมี Global Loading State อัตโนมัติเมื่อกด Submit ฟอร์ม (ปุ่มจะถูก Disable พร้อมแสดงไอคอนหมุน) หากฟอร์มใดไม่ต้องการเอฟเฟกต์นี้ (เช่น Search Form) ต้องใส่ Attribute `data-no-loading` ไว้ที่แท็ก `<form>` เสมอ

## 10. Date & Time Formatting Standards
- **Standard Date Format:** ทุกจุดในระบบที่แสดงผลวันที่ ต้องใช้รูปแบบ **`d/m/Y`** (เช่น `30/12/2026`) เป็นมาตรฐานเดียวกันทั้งหมด
- **Datetime Format:** เมื่อต้องการแสดงเวลาด้วย ให้ใช้รูปแบบ **`d/m/Y H:i`** หรือ **`d/m/Y H:i:s`** (เช่น `30/12/2026 15:30:00`)
- **Backend Standard Helper:** 
  - ใช้ฟังก์ชัน helper `formatDate($datetime, $withTime = false, $withSeconds = false)` จาก `app/helpers.php` เสมอ
  - ห้ามเขียน format อื่น เช่น `M d, Y` หรือ `Y-m-d` บนหน้า UI
- **Frontend / DataTables Standard:**
  - ใช้ฟังก์ชัน JavaScript `formatDate(dateStr, withTime, withSeconds)` ในการ render ข้อมูลคอลัมน์วันที่ใน DataTables เสมอ

## 11. Multi-Language (i18n) Standards & 100% Parity
- **Languages Supported:** ภาษาไทย (`th`) เป็นภาษาหลัก (Default) และภาษาอังกฤษ (`en`) เป็นภาษารอง
- **Language Storage:** จัดเก็บตัวเลือกภาษาของผู้ใช้ใน Session `$_SESSION['app_locale']` (fallback เป็น `'th'`)
- **Route Switch:** ใช้ Clean URL `GET /lang/{locale}` เพื่อสลับภาษาและ Redirect กลับหน้าเดิมอัตโนมัติ
- **Database Storage:**
  - ข้อมูลการแปลภาษาทั้งหมดถูกเก็บในตาราง `translations` ในฐานข้อมูล
  - ผู้ดูแลระบบสามารถเพิ่มหรือแก้ไขคำแปลได้ทันทีผ่าน UI หน้า Settings -> Translations โดยไม่ต้องแก้ไขโค้ด
- **Complete Localization & Parity Rule (กฎการใส่ภาษาให้ครบ 100%):**
  - **ห้าม Hardcode ข้อความ UI เด็ดขาด:** Labels, Buttons, Breadcrumbs, Flash Messages, Validations, Descriptions, Badges, หรือข้อความ Alert/Modal ใน JavaScript ต้องเรียกใช้ผ่าน `__('namespace.key')` (หรือ `addslashes(__('namespace.key'))` ใน JS)
  - **ความสมมาตร 100% ระหว่าง TH และ EN:** เมื่อเพิ่มคีย์ใน `app/Lang/th/<namespace>.php` ต้องเพิ่มคีย์เดียวกันลงใน `app/Lang/en/<namespace>.php` พร้อมคำแปลภาษาอังกฤษที่ถูกต้องในขั้นตอนเดียวกันเสมอ ห้ามมี Missing Key แม้แต่คีย์เดียว
  - **พฤติกรรมของ `Lang::trans()`:** หากหาคีย์ไม่พบ ฟังก์ชันจะแสดงชื่อคีย์ดิบ เช่น `'module.key'` ออกมาหน้าเว็บทันที ดังนั้นทุกคีย์ที่ถูกเรียกใช้ต้องมีคำแปลอยู่จริงทั้ง 2 ภาษา
  - **การตรวจสอบและซิงค์ข้อมูล:** ก่อนส่งมอบงานทุกครั้ง ต้องรัน `php compare_lang.php` (ต้องได้ `[]`) และรัน `php sync_lang.php` เพื่ออัปเดตคำแปลเข้าสู่ตาราง `translations` ใน MySQL
- **DataTables Global i18n & DOM:**
  - ห้ามใส่ property `language:` แยกต่างหากใน DataTables initialization ของแต่ละหน้า
  - ระบบมีการตั้งค่า Global Configuration ไว้แล้วใน `app/Views/layouts/app.php` ซึ่งครอบคลุมการแปลภาษา (Th/En) ของ DataTables แบบอัตโนมัติ
  - หากหน้าไหนมีการตั้งค่า DataTables ของตัวเอง (เช่น Custom Search) **ต้องใส่ `dom: 'rt<"flex flex-col md:flex-row justify-between items-center mt-4"lip><"clear">',` เสมอ** เพื่อดัน Length Menu (`l`), Info (`i`), และ Pagination (`p`) ไปอยู่ด้านล่างสุดของตารางให้เป็นมาตรฐานเดียวกันทั้งระบบ

## 12. Dynamic Theme, UI/UX, Button Preloading & SweetAlert2 Standards
- **Dynamic Palette:** ระบบรองรับการเปลี่ยนธีมแบบ Dynamic โดย `layouts/app.php` จะดึงค่าสีธีมปัจจุบันส่งเข้าไปทับ `primary` ของ Tailwind เสมอ ดังนั้นให้ใช้ class กลุ่ม `primary-*` สำหรับลูกเล่นหรือจุดที่ต้องการเปลี่ยนสีตามธีม
- **Primary Buttons:** ปุ่มกดหลักในแบบฟอร์ม (เช่น Save, Update, Create) **ต้องใช้ class `bg-sidebar` เสมอ** เพื่อให้ปุ่มมีสีตรงกับธีม Sidebar ปัจจุบันอย่างสมบูรณ์แบบ
- **Destructive Buttons:** ปุ่มที่เป็นอันตราย เช่น ลบข้อมูล หรือปิดการใช้งาน (Disable) ให้ใช้สีแดง (เช่น `bg-red-500`) ตามมาตรฐานสากลเสมอ
- **Button Preloading & Double-Submit Prevention (สถานะ Preloading ของปุ่ม):**
  - ทุกปุ่มดำเนินการ (Action Button) และปุ่ม Submit ในแบบฟอร์ม (บันทึก, อัปเดต, ลบ, ซิงค์ข้อมูล, ส่งคำขอ AJAX) **ต้องมีสถานะ Preloading เสมอ**
  - เมื่อผู้ใช้กดคลิก ต้อง Disable ปุ่มทันที (`btn.disabled = true;`) เพื่อป้องกัน Double Submission / Race Condition
  - แสดงไอคอนหมุนอนิเมชัน (เช่น `<i class="ph ph-circle-notch animate-spin text-base mr-2"></i>`) พร้อมข้อความระหว่างโหลดที่แปลภาษาแล้ว เช่น `<?= __('common.saving') ?>` ("กำลังบันทึก..."), `<?= __('common.please_wait') ?>` ("กรุณารอสักครู่...")
  - คืนสถานะปุ่ม (Re-enable) และข้อความเดิมเมื่อการทำงานเสร็จสิ้น หรือเมื่อเกิดข้อผิดพลาดในบล็อก `catch` / `finally`
- **SweetAlert2 Toast Standard (ตำแหน่งบนกลาง `position: 'top'`):**
  - ทุกการแจ้งเตือนแบบ Toast ด้วย SweetAlert2 (`Swal.fire({ toast: true, ... })` หรือ `window.Toast`):
    - **ตำแหน่งบังคับ:** ต้องกำหนดตำแหน่งเป็น **บนกลาง (`position: 'top'`) เท่านั้น** ห้ามใช้ `top-end`, `top-right`, `bottom-right`, `center`, หรือตำแหน่งอื่น
    - **ตัวเลือกมาตรฐาน:**
      ```javascript
      Swal.fire({
          toast: true,
          position: 'top',
          icon: 'success', // 'error', 'warning', 'info'
          title: message,
          showConfirmButton: false,
          timer: 3000,
          timerProgressBar: true,
          customClass: {
              popup: 'dark:bg-slate-800 dark:text-white border border-gray-100 dark:border-slate-700 shadow-xl',
              title: 'text-[13px] font-medium font-sans whitespace-nowrap px-2'
          }
      });
      ```
  - **Confirmation Dialogs:** การยืนยันใน Actions ที่อันตราย (เช่น Revoke, Delete) **ห้ามใช้ Native `confirm()` ของเบราว์เซอร์** ให้เรียกใช้ **SweetAlert2 (`Swal.fire`)** เสมอ
  - **ห้ามใช้ Native Alerts:** ห้ามใช้ `alert()`, `confirm()`, หรือ `prompt()` ของเบราว์เซอร์เด็ดขาด
- **Active & Hover States (Accents):** สถานะ Active ของแท็บเมนู (Tabs), แถวในตาราง, เมนูย่อย, หรือปุ่มรองต่างๆ ต้องใช้ `text-primary-*` และ `bg-primary-*` (เช่น `hover:bg-primary-50 dark:hover:bg-primary-900/20`) แทนการใช้สีเทา (`slate`) หรือสีฟ้า (`blue`) แบบเจาะจง เพื่อให้ความรู้สึกที่ตอบสนองกับธีมได้อย่างกลมกลืน (Premium Tint)
- **Global Color Scope:** ห้าม Override สีหลักของ Tailwind (เช่น `blue`, `gray`, `slate`) ทั่วทั้งระบบ เพื่อหลีกเลี่ยงผลกระทบต่อ UI ส่วนที่ไม่เกี่ยวข้อง จะมีการแมปแบบ Dynamic เฉพาะสี `primary` และ `sidebar` เท่านั้น
- **Ultra Dark Mode:** สำหรับโหมดกลางคืน (Dark Mode) ในส่วนของ Card หรือ Modal ให้ใช้ Background แบบโปร่งแสงที่ดูลึกและพรีเมียม เช่น `dark:bg-black/20 dark:backdrop-blur-md` หรือ `dark:bg-slate-950`
- **Sticky Top Save Button (มาตรฐานฟอร์มบันทึก):** ทุกหน้าจอที่เป็นฟอร์มบันทึกข้อมูล (เช่น Settings, Profile, Create/Edit Form) ปุ่มบันทึก (Save/Submit) ต้องถูกจัดวางไว้ในแถบ Header ด้านบนสุด และต้องใช้เทคนิค Sticky ควบคู่กับ Glassmorphism เพื่อให้แถบปุ่มติดขอบบนสุดตอนผู้ใช้เลื่อนหน้าจอลงมา
  - ตัวอย่างคลาสบังคับใช้: `sticky top-[-24px] md:top-[-32px] z-20 flex items-center justify-between mb-6 border-b border-gray-100 dark:border-slate-800 pb-4 pt-6 md:pt-8 -mt-6 md:-mt-8 -mx-6 px-6 bg-white/95 dark:bg-slate-900/95 backdrop-blur-md rounded-t-2xl`


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

### 0. กฎข้อที่ 1: ตรวจสอบและอ่านกฎก่อนเริ่มงาน (Must Read Rules First)
- [ ] **อ่านกฎทุกครั้ง:** อ่านข้อกำหนดใน `rules.md` และ `GEMINI.md` ก่อนเริ่มวิเคราะห์ ออกแบบ หรือเขียนโค้ดเสมอ

### 1. Routing & Controller (หลังบ้าน)
- [ ] ใช้ **Clean URL** เสมอ (เช่น `/users/create`) ห้ามใช้ Query String เพื่อเปลี่ยนหน้า (เช่น `?page=create`)
- [ ] ชื่อ Parameter ใน Route ต้อง **ตรงกับตัวแปรใน Controller** เสมอ (ป้องกัน PHP 8 Named Argument Error)
- [ ] ป้องกัน CSRF เสมอเมื่อรับ POST (ใช้ `\App\Core\Security::checkCsrf()`)
- [ ] ถอดรหัส ID (`decodeId`) ทันทีเมื่อรับค่าจากหน้าเว็บ และเข้ารหัส ID (`encodeId`) ก่อนส่งออกไปที่หน้าเว็บ

### 2. View, UI, Buttons & Notifications (หน้าบ้าน)
- [ ] **ใส่ภาษาให้ครบ 100%:** ห้าม Hardcode ข้อความภาษาไทยหรืออังกฤษใน View, JS หรือ Controller — ต้องเรียกผ่าน `__('namespace.key')`
- [ ] **ความสมมาตรของพจนานุกรม:** มีคีย์คำแปลตรงกัน 1:1 ครบทั้ง `app/Lang/th/` และ `app/Lang/en/` (รัน `php compare_lang.php` ต้องได้ `[]`) และรัน `php sync_lang.php` ซิงค์เข้า MySQL DB
- [ ] **ปุ่มมีสถานะ Preloading:** ปุ่ม Submit และปุ่ม Action ทุกปุ่ม ต้องมีกลไก Disable (`btn.disabled = true;`) ป้องกันการกดซ้ำซ้อน พร้อมแสดงไอคอนหมุน `<i class="ph ph-circle-notch animate-spin mr-2"></i>` และข้อความกำลังโหลดที่แปลภาษาแล้ว
- [ ] **SweetAlert2 Toast ตำแหน่งบนกลาง:** การแจ้งเตือน Toast ทุกจุดต้องตั้งค่า `position: 'top'` (บนกลาง) เสมอ ห้ามใช้ `top-end`, `top-right` หรือ Native `alert()`
- [ ] แสดงวันที่ต้องใช้ `formatDate($date)` (ฝั่งเซิร์ฟเวอร์) หรือ `formatDate(data)` (ฝั่ง JS) เท่านั้น (รูปแบบมาตรฐาน `d/m/Y`)
- [ ] **ปุ่ม Primary Action** (เช่น สร้าง, บันทึก) ต้องใช้ Class `bg-sidebar` (เพื่อให้สีเปลี่ยนตามธีมที่ตั้งค่าไว้)
- [ ] **ปุ่มอันตราย** (ลบ, ระงับ) ต้องใช้ `bg-red-500` และต้องแสดงหน้าต่างยืนยันด้วย **SweetAlert2 (`Swal.fire`)** เท่านั้น ห้ามใช้ `confirm()` ปกติ
- [ ] **ห้ามใช้สัญลักษณ์สกุลเงินบาท:** ห้ามใช้สัญลักษณ์ `฿` ทุกที่ ให้ใช้คำว่า 'บาท' หรือ 'THB' เท่านั้น
- [ ] **โลโก้และไอคอนแบรนด์ (Social Logos):** ต้องใช้ **Inline Vector SVG** หรือไฟล์ Asset ภายในระบบเท่านั้น ห้ามดึงภาพจาก Hotlink ภายนอก (เช่น Wikimedia Commons หรือ CDN นอก) ที่อาจโหลดไม่ติดเมื่อออฟไลน์หรือถูกบล็อก
- [ ] **การคัดลอกลงคลิปบอร์ด (Clipboard Feedback):** ทุกปุ่มคัดลอก (เช่น Callback URL, Token) ต้องเปลี่ยนไอคอนเป็นเครื่องหมายถูกสีเขียวชั่วคราว พร้อมแสดง **SweetAlert2 Toast บนกลาง (`position: 'top'`)** แจ้งเตือนข้อความที่แปลภาษาแล้ว และมี Fallback สำหรับ Context ที่ไม่ใช่ HTTPS
- [ ] **โครงสร้าง Modal แบบ Fixed Header/Footer:** ทุก Modal Dialog ต้องมี Header และ Footer แบบคงที่ (`flex-shrink-0`) พร้อมส่วนเนื้อหากลางที่เลื่อนได้ (`flex-1 overflow-y-auto`) และจำกัดความสูงไม่เกิน `max-h-[85vh] md:max-h-[90vh]` เพื่อให้ปุ่มบันทึกและปิดมองเห็นได้ตลอดเวลา
- [ ] สี Active / Hover ของเมนูหรือแท็บ ให้ใช้กลุ่มสี `primary-*` เท่านั้น (ห้ามใช้สี blue หรือ slate คงที่)

### 3. DataTables (ตารางข้อมูลแบบ Server-Side)
- [ ] **ทำงานแบบ Server-Side 100%:** กำหนด `serverSide: true` และ `processing: true` ในการสร้างตาราง DataTables ทุกหน้าจอ
- [ ] **สร้าง AJAX Endpoint:** มีเมธอดรองรับการดึงข้อมูลแบบ AJAX ใน Controller (เช่น `public function data()`) และใช้ `\App\Core\DataTableHelper::process()` ในการ Query
- [ ] **ความปลอดภัยของ ID (Hashids):** ข้อมูลที่ส่งกลับใน JSON ต้องแปลง `id` เป็น `hash_id` ด้วย `Security::encodeId()` เสมอ ห้ามส่ง Sequential ID ดิบ
- [ ] **DOM Layout มาตรฐาน:** หากต้องตั้งค่าหน้าตาของ DataTable เอง (เช่น ซ่อน Search เริ่มต้น) ต้องใส่ `dom: 'rt<"flex flex-col md:flex-row justify-between items-center mt-4"lip><"clear">',` เสมอ เพื่อจัดระเบียบ Length, Info, Pagination ให้อยู่ด้านล่างสุดของตารางอย่างสวยงาม
- [ ] **Global i18n:** ห้ามใส่ property `language:` แยกต่างหากใน DataTables initialization ของแต่ละหน้า ให้ใช้การแปลภาษาอัตโนมัติจาก `layouts/app.php`

### 4. Null Safety (PHP 8.1+)
- [ ] ตัวแปรใดที่ดึงจากฐานข้อมูลและอาจมีโอกาสเป็น `null` ต้องครอบด้วย `?? ''` ก่อนนำไปใช้ร่วมกับฟังก์ชันจัดการ String เสมอ

## 17. Premium Dashboard & UI Design Standards
- **No Welcome Banners:** เพื่อให้หน้า Dashboard ดูสะอาดและเน้นไปที่ข้อมูล (Data-driven) ให้ตัดส่วน Welcome Banner ขนาดใหญ่ออก และเริ่มต้นด้วยการ์ดสถิติ (Stat Cards) ทันที
- **Stat Cards (4 Columns):** จัดเรียงกล่องสถิติเป็น 4 คอลัมน์ (`grid-cols-1 md:grid-cols-2 xl:grid-cols-4`) เพื่อความสมดุล โดยให้มีหนึ่งกล่องแสดงยอด **"ผู้ใช้ออนไลน์ขณะนี้" (Online Users)** ซึ่งดึงข้อมูลจริงจากความเคลื่อนไหวในตาราง `user_sessions` (เช่น 15 นาทีล่าสุด)
- **Premium Aesthetics:** 
  - ใช้ลูกเล่น Glassmorphism (`backdrop-blur-xl`, `bg-white/50`) 
  - เพิ่มมิติด้วยเอฟเฟกต์ Hover (เช่น `hover:-translate-y-1`, `hover:scale-105`)
  - ใช้เงาสะท้อนแบบเรืองแสง (Glow shadows) แทนเงาดำปกติ เพื่อความหรูหรา
- **Modern Charts:** การตั้งค่ากราฟ (Chart.js) ให้ใช้เส้นโค้งมนสมูท (`tension: 0.4`), เพิ่มการถมสีแบบไล่ระดับ (Linear Gradients) สำหรับกราฟเส้น, และใช้ชุดสีโทน Premium Palette สำหรับกราฟวงกลม พร้อมปรับ Tooltip ให้เข้ากับธีม

## 18. Advanced Security (Password Policy, 2FA & RBAC Protection)
- **Password Complexity:** รหัสผ่านต้องถูกตรวจสอบความซับซ้อนตามนโยบายที่ตั้งไว้ใน Settings (`pwd_require_upper`, `pwd_require_lower`, `pwd_require_number`, `pwd_require_special`, `password_min_length`) เสมอเมื่อมีการสร้างหรือเปลี่ยนรหัสผ่าน
- **Validation Class:** ใช้เมธอด `\App\Core\Security::validatePasswordComplexity($password)` ในการตรวจสอบและรับค่า Error Array ออกมาแสดงผล
- **Two-Factor Authentication (2FA):** 
  - ระบบรองรับ 2FA (Google Authenticator) โดยอิงตามคอลัมน์ `google2fa_secret`
  - หาก `force_2fa_admin` ถูกเปิดใช้งาน ผู้ใช้ที่มีสิทธิ์ระดับ Admin จะต้องถูกบังคับให้ตั้งค่า 2FA ทันทีที่เข้าสู่ระบบ 
  - การบังคับใช้ถูกจัดการโดย `AuthMiddleware` ซึ่งจะ Redirect ไปที่หน้า Profile เพื่อบังคับให้ผู้ใช้เปิดใช้งาน 2FA ก่อนเข้าถึงระบบส่วนอื่น
- **RBAC Superadmin Protection:**
  - บัญชีระบบขั้นสูงสุดที่มี username ว่า `superadmin` หรือ `admin` จะต้องถูกบล็อก (Disabled) ปุ่มลบ (Delete) บนหน้า UI เพื่อป้องกันข้อผิดพลาด
  - ห้ามผู้ใช้ที่มีระดับสิทธิ์ (Role) ต่ำกว่า เข้าไปแก้ไขข้อมูลหรือลบบัญชี `admin` และ `superadmin` โดยเด็ดขาด 
  - ต้องมีการตรวจสอบซ้อนที่ฝั่ง Backend ในเมธอด `update()` และ `delete()` ของ `UserController` เสมอ

## 19. System Maintenance & Error Handling
- **Maintenance Mode:** ระบบรองรับโหมดบำรุงรักษา (Maintenance Mode) โดยแสดงหน้า `app/Views/errors/maintenance.php` ป้องกันผู้ใช้ทั่วไปเข้าใช้งานระหว่างอัปเดตระบบ
- **Error Handling:** จัดการข้อผิดพลาดและ Exception ทั้งหมดผ่าน `App\Core\ErrorHandler` โดยในโหมด Production (`APP_DEBUG=false`) จะซ่อนข้อผิดพลาดไม่ให้แสดงออกหน้าจอ แต่จะบันทึกรายละเอียด (Stack Trace) ลงใน `storage/logs/error.log` แทนเพื่อความปลอดภัย

## 20. Rate Limiting (Security)
- **Brute Force Protection:** ระบบมีตาราง `rate_limits` (`ip_address`, `endpoint`, `attempts`, `lockout_time`) เพื่อป้องกันการโจมตีแบบ Brute Force หรือสแปม Request ในจุดสำคัญ เช่น หน้า Login, OTP หรือการร้องขอรีเซ็ตรหัสผ่าน

## 21. Social Login & OAuth Architecture (Google, LINE, Facebook, GitHub)
- **Multi-Provider Support:** ระบบรองรับ Social Login มาตรฐาน 4 ผู้ให้บริการหลัก ได้แก่ Google, LINE, Facebook และ GitHub ผ่าน `SocialAuthController`
- **Inline Vector SVGs:** โลโก้และไอคอนแบรนด์ทุกตัวต้องใช้เป็น Inline Vector SVG คุณภาพสูงภายในไฟล์โดยตรง ห้ามใช้ `<img>` ชี้ไปยัง CDN หรือ Hotlink ภายนอก (เช่น Wikimedia) เพื่อป้องกันปัญหาภาพไม่โหลดตอนออฟไลน์ หรือข้อผิดพลาด SSL Revocation
- **Data Schema:** ข้อมูลผู้ใช้ผูกเข้ากับคอลัมน์ `google_id`, `line_id`, `facebook_id`, `github_id` ในตาราง `users`
- **Confirmation on Disconnect:** การยกเลิกการผูกบัญชีโซเชียลในหน้าโปรไฟล์ (`/profile`) ต้องใช้ **SweetAlert2 Dialog (`Swal.fire`)** เสมอ ห้ามใช้ Native `confirm()` ของเบราว์เซอร์

## 22. Clipboard Copy Standard & Top-Center Toast Feedback
- **Dual Visual Feedback:** ทุกครั้งที่มีปุ่มกดคัดลอก (เช่น OAuth Callback URL ในหน้า Settings หรือ Token ต่างๆ):
  1. ไอคอนปุ่มต้องเปลี่ยนเป็นเครื่องหมายถูกสีเขียว (`ph-check text-green-500`) เป็นเวลา 2 วินาที
  2. แสดงผลการแจ้งเตือน **SweetAlert2 Toast ตำแหน่งบนกลาง (`position: 'top'`)** พร้อมข้อความภาษาที่ถูกต้อง (`__('settings.copied_to_clipboard')`)
- **HTTP/Localhost Compatibility:** ต้องมีกลไกตรวจสอบ `navigator.clipboard && window.isSecureContext` พร้อม Fallback ด้วย `document.execCommand('copy')` เพื่อให้สามารถใช้งานบน Localhost HTTP ได้อย่างราบรื่น

## 23. Fixed Modal Header & Footer Architecture (สถาปัตยกรรม Modal แบบ Header/Footer คงที่ และตรงกลางเลื่อนได้)
- **Flexbox Layout Standard:** ทุกหน้าต่าง Modal Dialog ในระบบ ต้องจัดโครงสร้างแบบ Flex Column ที่มีการกำหนดความสูงสูงสุดอย่างชัดเจน:
  - **Container & Max Height:** กล่อง Modal Card ต้องมีคลาส `flex flex-col max-h-[85vh] md:max-h-[90vh] overflow-hidden` (หากมีแท็ก `<form>` ครอบเนื้อหาทั้งหมด ฟอร์มต้องมีคลาส `flex flex-col h-full max-h-[85vh] md:max-h-[90vh] overflow-hidden` เช่นกัน)
  - **Fixed Header:** แถบหัวเรื่องด้านบนต้องมีคลาส `flex-shrink-0 border-b border-gray-100 dark:border-slate-800 px-6 py-4 bg-white dark:bg-slate-900` พร้อมปุ่มปิด (`ph-x`) โดยแถบนี้ต้องอยู่กับที่เสมอ ห้ามเลื่อนหลุดสายตาเด็ดขาด
  - **Scrollable Middle Body:** ส่วนเนื้อหาแบบฟอร์มตรงกลางต้องกำหนดคลาส `flex-1 overflow-y-auto p-6` (พร้อม custom scrollbar) เพื่อให้สามารถเลื่อนดูเนื้อหาภายในได้เฉพาะส่วนนี้เมื่อข้อมูลยาวเกินหน้าจอ
  - **Fixed Footer:** แถบปุ่มดำเนินการด้านล่าง (เช่น บันทึก, ยกเลิก) ต้องมีคลาส `flex-shrink-0 border-t border-gray-100 dark:border-slate-800 px-6 py-4 bg-white dark:bg-slate-900 flex justify-end gap-3` เพื่อให้ปุ่ม Action สำคัญแสดงผลติดขอบล่างของ Modal ตลอดเวลา ผู้ใช้สามารถกดบันทึกหรือยกเลิกได้ทันทีโดยไม่ต้องเลื่อนหน้าจอลงไปล่างสุด


