# Standard Core System (PHP UMS Boilerplate)

ระบบแกนหลักและฐานรากมาตรฐานสำหรับการพัฒนาเว็บแอปพลิเคชัน (**Pure PHP 8.4+ MVC Boilerplate & Manager Hub**) ออกแบบมาเพื่อเป็นศูนย์กลางการบริหารจัดการผู้ใช้ สิทธิ์การใช้งาน และการตั้งค่าระบบ พร้อมต่อยอดเชื่อมต่อกับระบบธุรกิจอื่นๆ ได้ทันที เช่น ERP, CRM, POS, สต็อกสินค้า หรือระบบจัดการคำสั่งซื้อ โดยไม่ต้องเริ่มต้นสร้างระบบพื้นฐานใหม่

---

## 📌 1. Overview: โปรเจกต์นี้ทำอะไร

โปรเจกต์นี้ทำหน้าที่เป็น **"ระบบแกนกลางมาตรฐาน (Standard Core System)"** สำหรับองค์กรหรือทีมพัฒนาซอฟต์แวร์ โดยยึดหลักการทำงานแบบ **Core as a Manager & Highly Modular** ตัวระบบหลักจะทำหน้าที่เป็นศูนย์กลางบริหารจัดการความปลอดภัย บัญชีผู้ใช้งาน สิทธิ์ และการตั้งค่า โดยระบบอื่นๆ ที่จะนำมาต่อยอดจะเข้ามาเชื่อมต่อในรูปแบบ Module แยกอิสระ

### ฟีเจอร์หลักของระบบ (Core Features):
* **🔐 Authentication & User Management:** ระบบล็อกอิน, สมัครสมาชิก, ยืนยันอีเมล, รีเซ็ตรหัสผ่าน, นโยบายความซับซ้อนของรหัสผ่าน (Password Complexity Policy), Two-Factor Authentication (2FA - Google Authenticator), และระบบป้องกัน Brute-force ด้วย Rate Limiter แบบ Sliding Time Window
* **🌐 4-Provider Social Login & OAuth:** เข้าสู่ระบบและเชื่อมต่อบัญชีผ่าน Google, LINE, Facebook และ GitHub พร้อมแสดงโลโก้ด้วย Inline Vector SVG คมชัดทุกขนาดหน้าจอ ตัดปัญหา Hotlink ขาดหาย
* **🛡️ Role-Based Access Control (RBAC):** กำหนดบทบาทและสิทธิ์อย่างละเอียดผ่าน Matrix พร้อมกลไก Superadmin Protection ปกป้องบัญชีแอดมินสูงสุด และเข้ารหัส Primary Key ด้วย Hashids 8 หลักทั้งระบบ
* **🌍 100% Complete Bilingual Localization (i18n):** รองรับภาษาไทย (`th`) และภาษาอังกฤษ (`en`) แบบสมมาตร 1:1 ครบ 100% ทุกจุด (0 missing keys) สลับภาษาได้ทันที และมีระบบแก้ไขคำแปลผ่าน UI บันทึกลงฐานข้อมูล MySQL
* **🎨 Modern UI/UX & Dynamic Theming:** พัฒนาด้วย Tailwind CSS, Google Font Prompt, Dark/Light Mode สมบูรณ์แบบ, สลับสีธีมได้ 5 เฉดสี (Primary Palettes), ตาราง DataTables แบบ Server-Side Processing 100% ประมวลผลบนฐานข้อมูลรวดเร็วและปลอดภัย, และ SweetAlert2 Toast ตำแหน่งบนกลาง (`position: 'top'`)
* **⚙️ System Settings & Design Spec Portal:** ปรับแต่งค่าระบบได้ละเอียด พร้อมหน้าจอ Design Specification Portal (`/settings/design_spec`) แสดงพิมพ์เขียวสถาปัตยกรรม โครงสร้างฐานข้อมูล 12 ตาราง และ UI Playground
* **📊 Activity & Audit Logging:** บันทึกประวัติกิจกรรมผู้ใช้งาน (Activity Logs) และบันทึกประวัติการเปลี่ยนแปลงข้อมูลระดับลึกแบบ JSON Diff Viewer (Audit Logs)

