# บันทึกการเปลี่ยนแปลง (Changelog)

การเปลี่ยนแปลงที่สำคัญทั้งหมดของโปรเจกต์ **Standard Core System** จะถูกบันทึกไว้ในไฟล์นี้

รูปแบบการบันทึกอ้างอิงตาม [Keep a Changelog](https://keepachangelog.com/th/1.0.0/) 
และโปรเจกต์นี้ยึดหลักการตั้งชื่อเวอร์ชันแบบ [Semantic Versioning](https://semver.org/spec/v2.0.0.html)

## [ยังไม่ปล่อยเวอร์ชัน (Unreleased)]
### เพิ่ม (Added)
- ตั้งค่าโครงสร้างโปรเจกต์เริ่มต้น
- ตั้งค่าระบบโหลดคลาสอัตโนมัติ (PSR-4 autoloader)
- ติดตั้งแพ็กเกจ `phpmailer/phpmailer` ผ่าน Composer สำหรับระบบส่งอีเมล
- เพิ่มไฟล์รองรับการ Deploy ลงบน Shared Hosting อย่างปลอดภัย (`.htaccess` ที่ Root และ `/public`)
- เพิ่มไฟล์จัดการตัวแปรระบบ Environment (`.env.example` และ `.env`)
- จัดทำเอกสารข้อบังคับและกฎเกณฑ์สถาปัตยกรรมของโปรเจกต์ (`rules.md`)
- สร้างไฟล์บันทึกการเปลี่ยนแปลง (`CHANGELOG.md`)
- สร้างโครงสร้างระบบ MVC หลัก (Router, View, Controller, Database)
- สร้างระบบ Layout และ View Engine สำหรับ Template หลัก (`app.php`)
- สร้างระบบฐานข้อมูลด้วย PDO แบบ Singleton Pattern พร้อมรองรับ Migration
- สร้างคลาส `Security` เพื่อรองรับระบบ CSRF Protection และการเข้ารหัสรหัสผ่าน
- สร้างคลาส `Session` เพื่อจัดการ Flash Messages และ CSRF Token
- สร้างระบบ `Auth` และระบบการจัดเก็บ Log เข้าฐานข้อมูล (Activity Logs & Audit Logs)
- สร้างระบบ `RBAC` (Role-Based Access Control) สำหรับตรวจสอบสิทธิ์ผู้ใช้งาน และ `PermissionMiddleware`
- พัฒนาโมดูล **User Management** พร้อมหน้าจอแสดงผล DataTables แบบ AJAX
- พัฒนาโมดูล **Role & Permission Management** พร้อมหน้าจอ Permission Matrix
- พัฒนาโมดูล **Activity & Audit Logs Viewer** แบบ DataTables Server-side พร้อมหน้าจอ JSON Diff
- พัฒนาโมดูล **System Settings UI** สำหรับตั้งค่าชื่อเว็บไซต์และลิขสิทธิ์
- พัฒนาหน้าจอ **Login & Authentication UI** แยก Layout โทนสะอาดตา
- พัฒนาหน้าจอ **My Profile** สำหรับอัปเดตข้อมูลผู้ใช้และเปลี่ยนรหัสผ่านส่วนตัว
- พัฒนาระบบอัปโหลดรูปภาพโปรไฟล์ (Profile Image) สำหรับผู้ใช้แต่ละคน
- สร้างคลาส `Lang` เพื่อรองรับระบบ Multi-language (i18n) รองรับภาษาไทยและอังกฤษ
- ปรับปรุง DataTables ให้เป็นรูปแบบ Premium UI และตั้งค่าภาษาไทย/อังกฤษตาม Locale แบบ Global
- เพิ่มระบบกรองข้อมูล (Filter) ด้วย Select2 Dropdown และเพิ่มช่องค้นหาแบบ Custom ไว้ตำแหน่งเดียวกัน
- แก้ไขข้อผิดพลาด (Error) ของระบบค้นหา DataTables (PDO Parameter Reuse)
- ปรับปรุง UI ของ DataTables โดยย้ายกล่องเลือกจำนวนรายการ (Show entries) ไปไว้ด้านล่างสุดร่วมกับ Pagination
- ปรับปรุงระบบทั้งหมดให้รองรับ Responsive Design (ซ่อน Sidebar และแสดงปุ่ม Hamburger บนมือถือ, ตารางเลื่อนแนวนอนได้)
- ปรับปรุง Layout หลักของระบบให้ขยายเต็มหน้าจอ (Full Width) เพื่อให้ตาราง DataTables ใช้พื้นที่แสดงผลได้อย่างคุ้มค่าบนหน้าจอขนาดใหญ่
- ปรับปรุงหน้าแสดงผลข้อมูล (Activity Logs, Audit Logs, Roles) ให้ใช้รูปแบบ UI ทิศทางเดียวกันกับหน้า Users ทั้งหมด (Custom Search Bar)
- เพิ่มระบบยืนยันตัวตน (Confirmation Dialog) ด้วย SweetAlert2 ก่อนออกจากระบบ (Logout) เพื่อป้องกันการกดพลาด
- ปรับปรุง SweetAlert2 ให้แสดงผลแบบ Premium UI และรองรับ Dark Mode เต็มรูปแบบ
- พัฒนาระบบ **Forgot Password** สำหรับขอลิงก์รีเซ็ตรหัสผ่านผ่านอีเมล พร้อมระบบ Token หมดอายุภายใน 1 ชั่วโมง
- ขยายความสามารถ **System Settings** ให้รองรับการเปลี่ยน Logo, Favicon, พื้นหลังและข้อความในหน้า Login
- เพิ่มระบบเข้ารหัส ID แบบ 8 หลัก (**Hashids**) ป้องกัน IDOR และซ่อน Primary Key ในทุกส่วนของ User และ Role Management
- ปรับเปลี่ยนเส้นทาง URL เป็น **Clean URL** (เช่น `/settings/general`, `/settings/login`) แทนการใช้ Query String
- ปรับปรุงสไตล์ UI ทั้งระบบ รองรับ Dark / Light Mode เต็มรูปแบบ พร้อม Custom Scrollbar แบบโมเดิร์น
- เปลี่ยนฟอนต์หลักทั้งระบบเป็น Google Font **Prompt** สำหรับรองรับทั้งภาษาไทยและอังกฤษ
- ปรับปรุงรูปแบบการแสดงผลวันที่และเวลาทั้งระบบให้เป็นมาตรฐานเดียวกัน **`30/12/2026`** (`d/m/Y` และ `d/m/Y H:i:s`) พร้อมสร้าง Helper function `formatDate()` ทั้งฝั่ง PHP Backend และ JavaScript Frontend
- พัฒนาระบบรองรับหลายภาษา (**Multi-Language / i18n**) เริ่มต้นด้วยภาษาไทยเป็นภาษาหลัก (`th`) และภาษาอังกฤษ (`en`) รองรับการสลับภาษาผ่าน `GET /lang/{locale}` พร้อมปุ่มสลับภาษาใน Topbar และแปลข้อความทั้งระบบผ่านฟังก์ชัน `__()`
- พัฒนาระบบ **เมนูโปรด (Pinned Sidebar Menus)** ที่บันทึกผ่าน LocalStorage ช่วยให้ผู้ใช้ปักหมุดเมนูที่ใช้บ่อยไว้ด้านบนสุดของ Sidebar ได้อย่างง่ายดาย
- เพิ่มหน้าจอ **ข้อกำหนดการออกแบบ (Design Specification)** เข้าสู่ระบบการตั้งค่า (`/settings/design_spec`) พร้อมเมนูนำทางในแท็บ Settings, รองรับการค้นหาผ่าน Ctrl+K, มีระบบคัดลอก Markdown, พิมพ์เอกสาร, รายละเอียดสถาปัตยกรรม 12 ตารางฐานข้อมูล, ความปลอดภัย (Hashids, 2FA, Rate Limiting, CSRF), มาตรฐาน UI/UX และชุดทดสอบการแจ้งเตือน (Notifications & Preloader Playground)
- กำหนดและบังคับใช้ **กฎเหล็กมาตรฐานแกนหลัก 4 ข้อ** (กฎข้อที่ 1 ต้องอ่านกฎใน `rules.md` และ `GEMINI.md` ทุกครั้งก่อนเริ่มงาน, กฎใส่ภาษาครบ 100% ทั้ง TH/EN แบบสมมาตร 1:1, กฎสถานะ Preloading ของปุ่มและการป้องกัน Double-Submit, กฎการแจ้งเตือน SweetAlert2 Toast ตำแหน่งบนกลาง `position: 'top'`)
- พัฒนาระบบ **Social Login & OAuth 4 ผู้ให้บริการหลัก** (Google, LINE, Facebook, GitHub) ในหน้าตั้งค่า `/settings/social` และหน้าล็อกอิน `/login`
- ปรับเปลี่ยนโลโก้แบรนด์ Social Login ทุกตัวเป็น **Inline Vector SVG** คุณภาพสูง ตัดปัญหาภาพโหลดไม่ขึ้นหรือบั๊ก SSL Revocation จาก Wikimedia Commons
- เพิ่มคอลัมน์ `facebook_id` และ `github_id` ในตาราง `users` รองรับการผูกบัญชีและเข้าสู่ระบบด้วย Facebook และ GitHub
- เพิ่มระบบคัดลอกลงคลิปบอร์ดแบบ Dual Feedback (เปลี่ยนไอคอนเป็นเครื่องหมายถูกสีเขียว 2 วินาที + SweetAlert2 Toast บนกลาง) พร้อมรองรับ Fallback สำหรับ Localhost HTTP
- ปรับปรุงหน้าโปรไฟล์ (`/profile`) ให้รองรับการเชื่อมต่อและตัดการเชื่อมต่อบัญชีโซเชียลครบทั้ง 4 แพลตฟอร์ม พร้อมยืนยันด้วย SweetAlert2 Dialog (ยกเลิก Native `confirm()`)
- ปรับปรุงเอกสารโครงการและคู่มือนักพัฒนาใน `README.md` ตามโครงสร้างมาตรฐาน 8 เสาหลัก (Overview, Map, Setup, Test, Conventions, Logic, Never, Done)

### แก้ไข (Fixed)
- แก้ไขข้อผิดพลาด `copyToClipboard is not defined` ในหน้าการตั้งค่าแท็บ Social จากการจำกัด Scope ฟังก์ชันผิดแท็บ
- นำแท็กปิด `</body></html>` ที่ตกค้างผิดตำแหน่งใน `app/Views/settings/index.php` ออก
- แก้ไขข้อผิดพลาดของฐานข้อมูลโดยเพิ่มตาราง `system_settings` ที่ตกหล่น เพื่อให้ระบบตั้งค่าสามารถจัดเก็บข้อมูลได้สมบูรณ์
- ปรับแก้คลาสสำหรับ Dark Mode ในตาราง Audit Logs (DataTables) ให้แสดงผลได้อย่างถูกต้องในทุกโหมดสี
- แก้ไขปัญหาข้อผิดพลาด 500 Internal Server Error ในหน้า Audit Logs ที่เกิดจาก Column `ip_address` หายไป
