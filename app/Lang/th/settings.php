<?php
// app/Lang/th/settings.php
return [
    'title'           => 'ตั้งค่าระบบ',
    'general'         => 'ข้อมูลทั่วไป',
    'login_page'      => 'หน้าเข้าสู่ระบบ',
    'email_settings'  => 'ตั้งค่าอีเมล',
    'coming_soon'     => 'เร็วๆ นี้',

    // General
    'site_name'       => 'ชื่อเว็บไซต์',
    'site_name_hint'  => 'ชื่อนี้จะแสดงที่มุมซ้ายบน หัวข้อหน้าเว็บ และอีเมลแจ้งเตือน',
    'footer_text'     => 'ข้อความส่วนท้าย',
    'footer_hint'     => 'แสดงที่ด้านล่างของแดชบอร์ด',
    'site_logo'       => 'โลโก้เว็บไซต์',
    'site_logo_hint'  => 'แสดงในแถบด้านข้างและหน้าเข้าสู่ระบบ',
    'site_favicon'    => 'ไอคอนเว็บไซต์ (Favicon)',
    'site_favicon_hint'=> 'ไอคอนบนแท็บเบราว์เซอร์',
    'ph_app_name'     => 'เช่น ระบบจัดการเอกสาร',
    'ph_footer'       => 'เช่น &copy; 2026 บริษัทของฉัน',

    // Login
    'login_title'     => 'หัวข้อหน้าเข้าสู่ระบบ',
    'login_title_hint'=> 'หัวข้อหลักที่แสดงบนหน้าเข้าสู่ระบบ',
    'login_subtitle'  => 'คำอธิบายหน้าเข้าสู่ระบบ',
    'login_subtitle_hint'=> 'ข้อความย่อยใต้ชื่อระบบ',
    'ph_welcome'      => 'เช่น ยินดีต้อนรับกลับมา!',
    'ph_login_sub'    => 'เช่น กรุณากรอกข้อมูลของคุณเพื่อเข้าสู่ระบบ',

    // Themes
    'login_logo'      => 'โลโก้หน้าเข้าสู่ระบบ',
    'login_logo_hint' => 'ใช้โลโก้นี้แทนโลโก้หลักในหน้าเข้าสู่ระบบ',
    'login_bg'        => 'ภาพพื้นหลัง',
    'login_bg_hint'   => 'ภาพพื้นหลังสำหรับหน้าเข้าสู่ระบบ (ไม่บังคับ)',
    'remove'          => 'ลบออก',

    'save_settings'   => 'บันทึกการตั้งค่า',
    'settings_saved'  => 'บันทึกการตั้งค่าสำเร็จ',
    'general_info'    => 'ข้อมูลทั่วไป',
    'manage_desc'     => 'จัดการการตั้งค่าทั่วไปของระบบ',
    
    'system_status'   => 'สถานะระบบ',
    'maintenance_mode'=> 'โหมดซ่อมบำรุง',
    'maintenance_mode_desc' => 'เมื่อเปิดใช้งาน เฉพาะผู้ดูแลระบบสูงสุด (Super Admin) เท่านั้นที่สามารถเข้าถึงระบบได้',
    'enable_registration'   => 'เปิดรับสมัครสมาชิก',
    'enable_registration_desc' => 'อนุญาตให้ผู้ใช้ใหม่สามารถสมัครสมาชิกได้',
    
    // Security
    'security' => 'ความปลอดภัย',
    'pwd_policy' => 'นโยบายรหัสผ่าน',
    'min_pwd_length' => 'ความยาวรหัสผ่านขั้นต่ำ',
    'min_pwd_length_desc' => 'ความยาวรหัสผ่านขั้นต่ำสำหรับผู้ใช้ใหม่และการเปลี่ยนรหัสผ่าน',
    'req_upper' => 'ต้องมีตัวอักษรพิมพ์ใหญ่ (A-Z)',
    'req_lower' => 'ต้องมีตัวอักษรพิมพ์เล็ก (a-z)',
    'req_number' => 'ต้องมีตัวเลข (0-9)',
    'req_special' => 'ต้องมีอักขระพิเศษ (!@#$...)',
    
    '2fa_admin' => 'การยืนยันตัวตนแบบสองขั้นตอน (2FA)',
    'force_2fa_admin' => 'บังคับใช้ 2FA สำหรับ Admin',
    'force_2fa_admin_desc' => 'หากเปิดใช้งาน ผู้ใช้ที่มีสิทธิ์ระดับ Admin จะถูกบังคับให้ตั้งค่า 2FA ทันทีที่เข้าสู่ระบบ',
    
    'login_security' => 'ความปลอดภัยการเข้าสู่ระบบ & Rate Limiting',
    'max_login_attempts' => 'จำนวนครั้งที่เข้าสู่ระบบผิดพลาดสูงสุด',
    'max_login_attempts_desc' => 'จำนวนครั้งที่อนุญาตให้เข้าสู่ระบบผิดพลาด ก่อนที่จะถูกบล็อกชั่วคราว',
    'login_lockout_time' => 'ระยะเวลาบล็อกบัญชี (นาที)',
    'login_lockout_time_desc' => 'ระยะเวลาที่บัญชีจะถูกล็อคชั่วคราวหากเข้าสู่ระบบผิดพลาดเกินจำนวนครั้ง',
    'session_lifetime' => 'ระยะเวลาของเซสชัน (นาที)',
    'session_lifetime_desc' => 'ระยะเวลาที่อนุญาตให้ไม่มีการใช้งานก่อนที่ระบบจะล็อกเอาต์อัตโนมัติ',
    'email_verification' => 'การยืนยันอีเมล',
    'require_email_verification' => 'บังคับยืนยันอีเมล',
    'require_email_verification_desc' => 'บังคับให้ผู้ใช้งานใหม่ต้องยืนยันอีเมลก่อนถึงจะเข้าสู่ระบบได้',

    // Social Login
    'social_login' => 'Social Login',
    'social_login_desc' => 'ตั้งค่าการเข้าสู่ระบบผ่านบัญชีโซเชียลมีเดีย',
    'google_login' => 'Google Login',
    'enable_google_login' => 'เปิดใช้งาน Google Login',
    'google_client_id' => 'Google Client ID',
    'google_client_secret' => 'Google Client Secret',
    'google_redirect_uri' => 'Google Redirect URI',
    'line_login' => 'LINE Login',
    'enable_line_login' => 'เปิดใช้งาน LINE Login',
    'line_client_id' => 'LINE Client ID',
    'line_client_secret' => 'LINE Client Secret',
    'line_redirect_uri' => 'LINE Redirect URI',
];