---

## 🗺️ 2. Map: โครงสร้างไฟล์และจุดเริ่มต้นของระบบ

### แผนผังโฟลเดอร์หลัก (Directory Structure):
```text
php_ums/
├── app/                           # โค้ดหลักของแอปพลิเคชัน (PSR-4 Autoloading)
│   ├── Controllers/               # Controller ตามหลัก MVC
│   │   ├── Api/                   # API Endpoints (JSON Response)
│   │   ├── AuthController.php     # ระบบ Authentication พื้นฐาน
│   │   ├── SocialAuthController.php # จัดการ OAuth (Google, LINE, Facebook, GitHub)
│   │   ├── UserController.php     # จัดการข้อมูลผู้ใช้
│   │   ├── RoleController.php     # จัดการบทบาทและสิทธิ์ (RBAC Matrix)
│   │   ├── ProfileController.php  # จัดการโปรไฟล์ รหัสผ่าน 2FA และ Social Links
│   │   ├── SettingController.php  # จัดการการตั้งค่าระบบและคำแปลภาษา
│   │   ├── LogController.php      # แสดง Activity & Audit Logs
│   │   └── DashboardController.php # หน้าสรุปภาพรวมและสถิติ
│   ├── Core/                      # กลไกแกนหลักของเฟรมเวิร์ก
│   │   ├── Router.php             # ตัวจัดการ Clean URL Routing และ Middleware Pipeline
│   │   ├── Controller.php         # Base Controller (view, json, redirect)
│   │   ├── Database.php           # PDO Wrapper พร้อม Prepared Statements
│   │   ├── Security.php           # Hashids (encode/decode), CSRF, Password Policy
│   │   ├── Session.php            # Session Wrapper & Flash Messages
│   │   ├── Lang.php               # ระบบแปลภาษา i18n
│   │   ├── RateLimiter.php        # ระบบป้องกัน Brute Force ด้วย MySQL
│   │   ├── RBAC.php               # ตรวจสอบสิทธิ์ Permission
│   │   ├── ErrorHandler.php       # จัดการ Exceptions & Error Logging
│   │   └── Settings.php           # ตัวดึงและบันทึกค่า System Settings
│   ├── Middlewares/               # Filter กรองคำขอก่อนเข้า Controller
│   │   ├── AuthMiddleware.php     # ตรวจสอบการเข้าสู่ระบบและบังคับ 2FA
│   │   ├── PermissionMiddleware.php # ตรวจสอบสิทธิ์การเข้าถึงเมนู/ฟังก์ชัน
│   │   └── RateLimitMiddleware.php # ควบคุมอัตราการส่ง Request
│   ├── Lang/                      # ไฟล์พจนานุกรมแปลภาษา
│   │   ├── th/                    # ภาษาไทย (auth, common, users, roles, settings, etc.)
│   │   └── en/                    # ภาษาอังกฤษ (โครงสร้างคีย์ตรงกัน 1:1 กับ th)
│   ├── Views/                     # ไฟล์มุมมองและหน้าจอแสดงผล (HTML/PHP)
│   │   ├── layouts/               # โครงร่างหน้าเว็บ (app.php, auth.php)
│   │   ├── auth/                  # หน้า Login, Register, Forgot/Reset Password, 2FA
│   │   ├── dashboard/             # หน้าสถิติและกราฟวิเคราะห์
│   │   ├── users/                 # หน้าแสดงรายการ สร้าง และแก้ไขผู้ใช้
│   │   ├── roles/                 # หน้ากำหนดสิทธิ์ RBAC Matrix
│   │   ├── settings/              # หน้าตั้งค่าระบบ และ Design Spec Portal
│   │   └── logs/                  # หน้าประวัติกิจกรรมและประวัติการแก้ไขข้อมูล
│   └── helpers.php                # ฟังก์ชันระดับ Global (__, url, asset, formatDate, etc.)
├── database/                      # ฐานข้อมูลและการจัดการ Schema
│   ├── migrations/                # ประวัติการสร้างตาราง (Phinx Migrations)
│   └── seeds/                     # ข้อมูลเริ่มต้นสำหรับระบบ
├── public/                        # เว็บรูทที่เปิดให้เข้าถึงได้จากภายนอก
│   ├── assets/                    # CSS, JS, รูปภาพ, โลโก้
│   ├── uploads/                   # โฟลเดอร์เก็บไฟล์ที่ผู้ใช้อัปโหลด (รูปโปรไฟล์)
│   └── index.php                  # จุดเริ่มต้นการทำงานของระบบ (Single Entry Point)
├── cli/                           # เครื่องมืออำนวยความสะดวกบน Command Line
├── .htaccess                      # Rewrite URL ไปที่ public/index.php อย่างปลอดภัย
├── compare_lang.php               # สคริปต์ตรวจสอบความสมบูรณ์คู่ภาษา 100%
├── sync_lang.php                  # สคริปต์ซิงค์คำแปลภาษาเข้าสู่ MySQL
├── rules.md                       # กฎและมาตรฐานสถาปัตยกรรมของระบบ
└── GEMINI.md                      # กฎเหล็กของ AI Agent ในการพัฒนาโปรเจกต์
```

