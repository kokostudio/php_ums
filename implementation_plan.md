# Implementation Plan: Standard Core System (PHP 8.4+ Pure MVC)

ระบบแกนหลักมาตรฐาน (Standard Core System) ที่มีระบบ User, RBAC, Logs และรองรับโครงสร้างแบบ Module

## User Decisions (Approved)
- **UI Framework:** Tailwind CSS
- **Database Schema Management:** Custom PHP Migration & Seeder system
- **Email:** PHPMailer Integration for SMTP

---

## 1. Directory Structure & Architecture
ยึดตามมาตรฐาน PSR-4 (Namespace `App\`) และเป็นแบบ Single Entry Point (วิ่งเข้า `public/index.php`)

```text
/
├── app/                      # Namespace: App\
│   ├── Core/                 # ระบบแกนกลาง
│   │   ├── Application.php   # ตัวจัดการ Bootstrap
│   │   ├── Router.php        # ระบบ Routing
│   │   ├── Database.php      # PDO Connection (Singleton)
│   │   ├── Controller.php    # Base Controller
│   │   ├── Request.php       # จัดการ HTTP Request / Input
│   │   ├── Response.php      # จัดการ HTTP Response / JSON
│   │   └── Security.php      # จัดการ CSRF, XSS, Hashing
│   ├── Controllers/          # Core Controllers (Auth, User, Role)
│   ├── Models/               # Core Models
│   ├── Views/                # Template Files (PHP/HTML)
│   ├── Services/             # Business Logic / Service Layer
│   ├── Middlewares/          # กรอง Request (Auth, Permission, CSRF)
│   └── Modules/              # สำหรับระบบอื่นที่จะมาเชื่อมต่อในอนาคต (เช่น ERP, POS)
├── config/                   # ตั้งค่าระบบ (Database, App Config)
├── public/                   # Document Root ของ Web Server
│   ├── index.php             # Front Controller (Entry Point)
│   ├── .htaccess             # กฏการ Rewrite สำหรับ Web Server
│   └── assets/               # CSS, JS, Images (SweetAlert2, Axios, DataTables)
├── storage/                  # เก็บไฟล์ Log และอัปโหลดที่ซ่อนจากภายนอก
│   ├── logs/                 # System Error Logs
│   └── uploads/              # ไฟล์ที่ต้องการจำกัดสิทธิ์เข้าถึง
├── composer.json             # จัดการ PSR-4 Autoload
├── .env.example              # ตัวอย่างไฟล์ตั้งค่า Environment
├── .htaccess                 # (Root) Redirect ไปที่ public/ อัตโนมัติ (สำหรับ Shared Hosting)
└── rules.md                  # ข้อบังคับโปรเจกต์
```

---

## 2. Database Schema Design (Core Foundation)
โครงสร้าง Database เบื้องต้นสำหรับระบบแกนหลัก

- `users`: ข้อมูลผู้ใช้งาน (`id`, `username`, `password`, `email`, `role_id`, `status`)
- `roles`: ระดับสิทธิ์ (`id`, `name`, `description`)
- `permissions`: สิทธิ์การกระทำในแต่ละโมดูล (`id`, `module`, `action`, `description`)
- `role_permissions`: จับคู่ Role กับ Permission (`role_id`, `permission_id`)
- `activity_logs`: บันทึกการใช้งานทั่วไป (`id`, `user_id`, `action`, `module`, `ip_address`, `user_agent`, `created_at`)
- `audit_logs`: บันทึกการแก้ไขข้อมูลเชิงลึก (`id`, `user_id`, `table_name`, `record_id`, `old_values`, `new_values`, `action`, `created_at`)
- `files`: ระบบจัดการไฟล์แนบส่วนกลาง (`id`, `file_name`, `file_path`, `mime_type`, `size`, `uploaded_by`, `created_at`)
- `settings`: ระบบตั้งค่าพื้นฐานของเว็บไซต์ (`id`, `setting_key`, `setting_value`, `description`, `updated_at`)

---

## 3. Security Implementation Plan
> [!TIP]
> เพื่อให้ระบบนี้เป็น **มาตรฐาน** ที่ปลอดภัยที่สุด เราจะเตรียมฟีเจอร์ด้าน Security ไว้ที่ระดับ Core:

1. **SQL Injection:** บังคับใช้ `PDO Prepared Statements` ใน Core Model ทุกจุด
2. **CSRF Protection:** สร้าง Middleware สำหรับตรวจสอบ CSRF Token ในทุกๆ Request ที่เป็น POST, PUT, DELETE
3. **XSS Protection:** สร้าง Helper Function สำหรับทำ HTML Escaping ก่อนแสดงผลใน View เสมอ
4. **Authentication:** ใช้ `password_hash()` แบบ Argon2id หรือ Bcrypt สำหรับรหัสผ่าน และมีการ Regenerate Session ID เมื่อ Login สำเร็จ
5. **Rate Limiting & Lockout:** ป้องกัน Brute Force สำหรับหน้า Login (ล็อคบัญชีชั่วคราวหากใส่รหัสผิดเกิน x ครั้ง)
6. **2FA (Two-Factor Authentication):** [Optional/แนะนำ] เพิ่มความปลอดภัยอีกชั้นด้วย Google Authenticator หรือ Email OTP

---

## 4. Execution Roadmap (แผนการพัฒนา)

### Phase 1: Core Framework Setup & Deployment Prep
- [ ] Initialize `composer.json` และตั้งค่า PSR-4 Autoloader
- [ ] สร้างระบบอ่านไฟล์ `.env` สำหรับจัดการตัวแปร Environment (เช่น รหัสผ่าน Database) อย่างปลอดภัย
- [ ] สร้างคลาส `ErrorHandler` เพื่อจับ Exception และบันทึก Error ลงไฟล์ `storage/logs/` (ไม่พ่น Error ออกหน้าจอ)
- [ ] สร้าง Core Classes: `Router`, `Database (PDO)`, `Base Controller`, `View` (จัดการ Layout/Master Page)
- [ ] สร้าง Entry Point (`public/index.php`) 
- [ ] สร้างและตั้งค่า `.htaccess` สองจุด (ที่ Root และโฟลเดอร์ `public/`) เพื่อรองรับการ Deploy บน **Shared Hosting** อย่างปลอดภัย

### Phase 2: Security & Middleware
- [ ] สร้างระบบจัดการ Session และ CSRF Token
- [ ] สร้าง Middleware System (รองรับการทำ `AuthMiddleware` และ `RoleMiddleware`)
- [ ] สร้าง Security Helper (Sanitize Input, Escape Output)

### Phase 3: Auth & User Management (Core App)
- [ ] ออกแบบและสร้างไฟล์ฐานข้อมูล `.sql` หรือระบบ Migration
- [ ] พัฒนาระบบ Login / Logout 
- [ ] พัฒนาหน้าการจัดการ User (CRUD) โดยดึงข้อมูลผ่าน DataTables (Server-side) + Axios

### Phase 4: Role-Based Access Control (RBAC)
- [ ] พัฒนาหน้าจัดการ Role และ Permissions
- [ ] ผูกระบบ Role เข้ากับ Middleware เพื่อใช้เช็คสิทธิ์ก่อนเข้าถึง Controller/Action

### Phase 5: Logging System & Profile
- [ ] สร้าง Service สำหรับบันทึก Activity Log
- [ ] สร้าง Model Trait / Service สำหรับบันทึก Audit Log เมื่อมีการแก้ไขข้อมูลสำคัญ
- [ ] พัฒนาหน้าระบบ Profile สำหรับผู้ใช้งาน (รองรับการเปลี่ยนรหัสผ่าน)

### Phase 6: System Settings & Email/Notification Core
- [ ] พัฒนาหน้าการตั้งค่าระบบ (ชื่อเว็บ, โลโก้, Favicon, Footer, SMTP Email, Maintenance Mode)
- [ ] สร้าง Helper Function สำหรับดึงค่า Setting
- [ ] สร้าง Email Service (อาจใช้ PHPMailer) สำหรับเป็นแกนหลักในการส่งแจ้งเตือน

### Phase 7: Advanced Security & APIs (รองรับการเติบโต)
- [ ] สร้างระบบลืมรหัสผ่าน (Forgot Password) พร้อมส่งลิงก์เข้าอีเมล
- [ ] สร้างกลไก Rate Limiting (จำกัดจำนวนครั้งการล็อกอินผิดพลาด)
- [ ] เตรียมโครงสร้าง API Core Response (เผื่อให้ Module ภายนอกหรือ Mobile App ยิง API เข้ามาได้)

## Verification Plan
1. **Automated / Unit Testing:** (หากต้องการ สามารถเพิ่ม PHPUnit ในอนาคต)
2. **Manual Verification:**
   - ทดสอบ Routing ทุกลักษณะ (GET, POST พร้อม/ไม่พร้อม Parameters)
   - ทดสอบจำลองการโจมตี SQL Injection เพื่อดูการทำงานของ PDO
   - ทดสอบส่งฟอร์มโดยไม่มี CSRF Token เพื่อดูว่าถูก Block หรือไม่
   - ตรวจสอบ Audit Log ว่าบันทึกค่าเก่า/ใหม่ (Old/New values) ได้ถูกต้องเมื่อมีการ Update ข้อมูลผู้ใช้
