# Standard Core System (PHP UMS Boilerplate)

ระบบแกนหลักและฐานรากมาตรฐานสำหรับการพัฒนาเว็บแอปพลิเคชัน (**Pure PHP 8.4+ MVC Boilerplate & Manager Hub**) ออกแบบมาเพื่อเป็นศูนย์กลางการบริหารจัดการผู้ใช้ สิทธิ์การใช้งาน และการตั้งค่าระบบ พร้อมต่อยอดเชื่อมต่อกับระบบธุรกิจอื่นๆ ได้ทันที เช่น ERP, CRM, POS, สต็อกสินค้า หรือระบบจัดการคำสั่งซื้อ โดยไม่ต้องเริ่มต้นสร้างระบบพื้นฐานใหม่

---

## ✨ ฟีเจอร์หลักของระบบ (Core Features)

* **🔐 Authentication & User Management:**
  * ระบบเข้าสู่ระบบ (Login), สมัครสมาชิก (Register), ยืนยันอีเมล, ขอลิงก์รีเซ็ตรหัสผ่าน (Forgot Password)
  * ระบบเปลี่ยนรหัสผ่าน พร้อมการตรวจสอบนโยบายความปลอดภัย (Password Complexity Policy)
  * การรักษาความปลอดภัยด้วย Two-Factor Authentication (2FA - Google Authenticator) พร้อมนโยบายบังคับสำหรับแอดมิน (`force_2fa_admin`)
  * ป้องกันการสแปมและการโจมตีแบบ Brute-Force ด้วย `RateLimiter` แบบ Sliding Time Window
* **🌐 4-Provider Social Login & OAuth:**
  * รองรับการเข้าสู่ระบบและเชื่อมต่อบัญชีผ่าน **Google, LINE, Facebook และ GitHub**
  * โลโก้แบรนด์ทุกตัวแสดงผลด้วย **Inline Vector SVG** คุณภาพสูง คมชัดทุกขนาดหน้าจอ ตัดการพึ่งพา Hotlink ภายนอก หมดปัญหาภาพแตกหรือข้อผิดพลาด SSL Revocation
  * ระบบเชื่อมต่อและตัดการเชื่อมต่อบัญชีโซเชียลในหน้าโปรไฟล์ พร้อมหน้าต่างยืนยันด้วย SweetAlert2
* **🛡️ Role-Based Access Control (RBAC):**
  * จัดการบทบาท (Roles) และกำหนดสิทธิ์ (Permissions) อย่างละเอียดผ่านหน้าจอ Matrix
  * การป้องกันสิทธิ์ระดับสูงสุด (Superadmin Protection) ป้องกันการแก้ไขหรือลบบัญชีแอดมินหลัก
  * การป้องกันช่องโหว่ IDOR ด้วยการเข้ารหัส ID 8 หลัก (**Hashids**) ซ่อน Primary Key ทั้งระบบ
* **🌍 100% Complete Bilingual Localization (i18n):**
  * รองรับภาษาไทย (`th`) และภาษาอังกฤษ (`en`) แบบสมมาตร 1:1 ครบทุกจุด (0 missing keys)
  * สลับภาษาได้ทันทีผ่าน Topbar Dropdown
  * มีสคริปต์ตรวจสอบความสมบูรณ์ `php compare_lang.php` และซิงค์คำแปลเข้าฐานข้อมูล `php sync_lang.php`
* **🎨 Modern UI/UX & Dynamic Theming:**
  * ออกแบบด้วย Tailwind CSS, Google Font **Prompt** (รองรับทั้งภาษาไทยและอังกฤษ) และ Phosphor Icons
  * รองรับ **Dark / Light Mode** เต็มรูปแบบ พร้อมสลับชุดสีธีมได้ 5 เฉดสี (Primary Color Palettes)
  * ตารางข้อมูล **Client-Side DataTables** โหลดข้อมูลทันทีแบบ Zero-FOUC โดยไม่มีการกระพริบของหน้าเว็บ
  * แถบแสดงความคืบหน้าด้านบนสไตล์ YouTube (**YouTube-style Top Preloader**) ไร้แสงฟุ้ง ไม่ทำให้หน้าจอขาวตอนเปลี่ยนหน้า
  * เมนูด้านข้างและแท็บหน้าจอตั้งค่าแบบ **Sticky Navigation** ล็อกตำแหน่งสะดวกต่อการใช้งาน
  * ระบบแจ้งเตือน **SweetAlert2 Toast ตำแหน่งบนกลาง (`position: 'top'`)** สวยงาม เรียบหรู
* **⚙️ System Settings & Design Specification:**
  * ตั้งค่าชื่อเว็บไซต์, โลโก้, Favicon, พื้นหลัง, ข้อความหน้า Login, โหมดปิดปรับปรุง (Maintenance Mode)
  * หน้าจอ **Design Specification Portal** (`/settings/design_spec`) แสดงเอกสารสถาปัตยกรรมระบบ โครงสร้าง 12 ตารางฐานข้อมูล นโยบายความปลอดภัย และ Playground ทดสอบ UI
* **📊 Activity & Audit Logging:**
  * บันทึกประวัติกิจกรรมผู้ใช้งาน (Activity Logs) และบันทึกการเปลี่ยนแปลงข้อมูลระดับลึกพร้อม JSON Diff Viewer (Audit Logs)

---

## 🛠️ สถาปัตยกรรมและเทคโนโลยี (Tech Stack)