### จุดเริ่มต้นและการไหลของระบบ (Request Lifecycle):
1. **Entry Point:** ผู้ใช้ส่งคำขอมาที่เว็บเซิร์ฟเวอร์ `.htaccess` ที่รูทจะ Rewrite ทุก Request ไปที่ `public/index.php`
2. **Bootstrapping:** `public/index.php` โหลด Composer Autoloader, โหลดไฟล์ `.env`, เริ่มต้น Session, และลงทะเบียน `ErrorHandler`
3. **Routing:** `App\Core\Router` อ่าน URL ที่ส่งเข้ามาแบบ Clean URL และจับคู่กับ Route ที่ลงทะเบียนไว้
4. **Middleware:** Request จะวิ่งผ่าน Middleware Pipeline เช่น `AuthMiddleware` (ตรวจสิทธิ์ผู้ใช้และ 2FA) หรือ `PermissionMiddleware` (ตรวจสิทธิ์เฉพาะจุด)
5. **Controller & Service:** Controller รับค่า ถอดรหัส ID (`Security::decodeId`), เรียกใช้ Model/Database, และส่งข้อมูลไปเรนเดอร์ที่ View
6. **Rendering & Output:** View ดึง Layout หลัก (`layouts/app.php`) ประกอบส่วนหัว, Dynamic Theme, และส่ง HTML กลับไปยังเบราว์เซอร์

---

## ⚙️ 3. Setup: วิธีติดตั้งและรันโปรเจกต์

### สิ่งที่ต้องเตรียม (Prerequisites):
* **PHP:** เวอร์ชัน 8.2 หรือสูงกว่า (แนะนำ **PHP 8.4+**)
* **Database:** MySQL 8.0+ หรือ MariaDB 10.5+
* **Package Manager:** Composer
* **Web Server:** Apache (พร้อมเปิด `mod_rewrite`), Nginx หรือ Laragon

### ขั้นตอนการติดตั้ง:
1. **Clone โปรเจกต์ลงมาที่เครื่อง:**
   ```bash
   git clone https://github.com/kokostudio/php_ums.git my_project
   cd my_project
   ```

2. **ติดตั้ง Dependencies:**
   ```bash
   composer install
   ```

3. **สร้างและตั้งค่าไฟล์ Environment (.env):**
   ```bash
   cp .env.example .env
   ```
   แก้ไขข้อมูลการเชื่อมต่อฐานข้อมูลใน `.env`:
   ```ini
   APP_ENV=local
   APP_DEBUG=true
   APP_URL=http://php_ums.localhost

   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=php_ums
   DB_USERNAME=root
   DB_PASSWORD=
   ```

4. **รัน Migration และ Seed ข้อมูลเริ่มต้น:**
   ```bash
   vendor/bin/phinx migrate
   vendor/bin/phinx seed:run
   ```

