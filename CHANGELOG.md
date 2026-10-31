# บันทึกการเปลี่ยนแปลง (Changelog)

การเปลี่ยนแปลงที่สำคัญทั้งหมดของโปรเจกต์ **Standard Core System** จะถูกบันทึกไว้ในไฟล์นี้

รูปแบบการบันทึกอ้างอิงตาม [Keep a Changelog](https://keepachangelog.com/th/1.0.0/) 
และโปรเจกต์นี้ยึดหลักการตั้งชื่อเวอร์ชันแบบ [Semantic Versioning](https://semver.org/spec/v2.0.0.html)

## [ยังไม่ปล่อยเวอร์ชัน (Unreleased)]
### เพิ่ม (Added)
- **ระบบทดสอบอัตโนมัติ (PHPUnit):** เพิ่ม `tests/` + `phpunit.xml` ครอบคลุม Security (AES-256-GCM round-trip/tamper, Hashids, sanitize), UploadManager (ปฏิเสธไฟล์ .php ปลอมเป็นรูป, MIME mismatch, base64 magic bytes, ขนาดเกิน), Router (route compilation) และ Language Parity — รันด้วย `vendor/bin/phpunit`
- **CI Pipeline:** เพิ่ม `.github/workflows/ci.yml` รัน lint + PHPUnit + ตรวจคู่ภาษาอัตโนมัติทุก push (PHP 8.4)
- **Global CSRF Enforcement:** Router บังคับตรวจ CSRF token ทุก POST/PUT/PATCH/DELETE อัตโนมัติ (ยกเว้น `/api/*` ที่ใช้ JWT และ `/install/*` ก่อนล็อก) — จุดที่เคยหลุด (logout, notifications) ได้รับการคุ้มครองครบ, กรณ fail ตอบ 419 JSON สำหรับ AJAX
- **Security Headers ทั้งระบบ:** `App\Core\SecurityHeaders` ส่ง X-Frame-Options (DENY), X-Content-Type-Options, Referrer-Policy, Permissions-Policy, HSTS (HTTPS) และ CSP โหมด Report-Only ทุก request
- **Safe Redirect รวมศูนย์:** `App\Core\Redirect` — redirect ภายในยอมรับเฉพาะ relative path, redirect ภายนอก (OAuth) ต้องผ่าน host allowlist
- **`App\Core\Request::wantsJson()`:** ตรวจ AJAX/JSON จากจุดเดียว (Controller, Middlewares, ErrorHandler, Router ใช้ร่วมกัน)
- **`Logger::error()/warning()`:** บันทึก error ระดับระบบลง `storage/logs/error.log` พร้อม metadata อัตโนมัติ (user, IP, URI) + หมุนไฟล์อัตโนมัติเมื่อเกิน 5MB (เก็บ 3 ฉบับ)
- **Permission Gate ครบระบบ:** Settings ต้องมีสิทธิ์ `settings/view`+`settings/edit`, Logs ต้องมี `logs/view` (เพิ่ม permission ใหม่พร้อม grant ให้ Super Admin/Admin) — sidebar และ Search Palette แสดงเฉพาะเมนู/ผลค้นหาที่มีสิทธิ์
- **Session หมดอายุระหว่าง AJAX:** AuthMiddleware ตอบ 401 JSON พร้อม `session_expired` → ajax_engine แสดง toast และพาไป `/login` (แก้ปัญหา "toast สำเร็จปลอม")
- **AJAX Engine 2.0:** ตรวจชนิดข้อมูลตอบกลับก่อนตีความ (กัน HTML หลุดเข้ามา), รองรับ `session_expired`, define `closeActiveModal` ให้ flag `modal_close` ใช้งานได้จริง
- ฟอร์ม 2FA enable/disable และ Social Disconnect ทั้ง 4 ผู้ให้บริการเป็น AJAX แบบ stay + ยืนยันผ่าน SweetAlert2 (`data-confirm-text`) แทน native `confirm()`
- ย้ายเครื่องมือ CLI สู่ `cli/`: `compare_lang.php`, `sync_lang.php` (อัปเดตคำสั่งในเอกสารทุกจุด)
- เพิ่มคีย์ภาษาใหม่ TH/EN ครบถ้วน (upload, csrf, session, delete, secret placeholder ฯลฯ)

### แก้ไข (Fixed)
- **ปิดช่องโหว่ RCE ผ่านไฟล์อัปโหลด:** ทุก flow (avatar, base64 crop, logo/favicon/พื้นหลัง) ผ่าน `UploadManager` ที่ตรวจ extension whitelist คู่กับ MIME จริง + magic bytes + สร้างชื่อไฟล์สุ่ม (เดิมตรวจเฉพาะ MIME แต่เอา extension จากผู้ใช้ ทำให้อัปโหลด .php ได้)
- **แก้ toast "สำเร็จ" ปลอม 2 จุด:** `UserController::update` (password complexity / superadmin guard) เปลี่ยนเป็น `respond()` พร้อม errors รายฟิลด์ และ ajax_engine ตรวจ typeof ของ response
- **แก้ปุ่ม Disconnect Facebook/GitHub ไม่ทำงาน:** เพิ่มใน whitelist ของ `disconnectSocial` พร้อมกันตัดการเชื่อมต่อวิธีเข้าสู่ระบบสุดท้าย (ต้องมีรหัสผ่านหรือโซเชียลอื่นก่อน)
- **แก้ Social Login + 2FA พัง:** session key ชื่อต่างกัน (`temp_user_id` vs `2fa_pending_user_id`) — unify แล้วพร้อม expiry 5 นาที
- **RateLimiter เลิกเชื่อ X-Forwarded-For ตามอำเภอใจ:** ใช้ REMOTE_ADDR เป็นหลัก เชื่อ XFF เฉพาะเมื่อตั้ง `TRUSTED_PROXIES` (ปิดช่องบายพาส brute-force lockout)
- **`?debug=1` ไม่เปิด stack trace บน production แล้ว:** ผูกกับ `APP_DEBUG=true` เท่านั้น หน้า error ใน production แสดงข้อความ generic (ไม่มี file:line) และตอบ JSON สำหรับ AJAX
- **Token ความปลอดภัย:** reset/verify token เก็บเป็น SHA-256 hash ใน DB, JWT ปฏิเสธ secret ค่า default, อัปเกรด encryption จาก AES-256-CBC เป็น AES-256-GCM (authenticated)
- **APP_KEY จริงรั่วผ่าน .env.example:** หมุนค่าใหม่, example เว้นว่าง (installer/CLI สุ่มให้อัตโนมัติ), `APP_DEBUG=false` เป็นค่าเริ่มต้น
- **Stored XSS ผ่านระบบคำแปล:** sanitize ค่าแปลก่อนบันทึก + escape ข้อมูลก่อน highlight ใน Search Palette + escape การแจ้งเตือน + `json_encode` ฝัง `<script>` ใส่ JSON_HEX_* ครบทุกจุด
- **OAuth Client Secret ไม่ถูก echo กลับหน้าเว็บ:** เปลี่ยนเป็น `type="password"` + ช่องว่าง = คงค่าเดิม
- **Module Isolation:** ไฟล์ route ของ Module ใดพัง ระบบ log แล้วข้าม — เส้นทางอื่นไม่ล่มตาม (เคยเกิดเหตุจริง)
- **Router:** ตอบ 405 Method Not Allowed เมื่อ path ตรงแต่ method ไม่ตรง + เตือน log เมื่อ route ซ้ำ
- **`Logger::error()` ที่ถูกเรียกแต่ไม่มีอยู่จริง** ใน SettingController (เคยเป็น fatal ซ้อนเมื่อเกิด exception)
- **DataTables:** LIKE search escape `%`/`_`, รองรับ `length=-1` (All), LogController ใช้ `DataTableHelper` ตามกฎ Rule 14 (ลดโค้ดซ้ำ ~140 บรรทัด)
- **Delete users/roles** reload ตารางแบบเนียน (`ajax.reload`) ไม่กระพริบทั้งหน้า + payload รูปแบบเดียว `{success, message}` + non-AJAX fallback ครบ
- **ถอด DDL (ALTER TABLE) ออกจาก request จริง** ใน ProfileController (ย้ายเป็น migration เฉยๆ)
- **2FA pending มีอายุ 5 นาที**, login กัน timing-based username enumeration (dummy verify), `/reset-password` และ `/install/upgrade` มี throttle กัน brute-force และไม่ leak error message

### เปลี่ยนแปลงโครงสร้าง (Changed)
- **เก็บกวาด Repository:** ลบไฟล์เก่า/ขยะออกจาก git ทั้งหมด (old_*.txt, test_*.php, diff*.txt, AuthController_backup*, scratch/, .mimosa/, รูป uploads จริง, implementation_plan.md, walkthrough.md) + ปรับ `.gitignore` ครอบคลุม (เก็บโครง uploads ผ่าน .gitkeep)
- **Session Cookie:** เพิ่ม `secure` flag อัตโนมัติเมื่อรันบน HTTPS + เคลียร์ cookie ตอน logout
- **`.htaccess`:** บล็อกไฟล์อ่อนไหว (.env, .git, storage/, *.log, *.zip, old_/test_/diff files) ทำงานแม้ไม่มี mod_rewrite
- **เอกสารซิงก์กับโค้ด:** README (phinx → `php cli/migrate.php`, APP_KEY, TRUSTED_PROXIES, Tests/CI), rules.md (§26-27 ใหม่), GEMINI.md (กฎ 19-20 ใหม่), SKILL.md (หัวข้อ 33-34 ใหม่), INSTALL.md (PHP 8.4), .cursor rule
- เพิ่ม **Global AJAX Form Engine** (`app/Views/layouts/partials/ajax_engine.php`) สำหรับดักจับการส่งฟอร์มทั้งหมดที่มี `data-ajax="true"` ให้ทำงานผ่าน axios โดยหน้าเว็บไม่ต้องรีเฟรช พร้อมปุ่ม Loading (Rule 27), Toast บนกลาง (Rule 28), ไฮไลต์ข้อผิดพลาดรายฟิลด์ และระบบ Pending Toast ที่แสดงต่อเนื่องหลังเปลี่ยนหน้า
- เพิ่ม **Dual-Response Pattern** ใน `App\Core\Controller` เมธอด `isAjax()` และ `respond()` — ตอบ JSON เมื่อเป็น AJAX และตอบ Redirect + Flash ตามเดิมเมื่อเป็นฟอร์มปกติ (ไม่มี Breaking Changes ต่อโปรเจกต์ที่เชื่อมต่อ)
- ยกระดับฟอร์มทั้งระบบเป็น AJAX: Settings (ทุกแท็บ), Profile (ข้อมูล/รหัสผ่าน), Users & Roles (สร้าง/แก้ไข) และ Authentication ทั้งหมด (Login, Register, 2FA, Resend Verification, Forgot/Reset Password)
- ปุ่มส่งอีเมลยืนยันบัญชี (Resend Verification) แสดงแบบไดนามิกทันทีกรณีเข้าสู่ระบบด้วยบัญชีที่ยังไม่ยืนยันอีเมลในโหมด AJAX
- เพิ่มคีย์ภาษาไทย/อังกฤษใหม่ (`messages`) ครบทั้ง 2 ภาษา: การยืนยันอีเมล, ลิงก์รีเซ็ตรหัสผ่าน, รหัส 2FA ไม่ถูกต้อง, เซสชันหมดอายุ และผลลัพธ์การรีเซ็ตรหัสผ่าน
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
- กำหนดและบังคับใช้กฎสถาปัตยกรรมใหม่: ตารางข้อมูล DataTables ทั้งหมดของระบบต้องทำงานในรูปแบบ Server-Side Processing (`serverSide: true`) 100% ผ่าน `DataTableHelper` และ AJAX Endpoint พร้อมเข้ารหัส Hashids ในข้อมูล JSON เพื่อรองรับการขยายตัวของข้อมูลและประหยัดหน่วยความจำเบราว์เซอร์
- กำหนดและบังคับใช้กฎสถาปัตยกรรม Modal: หน้าต่าง Modal ทุกตัวในระบบต้องจัดโครงสร้างแบบ Fixed Header และ Fixed Footer (`flex-shrink-0`) พร้อมส่วนเนื้อหากลางที่เลื่อนได้ (`flex-1 overflow-y-auto`) และจำกัดความสูงไม่เกิน `max-h-[85vh] md:max-h-[90vh]` เพื่อให้ปุ่ม Action และปุ่มปิดมองเห็นได้ตลอดเวลาโดยไม่ต้องเลื่อนหน้าจอ พร้อมปรับปรุง Modal ในหน้า Logs, Profile และ Settings ให้เป็นไปตามมาตรฐานนี้
- ปรับปรุงหน้าตั้งค่า (`/settings/general`) ให้เลือกสี Sidebar ได้อิสระผ่าน **Color Picker** (รองรับ Preset 5 สี + Custom) พร้อมคำนวณ Primary Palette (50–950) และสีประกอบทั้งหมดให้อัตโนมัติ พร้อม Preview สีแบบ Real-time
- ปรับปรุง **Scrollbar ทั้งระบบ** ให้ใช้สีตามธีม Sidebar แบบ Dynamic ผ่าน CSS Variables (`--scrollbar-thumb`, `--scrollbar-thumb-dark`, `--scrollbar-sidebar-thumb` ฯลฯ) แยกสีตามพื้นผิว (โหมดสว่าง/โหมดมืด/ภายในแถบ Sidebar) พร้อมรับประกันอัตราส่วนคอนทราสต์ WCAG ขั้นต่ำ ไม่ว่าผู้ใช้จะเลือกสีใดก็ตาม

### แก้ไข (Fixed)
- แก้ไขข้อผิดพลาด `copyToClipboard is not defined` ในหน้าการตั้งค่าแท็บ Social จากการจำกัด Scope ฟังก์ชันผิดแท็บ
- นำแท็กปิด `</body></html>` ที่ตกค้างผิดตำแหน่งใน `app/Views/settings/index.php` ออก
- แก้ไขข้อผิดพลาดของฐานข้อมูลโดยเพิ่มตาราง `system_settings` ที่ตกหล่น เพื่อให้ระบบตั้งค่าสามารถจัดเก็บข้อมูลได้สมบูรณ์
- ปรับแก้คลาสสำหรับ Dark Mode ในตาราง Audit Logs (DataTables) ให้แสดงผลได้อย่างถูกต้องในทุกโหมดสี
- แก้ไขปัญหาข้อผิดพลาด 500 Internal Server Error ในหน้า Audit Logs ที่เกิดจาก Column `ip_address` หายไป