* **Backend:** PHP 8.4+ (Pure PHP MVC, PSR-4 Autoloading)
* **Database:** MySQL / MariaDB (PDO พร้อม Prepared Statements ป้องกัน SQL Injection 100%)
* **Frontend:** Tailwind CSS (CDN), Phosphor Icons, SweetAlert2, DataTables, Axios, Chart.js
* **Security Stack:** Hashids, Google2FA, CSRF Token Protection, Rate Limiter, ErrorHandler

---

## 🚀 คู่มือการติดตั้งสำหรับโปรเจกต์ใหม่ (Installation Guide)

### สิ่งที่ต้องเตรียม (Prerequisites)
* PHP 8.2 หรือสูงกว่า (แนะนำ PHP 8.4+)
* MySQL 8.0+ หรือ MariaDB 10.5+
* Composer
* Web Server (Apache พร้อมเปิดใช้งาน `mod_rewrite` หรือ Nginx หรือ Laragon)

### ขั้นตอนการติดตั้ง
1. **Clone โปรเจกต์ลงมาที่เครื่อง:**
   ```bash
   git clone https://github.com/kokostudio/php_ums.git my_project
   cd my_project
   ```

2. **ติดตั้ง Dependencies ผ่าน Composer:**
   ```bash
   composer install
   ```

3. **ตั้งค่า Environment (.env):**
   คัดลอกไฟล์ `.env.example` เป็น `.env`:
   ```bash
   cp .env.example .env
   ```
   จากนั้นแก้ไขค่าการเชื่อมต่อฐานข้อมูลใน `.env`:
   ```ini
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=php_ums
   DB_USERNAME=root
   DB_PASSWORD=
   APP_URL=http://php_ums.localhost
   ```

4. **สร้างโครงสร้างตารางและข้อมูลเริ่มต้น (Migration & Seeds):**
   ```bash
   vendor/bin/phinx migrate
   vendor/bin/phinx seed:run
   ```

5. **ซิงค์พจนานุกรมภาษาเข้าสู่ฐานข้อมูล:**
   ```bash
   php sync_lang.php
   ```

6. **ข้อมูลเข้าสู่ระบบเริ่มต้น (Default Superadmin):**
   * **URL:** `http://php_ums.localhost/login`
   * **Username:** `superadmin`
   * **Password:** `password`
   *(แนะนำให้เปลี่ยนรหัสผ่านทันทีหลังเข้าสู่ระบบครั้งแรกในหน้า My Profile)*

---

## 📋 กฎเหล็กและมาตรฐานการพัฒนา (Mandatory Standards)

สำหรับนักพัฒนาและ AI Agent ทุกครั้งที่ทำงาน สร้าง หรือปรับปรุงโมดูลใหม่ **ต้องยึดถือมาตรฐานต่อไปนี้อย่างเคร่งครัด**:

1. 📖 **กฎข้อที่ 1: ต้องอ่านกฎใน `rules.md` และ `GEMINI.md` ทุกครั้งก่อนเริ่มงาน** เพื่อรักษามาตรฐานสถาปัตยกรรมและความปลอดภัย 100%
2. 🌐 **ใส่ภาษาให้ครบ 100% ทุกครั้งที่สร้างสิ่งใหม่:** ห้าม Hardcode ข้อความใน View หรือ JS เด็ดขาด ต้องเรียกผ่าน `__('namespace.key')` โดยมีคำแปลสมมาตร 1:1 ทั้งใน `app/Lang/th/` และ `app/Lang/en/` เสมอ (ตรวจสอบด้วย `php compare_lang.php` ต้องได้ผลลัพธ์ `[]`)
3. ⏳ **สถานะ Preloading ของปุ่มและการป้องกัน Double-Submit:** ทุกปุ่ม Action หรือ Submit ต้อง Disable ทันทีเมื่อคลิก พร้อมแสดงไอคอนหมุน `<i class="ph ph-circle-notch animate-spin mr-2"></i>` และข้อความสถานะกำลังโหลด
4. 🔔 **SweetAlert2 Toast ตำแหน่งบนกลาง:** การแจ้งเตือน Toast ทุกจุดต้องตั้งค่า `position: 'top'` (บนกลาง) เสมอ และห้ามใช้ Native `alert()`, `confirm()` ของเบราว์เซอร์
5. 🚫 **ห้ามใช้สัญลักษณ์สกุลเงินบาท:** ห้ามใช้สัญลักษณ์ `฿` ทุกที่ ให้ใช้คำว่า 'บาท' หรือ 'THB' เท่านั้น
6. 🔒 **ความปลอดภัยของ ID:** ใช้ Clean URL เสมอ และต้องถอดรหัส ID (`decodeId`) ทันทีที่รับค่า และเข้ารหัส ID (`encodeId`) ก่อนส่งออกไปที่หน้าบ้าน
7. 🖼️ **โลโก้และไอคอน:** ใช้ Inline Vector SVG หรือ Asset ภายในระบบเท่านั้น ห้ามใช้ Hotlink รูปภาพภายนอกที่อาจโหลดไม่ขึ้น

---

## 🤖 ตัวอย่างคำสั่งสำหรับสั่งงาน AI Assistant

```text
"ฉันต้องการสร้างโมดูลใหม่ชื่อ [ชื่อโมดูล เช่น Inventory] 
โปรดอ่านกฎและมาตรฐานทั้งหมดจาก rules.md และ .agents/skills/php-ums-core-workflow/SKILL.md 
และสร้าง Controller, Model, View, Route พร้อมระบบสิทธิ์ RBAC 
โดยต้องใส่ภาษาครบทั้ง TH/EN 100% ป้องกัน Double-Submit ที่ปุ่ม และใช้ SweetAlert2 Toast ตำแหน่งบนกลาง"
```