5. **ซิงค์พจนานุกรมภาษาเข้าสู่ฐานข้อมูล MySQL:**
   ```bash
   php sync_lang.php
   ```

6. **ข้อมูลเข้าสู่ระบบเริ่มต้น (Default Superadmin):**
   * **URL:** `http://php_ums.localhost/login`
   * **Username:** `superadmin`
   * **Password:** `password`
   *(หมายเหตุ: แนะนำให้เปลี่ยนรหัสผ่านทันทีหลังเข้าสู่ระบบครั้งแรกในหน้า My Profile)*

---

## 🧪 4. Test: คำสั่งทดสอบและข้อควรระวัง

### คำสั่งตรวจสอบและทดสอบคุณภาพ:
* **ตรวจสอบความสมบูรณ์ของคู่ภาษา (100% Language Parity Check):**
  ```bash
  php compare_lang.php
  ```
  *เกณฑ์ผ่าน:* ผลลัพธ์ต้องแสดง `[]` (Array ว่าง มี Missing Key เป็น 0 เท่านั้น)
* **ซิงค์คำแปลเข้าสู่ฐานข้อมูล:**
  ```bash
  php sync_lang.php
  ```
* **รีเฟรช Code Context Graph (Graft Context Engine):**
  ```bash
  graft build
  ```

### ข้อควรระวังสำคัญ (Critical Caveats):
* **ระวังปัญหา Encoding บน Windows (Mojibake):** ห้ามใช้คำสั่ง Windows PowerShell (เช่น `Set-Content`, `Out-File`, `echo >`) ในการเขียนหรือแก้ไขไฟล์ที่มีภาษาไทยเด็ดขาด เพราะจะทำให้ฟอนต์เพี้ยนกลายเป็นภาษาต่างดาว ให้ใช้เครื่องมือแก้ไขไฟล์ของ Editor หรือรันผ่านสคริปต์ PHP แทน
* **ระวัง Named Arguments ใน PHP 8+:** ชื่อตัวแปร Parameter ในเมธอดของ Controller ต้องตั้งชื่อให้ตรงกับตัวแปรใน Route เสมอ (เช่น Route `/users/{id}/edit` ต้องรับค่าด้วย `public function edit($id)`) มิฉะนั้นจะเกิด Named Parameter Error จาก `call_user_func_array`
* **ระวังการลืมถอดรหัส ID:** เมื่อรับ ID มาจาก URL หรือ Form ต้องใช้ `Security::decodeId($id)` เสมอ และต้องตรวจสอบผลลัพธ์ว่าถอดรหัสสำเร็จก่อนส่งไป Query

---

## 📐 5. Conventions: รูปแบบการเขียนโค้ดและแนวทางที่ทีมใช้จริง

### สถาปัตยกรรมและมาตรฐานโค้ด (Architecture Standards):
* **PSR-4 Autoloading:** Namespace ต้องตรงกับโครงสร้างโฟลเดอร์ เช่น `App\Controllers\UserController`
* **Pure PHP MVC:** แยก Controller, Model และ View ชัดเจน ห้ามเขียน Business Logic ยาวเหยียดใน Controller
* **Clean URLs เสมอ:** ห้ามใช้ Query String ในการนำทางหลัก เช่น ให้ใช้ `/settings/general` ห้ามใช้ `/settings?tab=general`
* **ID Obfuscation (Hashids):** ห้ามเปิดเผย Database Primary Key (1, 2, 3) ให้กับผู้ใช้งานผ่าน UI หรือ URL ให้เข้ารหัสเป็น Hash 8 หลักด้วย `Security::encodeId($id)` ก่อนแสดงผล และถอดรหัสด้วย `Security::decodeId($hash)` เมื่อรับค่ากลับเข้ามา

