# Standard Core System (PHP UMS Boilerplate)

ระบบจัดการผู้ใช้ (User Management System) แบบพร้อมใช้งานทันที เหมาะสำหรับนำไปเป็น **"ฐานราก (Boilerplate)"** ในการพัฒนาต่อยอดเป็นระบบอื่นๆ เช่น ERP, CRM, POS หรือระบบจัดการสต็อกสินค้า โดยไม่ต้องเสียเวลาเขียนระบบ Login, ระบบจัดการสิทธิ์, หรือระบบตั้งค่าพื้นฐานใหม่

## ✨ ฟีเจอร์ที่มีให้ในตัว
* **Authentication:** ระบบ Login, สมัครสมาชิก, ยืนยันอีเมล, ลืมรหัสผ่าน
* **Social Login:** รองรับการเข้าสู่ระบบผ่าน Google และ LINE
* **Security:** 2FA (Two-Factor Auth), Rate Limiting, CSRF Protection
* **RBAC:** ระบบจัดการบทบาทและสิทธิ์ผู้ใช้งาน (Roles & Permissions)
* **Multi-language:** ระบบเปลี่ยนภาษา (ไทย/อังกฤษ) โหลดคำแปลจากฐานข้อมูล
* **Audit & Activity Logs:** ระบบบันทึกประวัติการใช้งานและประวัติระบบ
* **UI/UX:** หน้าจอ Dashboard ธีมสวยงามด้วย Tailwind CSS, Phosphor Icons, SweetAlert2 และ DataTables

---

## 🚀 คู่มือการติดตั้งสำหรับโปรเจกต์ใหม่ (Installation Guide)

### สิ่งที่ต้องมี (Prerequisites)
* PHP 8.1 หรือสูงกว่า
* MySQL หรือ MariaDB
* Composer

### ขั้นตอนการติดตั้ง
1. **Clone โปรเจกต์ลงมาที่เครื่องหรือเซิร์ฟเวอร์:**
   `ash
   git clone https://github.com/kokostudio/php_ums.git new_project_name
   cd new_project_name
   `

2. **ติดตั้ง Dependencies:**
   `ash
   composer install
   `

3. **ตั้งค่า Environment (.env):**
   คัดลอกไฟล์ .env.example เป็น .env (หากมี) หรือสร้างไฟล์ .env ใหม่และใส่ค่าเชื่อมต่อฐานข้อมูล
   `ash
   cp .env.example .env
   `
   *จากนั้นเปิดไฟล์ .env และแก้ไข DB_DATABASE, DB_USERNAME, DB_PASSWORD ให้ตรงกับฐานข้อมูลก้อนใหม่ของคุณ*

4. **สร้างตารางและข้อมูลตั้งต้น (Migration & Seeder):**
   โปรเจกต์นี้ใช้ Phinx ในการจัดการฐานข้อมูล ให้รันคำสั่งต่อไปนี้เพื่อสร้างโครงสร้างตารางและข้อมูลเริ่มต้น (คำแปล, บทบาท, แอดมิน):
   `ash
   vendor/bin/phinx migrate
   vendor/bin/phinx seed:run
   `

5. **การเข้าสู่ระบบครั้งแรก:**
   * **Username:** superadmin
   * **Password:** password
   *(แนะนำให้เปลี่ยนรหัสผ่านทันทีหลังเข้าสู่ระบบครั้งแรก)*

---

## 🛠 วิธีการต่อยอดและสร้างระบบใหม่ (For Developers & AI)

โครงสร้างระบบเป็นแบบ **Custom MVC**. เมื่อต้องการสร้างโมดูลใหม่ (เช่น Product) ให้ทำตามสแตนดาร์ดที่กำหนดไว้ในโฟลเดอร์ .agents/skills/php-ums-core-workflow/SKILL.md เสมอ เพื่อให้ระบบทำงานประสานกับ Core เดิมได้อย่างไร้รอยต่อ

### โครงสร้างที่แนะนำเมื่อเพิ่มโมดูลใหม่:
1. **Database:** สร้างไฟล์ Migration (ด้วย Phinx) เพื่อสร้างตารางใหม่
2. **Permission:** นำชื่อสิทธิ์ (เช่น products.view) ไปลงทะเบียนใน Database เพื่อให้ระบบ Role เดิมสามารถจัดการสิทธิ์โมดูลใหม่ได้
3. **Routing:** เพิ่ม Route ใน public/index.php โดยครอบด้วย AuthMiddleware และ PermissionMiddleware
4. **Controller/View:** ใช้ฟังก์ชัน __() ในการแสดงผลข้อความเสมอ และใช้ UI Components เดียวกับระบบผู้ใช้งาน

### 🤖 คำสั่งแนะนำเมื่อใช้ AI ช่วยเขียนโค้ด
ให้ก๊อปปี้ข้อความด้านล่างนี้ไปสั่ง AI เพื่อให้สร้างโมดูลใหม่ที่เข้ากับระบบได้อย่างสมบูรณ์:

> *"ฉันต้องการสร้างโมดูลใหม่ชื่อ **[ชื่อระบบ เช่น จัดการสินค้า]** โปรดอ่านสแตนดาร์ดการเขียนโค้ดและ UI ของโปรเจกต์จากไฟล์ .agents/skills/php-ums-core-workflow/SKILL.md และสร้าง Controller, View, Route พร้อมกับการเชื่อมต่อระบบสิทธิ์ (RBAC) ให้สอดคล้องกับระบบ UMS เดิม"*