### มาตรฐานหน้าจอและ UI/UX (UI/UX Conventions):
* **Typography:** ใช้ Google Font **Prompt** (wght 300-700) เป็นมาตรฐานเดียวทั้งระบบ รองรับภาษาไทยและอังกฤษอย่างลงตัว
* **Primary Buttons:** ปุ่มกดหลักในแบบฟอร์ม (สร้าง, บันทึก) ต้องใช้คลาส `bg-sidebar` เสมอ เพื่อให้สีปุ่มเปลี่ยนตามธีมที่เลือก
* **Destructive Buttons:** ปุ่มอันตราย (ลบ, ระงับการใช้งาน) ต้องใช้คลาส `bg-red-500` และต้องยืนยันผ่าน **SweetAlert2 (`Swal.fire`)** เสมอ
* **Button Preloading & Double-Submit Prevention:** ทุกปุ่ม Action หรือ Submit ต้อง Disable ทันทีเมื่อคลิก (`btn.disabled = true;`) แสดงไอคอนหมุน `<i class="ph ph-circle-notch animate-spin mr-2"></i>` และข้อความกำลังโหลดที่แปลภาษาแล้ว เช่น `__('common.saving')`
* **SweetAlert2 Toast ตำแหน่งบนกลาง:** การแจ้งเตือน Toast ทุกจุด **ต้องตั้งค่า `position: 'top'` (บนกลาง) เท่านั้น**
* **Mandatory Server-Side DataTables:** ตารางข้อมูลทุกหน้าจอต้องทำงานในรูปแบบ Server-Side Processing (`serverSide: true, processing: true`) ผ่าน AJAX Endpoint ที่ประมวลผลด้วย `DataTableHelper` เพื่อลดภาระหน่วยความจำบนเบราว์เซอร์ พร้อมซ่อน Primary Key ด้วย Hashids 8 หลักใน JSON เสมอ
* **Inline Vector SVGs:** โลโก้แบรนด์ทุกตัว (Google, LINE, Facebook, GitHub) ต้องใช้ Inline SVG ฝังในไฟล์โดยตรง ห้ามใช้ External Hotlink
* **Clipboard Copy UX:** ปุ่มคัดลอกต้องมี Dual Feedback คือเปลี่ยนไอคอนเป็น `ph-check text-green-500` ชั่วคราว 2 วินาที พร้อมแสดง SweetAlert2 Toast ตำแหน่งบนกลาง
* **Fixed Modal Header & Footer:** ทุกหน้าต่าง Modal ในระบบต้องใช้โครงสร้าง Flexbox จำกัดความสูงไม่เกิน `max-h-[85vh] md:max-h-[90vh]` โดย Header และ Footer ต้องมีคลาส `flex-shrink-0` อยู่กับที่เสมอ และมีเฉพาะส่วนเนื้อหากลางที่เลื่อนได้ (`flex-1 overflow-y-auto`) เพื่อให้ปุ่ม Action และปุ่มปิดมองเห็นได้ตลอดเวลาโดยไม่ต้องเลื่อนหา

---

## 🔒 6. Logic: กฎสำคัญเกี่ยวกับสิทธิ์ การยืนยันตัวตน และการจัดการข้อมูล

### 1. ระบบสิทธิ์ (RBAC Matrix & Protection):
* ควบคุมสิทธิ์ผ่านตาราง `roles`, `permissions`, และ `role_permissions`
* ตรวจสอบสิทธิ์ในโค้ดด้วยฟังก์ชัน `has_permission('module.action')` หรือผ่าน `PermissionMiddleware`
* **Superadmin Protection:** บัญชี `superadmin` และ `admin` จะถูก Disabled ปุ่มลบในหน้า UI และมี Backend Validation ป้องกันไม่ให้ผู้ใช้ที่มีสิทธิ์ต่ำกว่าเข้ามาแก้ไขหรือลบบัญชีหลักนี้เด็ดขาด

### 2. การยืนยันตัวตนและความปลอดภัย (Authentication):
* **Password Complexity:** ตรวจสอบความซับซ้อนของรหัสผ่านผ่าน `Security::validatePasswordComplexity($password)` ตามนโยบายใน Settings (ความยาวขั้นต่ำ, ตัวพิมพ์ใหญ่, ตัวพิมพ์เล็ก, ตัวเลข, อักขระพิเศษ)
* **Two-Factor Authentication (2FA):** รองรับ Google Authenticator หากเปิดใช้ `force_2fa_admin` ผู้ใช้ระดับ Admin จะถูก `AuthMiddleware` บังคับให้ตั้งค่า 2FA ทันทีก่อนเข้าถึงระบบ
* **Social Login (OAuth 2.0):** รองรับ 4 ผู้ให้บริการ ตรวจสอบ `state` parameter ป้องกัน CSRF และบันทึก ID แยกตามคอลัมน์ `google_id`, `line_id`, `facebook_id`, `github_id` ในตาราง `users`
* **Rate Limiting:** ป้องกันการ Brute Force ที่หน้า Login และ Endpoint สำคัญ โดยใช้ Sliding Time Window และจะรีเซ็ตอัตราการล็อกทันทีเมื่อล็อกอินสำเร็จ (`RateLimiter::reset('/login')`)

### 3. การจัดการข้อมูลและบันทึกประวัติ (Data & Logs):
* **Activity Log:** บันทึกการกระทำของผู้ใช้ เช่น ล็อกอิน, สร้าง, แก้ไข, ลบข้อมูล
* **Audit Log:** บันทึกความเปลี่ยนแปลงของข้อมูลระดับลึก โดยจัดเก็บค่าเก่าและค่าใหม่เป็น JSON เพื่อใช้แสดงผลใน JSON Diff Viewer
* **Date Formatting:** วันที่ในทุกจุดของระบบต้องแสดงผลในรูปแบบ `d/m/Y` หรือ `d/m/Y H:i:s` ผ่านฟังก์ชัน `formatDate()` เสมอ
* **PHP 8.1+ Null Safety:** ตัวแปรที่ดึงจากฐานข้อมูลและอาจมีค่าเป็น `null` ต้องครอบด้วย `?? ''` เสมอเมื่อนำไปใช้งานกับฟังก์ชันจัดการ String เพื่อป้องกัน Fatal Error

---

## 🚫 7. Never: สิ่งที่ห้ามทำโดยเด็ดขาด

1. ❌ **ห้ามเริ่มงานโดยไม่อ่านกฎ:** ห้ามวิเคราะห์ เขียนโค้ด หรือแก้ไขไฟล์โดยไม่อ่าน `rules.md` และ `GEMINI.md` ก่อนเด็ดขาด
2. ❌ **ห้ามใช้คำสั่ง Windows PowerShell เขียนไฟล์ภาษาไทย:** ห้ามใช้ `Set-Content` หรือ `Out-File` ที่ทำให้เกิดภาษาต่างดาว (Mojibake)
3. ❌ **ห้ามใช้สัญลักษณ์สกุลเงินบาท:** ห้ามพิมพ์สัญลักษณ์เงินบาท (ตัว B ขีด) ทุกจุดในโปรเจกต์ (UI, โค้ด, ตาราง, ข้อความแปล) ให้ใช้คำว่า 'บาท' หรือ 'THB' เท่านั้น
4. ❌ **ห้าม Hardcode ข้อความ UI ใน View หรือ JavaScript:** ต้องเรียกใช้ผ่าน `__('namespace.key')` เท่านั้น
5. ❌ **ห้ามใช้ Browser Native Dialogs:** ห้ามใช้ `alert()`, `confirm()`, หรือ `prompt()` ของเบราว์เซอร์ ให้ใช้ SweetAlert2 เท่านั้น
6. ❌ **ห้ามตั้งค่าตำแหน่ง SweetAlert2 Toast นอกเหนือจากบนกลาง:** ต้องใช้ `position: 'top'` เท่านั้น ห้ามใช้ top-end, center หรือ bottom
7. ❌ **ห้ามเปิดเผย Database Primary Key (Sequential ID 1, 2, 3):** ต้องเข้ารหัสเป็น Hashids 8 หลักเสมอ
8. ❌ **ห้ามใช้ Query String ในการนำทางหลัก:** ห้ามใช้ `?page=...` หรือ `?tab=...` ต้องใช้ Clean URL เสมอ
9. ❌ **ห้ามใช้ Form Submit แบบดั้งเดิมสำหรับการลบข้อมูล:** ต้องใช้ AJAX (`axios.post`) พร้อม SweetAlert2 Dialog ยืนยัน และมี Route GET Fallback เสมอ
10. ❌ **ห้ามใช้ External Hotlink สำหรับรูปภาพและโลโก้แบรนด์:** ต้องใช้ Inline Vector SVG หรือ Asset ภายในระบบเท่านั้น
11. ❌ **ห้ามใช้ Client-Side DataTables สำหรับตารางข้อมูลหลักของระบบ:** ตารางข้อมูลทุกหน้าจอต้องทำงานแบบ Server-Side Processing เท่านั้น
12. ❌ **ห้ามสร้าง Modal ที่ Header หรือ Footer เลื่อนหายไปตามเนื้อหา:** ต้องใช้โครงสร้าง Fixed Header, Fixed Footer และ Scrollable Middle Body เสมอ

---

## ✅ 8. Done: เงื่อนไขที่ต้องผ่านก่อนส่งงาน (Definition of Done)

ก่อนส่งมอบงานหรือ Commit & Push โค้ดขึ้น Repository ทุกครั้ง งานจะถือว่าเสร็จสมบูรณ์เมื่อผ่านเกณฑ์ตรวจสอบครบทั้ง 10 ข้อ:

- [ ] **1. กฎข้อที่ 1:** อ่านและตรวจสอบตามข้อกำหนดใน `rules.md` และ `GEMINI.md` อย่างครบถ้วน
- [ ] **2. 100% Complete Localization:** รัน `php compare_lang.php` ได้ผลลัพธ์เป็น `[]` (ไม่มี Missing Keys คู่ภาษา TH/EN ตรงกัน 1:1)
- [ ] **3. Database Sync:** รัน `php sync_lang.php` เพื่ออัปเดตคำแปลเข้าสู่ตาราง `translations` ในฐานข้อมูลเรียบร้อย
- [ ] **4. Zero Banned Currency Symbol:** ตรวจสอบไม่มีการใช้สัญลักษณ์สกุลเงินบาทในหน้าจอ โค้ด หรือคำแปล
- [ ] **5. Button Preloading:** ปุ่ม Action และ Submit ทุกปุ่มมีกลไก Disable พร้อม Spinner และข้อความระหว่างโหลด
- [ ] **6. SweetAlert2 Top-Center:** การแจ้งเตือน Toast ทุกจุดตั้งค่าตำแหน่ง `position: 'top'` (บนกลาง) เรียบร้อย
- [ ] **7. Server-Side DataTables:** ตารางข้อมูลทำงานในรูปแบบ Server-Side Processing (`serverSide: true`) 100% พร้อมแปลง `hash_id` ใน JSON
- [ ] **8. Fixed Modal Header/Footer:** หน้าต่าง Modal ทุกตัวจัดโครงสร้าง Header/Footer คงที่ และตรงกลางเลื่อนได้
- [ ] **9. Context Graph Sync:** รันคำสั่ง `graft build` เพื่ออัปเดตความสัมพันธ์ของโค้ดใน Context Graph
- [ ] **10. Changelog Update:** บันทึกรายละเอียดการปรับปรุงฟีเจอร์ลงใน `CHANGELOG.md` เป็นภาษาไทยครบถ้วน

---

## 🤖 ตัวอย่างคำสั่งสำหรับสั่งงาน AI Assistant

```text
"ฉันต้องการสร้างโมดูลใหม่ชื่อ [ชื่อโมดูล เช่น Inventory] 
โปรดอ่านกฎและมาตรฐานทั้งหมดจาก rules.md และ .agents/skills/php-ums-core-workflow/SKILL.md 
และสร้าง Controller, Model, View, Route พร้อมระบบสิทธิ์ RBAC 
โดยต้องใส่ภาษาครบทั้ง TH/EN 100% ป้องกัน Double-Submit ที่ปุ่ม และใช้ SweetAlert2 Toast ตำแหน่งบนกลาง"
```
