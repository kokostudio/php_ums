<?php
/**
 * System & UI/UX Design Specification View
 * Standard Core System (Pure PHP 8.4+ MVC UMS Boilerplate)
 */
?>

<div class="space-y-8" id="designSpecContainer">
    <!-- Top Card / Header Bar -->
    <div class="sticky top-[-24px] md:top-[-32px] z-20 -mt-6 md:-mt-8 -mx-6 md:-mx-8 px-6 md:px-8 py-5 bg-white/95 dark:bg-slate-900/95 backdrop-blur-md border-b border-gray-100 dark:border-slate-800 rounded-t-2xl flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 border border-indigo-100 dark:border-indigo-800">
                    <i class="ph ph-shield-check mr-1 text-sm"></i> Core Spec v1.0
                </span>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-800">
                    <i class="ph ph-check mr-1 text-sm"></i> Pure PHP 8.4+ MVC
                </span>
            </div>
            <h2 class="text-xl md:text-2xl font-bold text-gray-800 dark:text-white flex items-center gap-2">
                <i class="ph ph-file-text text-indigo-600 dark:text-indigo-400"></i>
                <?= __('settings.design_spec') ?>
            </h2>
            <p class="text-xs md:text-sm text-gray-500 dark:text-slate-400">
                <?= __('settings.design_spec_desc') ?>
            </p>
        </div>

        <div class="flex items-center gap-2.5 flex-wrap">
            <button type="button" onclick="copyDesignSpecMarkdown()" class="px-4 py-2 text-xs md:text-sm font-medium text-gray-700 dark:text-slate-300 bg-gray-100 dark:bg-slate-800 hover:bg-gray-200 dark:hover:bg-slate-700 rounded-xl transition-all shadow-xs flex items-center gap-2">
                <i class="ph ph-copy text-base"></i>
                <span><?= __('settings.ds_copy_spec') ?></span>
            </button>
            <button type="button" onclick="window.print()" class="px-4 py-2 text-xs md:text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl transition-all shadow-xs flex items-center gap-2">
                <i class="ph ph-printer text-base"></i>
                <span><?= __('settings.ds_print_btn') ?></span>
            </button>
        </div>
    </div>

    <!-- Quick Jump Sub-Navigation -->
    <div class="bg-gray-50 dark:bg-slate-900/60 p-3 rounded-2xl border border-gray-100 dark:border-slate-800">
        <div class="text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-slate-500 mb-2 px-1">
            <i class="ph ph-list-numbers mr-1"></i> Quick Jump Navigation (ข้ามไปยังหัวข้อ)
        </div>
        <div class="flex items-center gap-2 overflow-x-auto pb-1 text-xs">
            <a href="#sec-overview" class="px-3 py-1.5 rounded-lg bg-white dark:bg-slate-800 text-gray-700 dark:text-slate-300 hover:text-indigo-600 dark:hover:text-indigo-400 border border-gray-200/80 dark:border-slate-700 shadow-xs whitespace-nowrap transition-colors">
                <?= __('settings.ds_nav_overview') ?>
            </a>
            <a href="#sec-tech" class="px-3 py-1.5 rounded-lg bg-white dark:bg-slate-800 text-gray-700 dark:text-slate-300 hover:text-indigo-600 dark:hover:text-indigo-400 border border-gray-200/80 dark:border-slate-700 shadow-xs whitespace-nowrap transition-colors">
                <?= __('settings.ds_nav_tech') ?>
            </a>
            <a href="#sec-dir" class="px-3 py-1.5 rounded-lg bg-white dark:bg-slate-800 text-gray-700 dark:text-slate-300 hover:text-indigo-600 dark:hover:text-indigo-400 border border-gray-200/80 dark:border-slate-700 shadow-xs whitespace-nowrap transition-colors">
                <?= __('settings.ds_nav_dir') ?>
            </a>
            <a href="#sec-db" class="px-3 py-1.5 rounded-lg bg-white dark:bg-slate-800 text-gray-700 dark:text-slate-300 hover:text-indigo-600 dark:hover:text-indigo-400 border border-gray-200/80 dark:border-slate-700 shadow-xs whitespace-nowrap transition-colors">
                <?= __('settings.ds_nav_db') ?>
            </a>
            <a href="#sec-security" class="px-3 py-1.5 rounded-lg bg-white dark:bg-slate-800 text-gray-700 dark:text-slate-300 hover:text-indigo-600 dark:hover:text-indigo-400 border border-gray-200/80 dark:border-slate-700 shadow-xs whitespace-nowrap transition-colors">
                <?= __('settings.ds_nav_security') ?>
            </a>
            <a href="#sec-rbac" class="px-3 py-1.5 rounded-lg bg-white dark:bg-slate-800 text-gray-700 dark:text-slate-300 hover:text-indigo-600 dark:hover:text-indigo-400 border border-gray-200/80 dark:border-slate-700 shadow-xs whitespace-nowrap transition-colors">
                <?= __('settings.ds_nav_rbac') ?>
            </a>
            <a href="#sec-uiux" class="px-3 py-1.5 rounded-lg bg-white dark:bg-slate-800 text-gray-700 dark:text-slate-300 hover:text-indigo-600 dark:hover:text-indigo-400 border border-gray-200/80 dark:border-slate-700 shadow-xs whitespace-nowrap transition-colors">
                <?= __('settings.ds_nav_uiux') ?>
            </a>
            <a href="#sec-routes" class="px-3 py-1.5 rounded-lg bg-white dark:bg-slate-800 text-gray-700 dark:text-slate-300 hover:text-indigo-600 dark:hover:text-indigo-400 border border-gray-200/80 dark:border-slate-700 shadow-xs whitespace-nowrap transition-colors">
                <?= __('settings.ds_nav_routes') ?>
            </a>
            <a href="#sec-notifications" class="px-3 py-1.5 rounded-lg bg-white dark:bg-slate-800 text-gray-700 dark:text-slate-300 hover:text-indigo-600 dark:hover:text-indigo-400 border border-gray-200/80 dark:border-slate-700 shadow-xs whitespace-nowrap transition-colors">
                <?= __('settings.ds_nav_notif') ?>
            </a>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- SECTION 1: System Overview & Core Philosophy -->
    <!-- ========================================== -->
    <div id="sec-overview" class="scroll-mt-32 space-y-4">
        <div class="flex items-center gap-3 border-b border-gray-100 dark:border-slate-800 pb-3">
            <span class="w-8 h-8 rounded-xl bg-indigo-100 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold text-sm">1</span>
            <div>
                <h3 class="text-lg font-bold text-gray-800 dark:text-white">
                    <?= __('settings.ds_nav_overview') ?>
                </h3>
                <p class="text-xs text-gray-500 dark:text-slate-400">Pure PHP 8.4+ MVC Standard Core & Extensible Enterprise Architecture</p>
            </div>
        </div>

        <p class="text-sm text-gray-600 dark:text-slate-300 leading-relaxed">
            ระบบ <strong>Standard Core System (PHP UMS)</strong> ได้รับการออกแบบภายใต้แนวคิด <strong>"Core as a Manager & Modular Engine"</strong> เพื่อเป็นโครงสร้างแกนกลางสมบูรณ์แบบ (Enterprise-Grade Boilerplate) สำหรับการจัดการบัญชีผู้ใช้งาน, ระบบสิทธิ์ (RBAC), ความปลอดภัยระดับสูง (Hashids, 2FA, Multi-channel CSRF, Rate Limiting), บันทึกประวัติกิจกรรมและการตรวจสอบ (Activity & Audit Logs), และพจนานุกรมคำแปลภาษาคู่ขนาน 100% โดยไม่มีการครอบทับของเฟรมเวิร์กขนาดใหญ่ พร้อมนำไปต่อยอดเป็นระบบธุรกิจต่างๆ ได้อย่างไร้รอยต่อ
        </p>

        <!-- 2 Main Pillars Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
            <!-- Pillar 1: Pure MVC Architecture -->
            <div class="p-5 rounded-2xl bg-indigo-50/40 dark:bg-slate-800/60 border border-indigo-100/80 dark:border-slate-700 relative overflow-hidden">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-500 text-white flex items-center justify-center text-xl shadow-xs">
                        <i class="ph ph-cube"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-900 dark:text-white text-sm">Pure PHP 8.4+ MVC Architecture</h4>
                        <span class="text-xs text-indigo-600 dark:text-indigo-400 font-medium">สถาปัตยกรรมไร้เฟรมเวิร์ก ประสิทธิภาพสูงสุด</span>
                    </div>
                </div>
                <ul class="text-xs text-gray-600 dark:text-slate-300 space-y-2 list-disc list-inside">
                    <li>ทำงานบน PHP 8.4+ รองรับ Strict Types, Null-Safe Operators และ Named Arguments</li>
                    <li>โหลดคลาสแบบ PSR-4 ผ่าน Composer โดยไม่มี Dependency ฟุ่มเฟือย</li>
                    <li>เราเตอร์แบบ Clean URL รองรับ Dynamic Parameters และ Middleware Pipelines</li>
                    <li>การเชื่อมต่อฐานข้อมูล PDO Prepared Statements 100% ป้องกัน SQL Injection อย่างเด็ดขาด</li>
                    <li>ระบบความปลอดภัยหลายชั้น (Rate Limiting, CSRF, 2FA, Session Revoke)</li>
                </ul>
            </div>

            <!-- Pillar 2: Core as a Manager Foundation -->
            <div class="p-5 rounded-2xl bg-emerald-50/40 dark:bg-slate-800/60 border border-emerald-100/80 dark:border-slate-700 relative overflow-hidden">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-500 text-white flex items-center justify-center text-xl shadow-xs">
                        <i class="ph ph-tree-structure"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-900 dark:text-white text-sm">Extensible Modular Foundation</h4>
                        <span class="text-xs text-emerald-600 dark:text-emerald-400 font-medium">โครงสร้างฐานรองรับการต่อขยายทุกประเภทระบบ</span>
                    </div>
                </div>
                <ul class="text-xs text-gray-600 dark:text-slate-300 space-y-2 list-disc list-inside">
                    <li>ระบบผู้ใช้งานและบทบาท RBAC ละเอียดระดับโมดูลและแอคชัน (<code class="text-emerald-600 dark:text-emerald-400 font-mono">module:action</code>)</li>
                    <li>บันทึกประวัติความเปลี่ยนแปลงระดับฟิลด์ JSON (<code class="text-emerald-600 dark:text-emerald-400 font-mono">audit_logs</code>) เปรียบเทียบ Old/New Values สด</li>
                    <li>พจนานุกรมแปลภาษา 2 ภาษา (TH/EN) จัดเก็บใน Database พร้อม UI ปรับแต่งคำแปลได้ทันที</li>
                    <li>แถบโหลด YouTube-Style Progress Bar พร้อมระบบป้องกัน FOUC / จอกระพริบ</li>
                    <li>รองรับการต่อขยายเป็นระบบ POS, E-Commerce, CRM, HR, คลังสินค้า หรือระบบสั่งอาหารได้ทันที</li>
                </ul>
            </div>
        </div>

        <!-- 4 Golden Principles -->
        <div class="p-4 rounded-2xl bg-amber-50/60 dark:bg-amber-950/20 border border-amber-200/80 dark:border-amber-900/40">
            <h4 class="text-xs font-bold uppercase tracking-wider text-amber-800 dark:text-amber-400 flex items-center gap-1.5 mb-2.5">
                <i class="ph ph-seal-check text-base"></i> 4 กฎเหล็กของสถาปัตยกรรม (4 Golden Architecture Principles)
            </h4>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 text-xs">
                <div class="p-3 bg-white dark:bg-slate-900 rounded-xl border border-amber-100 dark:border-slate-800">
                    <span class="font-bold text-gray-800 dark:text-white block mb-1">1. Zero Raw ID Exposure</span>
                    <span class="text-gray-500 dark:text-slate-400">ห้ามเปิดเผย ID ตัวเลขเด็ดขาด ทุก ID ต้องเข้ารหัสเป็น Hashids 8 หลัก</span>
                </div>
                <div class="p-3 bg-white dark:bg-slate-900 rounded-xl border border-amber-100 dark:border-slate-800">
                    <span class="font-bold text-gray-800 dark:text-white block mb-1">2. Instant Load & Zero FOUC</span>
                    <span class="text-gray-500 dark:text-slate-400">เรนเดอร์ Client-Side DataTables ทันที และมีสคริปต์กันจอกระพริบใน Head เสมอ</span>
                </div>
                <div class="p-3 bg-white dark:bg-slate-900 rounded-xl border border-amber-100 dark:border-slate-800">
                    <span class="font-bold text-gray-800 dark:text-white block mb-1">3. Shared Hosting Ready</span>
                    <span class="text-gray-500 dark:text-slate-400">ทำงานได้บน Shared Hosting (cPanel / DirectAdmin) ผ่าน .htaccess โดยไม่ต้องมี SSH</span>
                </div>
                <div class="p-3 bg-white dark:bg-slate-900 rounded-xl border border-amber-100 dark:border-slate-800">
                    <span class="font-bold text-gray-800 dark:text-white block mb-1">4. 100% Bilingual Parity</span>
                    <span class="text-gray-500 dark:text-slate-400">คีย์ภาษาไทย (TH) และอังกฤษ (EN) ต้องตรงกัน 1:1 ครบทุกจุด 0 missing key</span>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- SECTION 2: Architecture & Tech Stack -->
    <!-- ========================================== -->
    <div id="sec-tech" class="scroll-mt-32 space-y-4 pt-4 border-t border-gray-100 dark:border-slate-800">
        <div class="flex items-center gap-3 border-b border-gray-100 dark:border-slate-800 pb-3">
            <span class="w-8 h-8 rounded-xl bg-indigo-100 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold text-sm">2</span>
            <div>
                <h3 class="text-lg font-bold text-gray-800 dark:text-white">
                    <?= __('settings.ds_nav_tech') ?>
                </h3>
                <p class="text-xs text-gray-500 dark:text-slate-400">Pure MVC Pipeline, Security Stack, Touchpoints & Real-Time Sync Layer</p>
            </div>
        </div>

        <!-- Tech Stack Table -->
        <div class="overflow-x-auto rounded-xl border border-gray-100 dark:border-slate-800">
            <table class="w-full text-left text-xs">
                <thead class="bg-gray-50 dark:bg-slate-900/80 text-gray-500 dark:text-slate-400 font-semibold border-b border-gray-100 dark:border-slate-800">
                    <tr>
                        <th class="px-4 py-3">Layer (เลเยอร์)</th>
                        <th class="px-4 py-3">Tech & Libraries (เทคโนโลยี)</th>
                        <th class="px-4 py-3">Implementation & Scope (บทบาทและหน้าที่)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-slate-800 text-gray-700 dark:text-slate-300">
                    <tr>
                        <td class="px-4 py-3 font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                            <i class="ph ph-file-code text-indigo-500 text-base"></i> Backend Runtime
                        </td>
                        <td class="px-4 py-3 font-mono font-medium text-indigo-600 dark:text-indigo-400">PHP 8.4+ (Strict Types)</td>
                        <td class="px-4 py-3">Pure MVC Architecture, PSR-4 Autoloading, Null-safe Operator, Zero bloated framework overhead</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                            <i class="ph ph-database text-blue-500 text-base"></i> Database Engine
                        </td>
                        <td class="px-4 py-3 font-mono font-medium text-blue-600 dark:text-blue-400">MySQL / MariaDB (InnoDB)</td>
                        <td class="px-4 py-3">UTF8MB4 Unicode, PDO Prepared Statements 100%, Native Migration Runner & Phinx compatible</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                            <i class="ph ph-shield-check text-emerald-500 text-base"></i> Security & Cryptography
                        </td>
                        <td class="px-4 py-3 font-mono font-medium text-emerald-600 dark:text-emerald-400">Hashids, Google2FA, JWT</td>
                        <td class="px-4 py-3">Hashids ID masking (8 chars), TOTP 2FA, Stateless API JWT, Multi-channel CSRF verification, Sliding RateLimiter</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                            <i class="ph ph-paint-brush text-rose-500 text-base"></i> Frontend & Theme
                        </td>
                        <td class="px-4 py-3 font-mono font-medium text-rose-600 dark:text-rose-400">Tailwind CSS + Prompt Font</td>
                        <td class="px-4 py-3">Dynamic Palette (5 themes: Blue, Emerald, Purple, Indigo, Rose), Ultra Dark Mode (<code class="font-mono">html.dark</code>), Rem Scaling (14/16/18px)</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                            <i class="ph ph-table text-cyan-500 text-base"></i> Data & UI Components
                        </td>
                        <td class="px-4 py-3 font-mono font-medium text-cyan-600 dark:text-cyan-400">DataTables, SweetAlert2, Axios</td>
                        <td class="px-4 py-3">Client-Side DataTables (Instant Load & Zero FOUC), Custom Toast Alerts, AJAX Deletions, Global Form Spinners</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                            <i class="ph ph-hourglass text-amber-500 text-base"></i> Top Progress Bar
                        </td>
                        <td class="px-4 py-3 font-mono font-medium text-amber-600 dark:text-amber-400">YouTube-style Preloader</td>
                        <td class="px-4 py-3">Chromium Paint-Holding compliant, zero white screen flicker during page transitions, automatically tracks Axios requests</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- High-Level Flow Representation -->
        <div class="p-5 rounded-2xl bg-gray-50 dark:bg-slate-900/60 border border-gray-100 dark:border-slate-800">
            <h4 class="text-xs font-bold uppercase tracking-wider text-gray-600 dark:text-slate-400 mb-3 flex items-center gap-2">
                <i class="ph ph-git-merge text-indigo-500 text-base"></i> High-Level Architecture Flowchart
            </h4>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-3 text-center text-xs">
                <div class="p-3 bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-xs">
                    <span class="inline-block p-2 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 rounded-lg mb-2">
                        <i class="ph ph-devices text-xl"></i>
                    </span>
                    <h5 class="font-bold text-gray-900 dark:text-white">1. Client Touchpoints</h5>
                    <p class="text-gray-500 dark:text-slate-400 mt-1">Web Desktop, Mobile Browser, Tablet, PWA & External API Consumers</p>
                </div>
                <div class="p-3 bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-xs">
                    <span class="inline-block p-2 bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 rounded-lg mb-2">
                        <i class="ph ph-shield-check text-xl"></i>
                    </span>
                    <h5 class="font-bold text-gray-900 dark:text-white">2. Security & Routing Layer</h5>
                    <p class="text-gray-500 dark:text-slate-400 mt-1">.htaccess &rarr; Router &rarr; Auth, RBAC, RateLimit & CSRF Middlewares</p>
                </div>
                <div class="p-3 bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-xs">
                    <span class="inline-block p-2 bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-lg mb-2">
                        <i class="ph ph-cpu text-xl"></i>
                    </span>
                    <h5 class="font-bold text-gray-900 dark:text-white">3. Core Controllers & Services</h5>
                    <p class="text-gray-500 dark:text-slate-400 mt-1">Auth, Users, Roles, Logs, Settings, Profile, Hashids & Lang Services</p>
                </div>
                <div class="p-3 bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-xs">
                    <span class="inline-block p-2 bg-purple-50 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 rounded-lg mb-2">
                        <i class="ph ph-database text-xl"></i>
                    </span>
                    <h5 class="font-bold text-gray-900 dark:text-white">4. Persistence & Storage</h5>
                    <p class="text-gray-500 dark:text-slate-400 mt-1">MySQL InnoDB (12 Tables) + Storage (Error Logs, Uploads)</p>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- SECTION 3: Directory Structure -->
    <!-- ========================================== -->
    <div id="sec-dir" class="scroll-mt-32 space-y-4 pt-4 border-t border-gray-100 dark:border-slate-800">
        <div class="flex items-center gap-3 border-b border-gray-100 dark:border-slate-800 pb-3">
            <span class="w-8 h-8 rounded-xl bg-indigo-100 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold text-sm">3</span>
            <div>
                <h3 class="text-lg font-bold text-gray-800 dark:text-white">
                    <?= __('settings.ds_nav_dir') ?>
                </h3>
                <p class="text-xs text-gray-500 dark:text-slate-400">Project Tree Structure & Coding Isolation Guidelines</p>
            </div>
        </div>

        <div class="p-4 rounded-2xl bg-gray-900 text-gray-200 font-mono text-xs overflow-x-auto leading-relaxed border border-gray-800 shadow-inner">
<pre class="text-gray-300">
php_ums/
├── app/
│   ├── Controllers/                       <span class="text-indigo-400"># Core Controllers (AuthController, UserController, RoleController, LogController, SettingController, ProfileController)</span>
│   │   └── Api/                           <span class="text-indigo-400"># Stateless JWT API Endpoints (/api/v1/login, /api/v1/me)</span>
│   ├── Core/                              <span class="text-emerald-400"># Config, Controller, Database, ErrorHandler, Lang, Logger, Model, RateLimiter, RBAC, Router, Security, Session, Settings, View</span>
│   ├── Lang/                              <span class="text-amber-400"># 100% Bilingual Dictionary Files</span>
│   │   ├── th/                            <span class="text-amber-400"># ภาษาไทย (auth, common, dashboard, logs, profile, roles, settings, users, ฯลฯ)</span>
│   │   └── en/                            <span class="text-amber-400"># English (Symmetric 1:1 match with Thai, 0 missing key)</span>
│   ├── Middlewares/                       <span class="text-purple-400"># AuthMiddleware, GuestMiddleware, PermissionMiddleware, RateLimitMiddleware, JwtMiddleware</span>
│   ├── Models/                            <span class="text-blue-400"># Core Domain Models (User, Role, ActivityLog, AuditLog, Notification, ฯลฯ)</span>
│   ├── Views/                             <span class="text-indigo-400"># MVC View Templates</span>
│   │   ├── auth/                          <span class="text-indigo-400"># Login, Register, 2FA, Forgot & Reset Password views</span>
│   │   ├── dashboard/                     <span class="text-indigo-400"># Main Admin Dashboard with Realtime Analytics</span>
│   │   ├── errors/                        <span class="text-indigo-400"># 403, 404, 500, and Maintenance Mode screens</span>
│   │   ├── layouts/                       <span class="text-indigo-400"># Master layouts (app.php, auth.php) & Partials (progress_bar.php)</span>
│   │   ├── logs/                          <span class="text-indigo-400"># Activity Logs & JSON Diff Audit Logs views</span>
│   │   ├── profile/                       <span class="text-indigo-400"># User Profile, Change Password, 2FA QR Setup, Active Sessions management</span>
│   │   ├── roles/                         <span class="text-indigo-400"># RBAC Roles & Module Permissions Matrix views</span>
│   │   ├── settings/                      <span class="text-indigo-400"># System Settings (General, Login, Translations, Security, Social, Design Spec)</span>
│   │   └── users/                         <span class="text-indigo-400"># User Management (CRUD tables, Hashids, Filters, SweetAlert2 Deletions)</span>
│   └── helpers.php                        <span class="text-blue-400"># Global Helpers (__, formatDate, getSystemThemeConfig, ฯลฯ)</span>
├── database/migrations/                   <span class="text-blue-400"># 16+ Database Migration Files (12 Core Tables Schema)</span>
├── public/                                <span class="text-amber-400"># Web Root (.htaccess, index.php, assets, uploads)</span>
├── storage/                               <span class="text-red-400"># Logs & Cache (storage/logs/error.log)</span>
├── compare_lang.php                       <span class="text-yellow-400"># Automated CLI tool verifying 100% TH &lt;-&gt; EN key parity</span>
└── sync_lang.php                          <span class="text-yellow-400"># Sync file translations to MySQL database</span>
</pre>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- SECTION 4: Database Schema & ER Diagram -->
    <!-- ========================================== -->
    <div id="sec-db" class="scroll-mt-32 space-y-4 pt-4 border-t border-gray-100 dark:border-slate-800">
        <div class="flex items-center gap-3 border-b border-gray-100 dark:border-slate-800 pb-3">
            <span class="w-8 h-8 rounded-xl bg-indigo-100 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold text-sm">4</span>
            <div>
                <h3 class="text-lg font-bold text-gray-800 dark:text-white">
                    <?= __('settings.ds_nav_db') ?>
                </h3>
                <p class="text-xs text-gray-500 dark:text-slate-400">Relational Database Model: 12 Core UMS Tables & Integrity Specifications</p>
            </div>
        </div>

        <!-- Core Tables Grid -->
        <div>
            <h4 class="text-xs font-bold uppercase tracking-wider text-indigo-600 dark:text-indigo-400 mb-3 flex items-center gap-2">
                <i class="ph ph-cube text-base"></i> <?= __('settings.ds_core_modules') ?>: 12 ตารางฐานข้อมูล
            </h4>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 text-xs">
                <div class="p-3.5 bg-white dark:bg-slate-900 rounded-xl border border-gray-100 dark:border-slate-800">
                    <div class="font-mono font-bold text-indigo-600 dark:text-indigo-400 mb-1">roles</div>
                    <p class="text-gray-500 dark:text-slate-400">กลุ่มบทบาทผู้ใช้ระบบ (Superadmin, Admin, Manager, Staff, User)</p>
                </div>
                <div class="p-3.5 bg-white dark:bg-slate-900 rounded-xl border border-gray-100 dark:border-slate-800">
                    <div class="font-mono font-bold text-indigo-600 dark:text-indigo-400 mb-1">permissions</div>
                    <p class="text-gray-500 dark:text-slate-400">สิทธิ์การทำงานละเอียดระดับโมดูลและแอคชัน (<code class="font-mono">module:action</code>)</p>
                </div>
                <div class="p-3.5 bg-white dark:bg-slate-900 rounded-xl border border-gray-100 dark:border-slate-800">
                    <div class="font-mono font-bold text-indigo-600 dark:text-indigo-400 mb-1">role_permissions</div>
                    <p class="text-gray-500 dark:text-slate-400">ตารางความสัมพันธ์ Many-to-Many เชื่อมบทบาทเข้ากับสิทธิ์การใช้งาน</p>
                </div>
                <div class="p-3.5 bg-white dark:bg-slate-900 rounded-xl border border-gray-100 dark:border-slate-800">
                    <div class="font-mono font-bold text-indigo-600 dark:text-indigo-400 mb-1">users</div>
                    <p class="text-gray-500 dark:text-slate-400">บัญชีผู้ใช้ระบบ รหัสผ่านแฮช 2FA TOTP, OAuth โซเชียล และสถานะการเปิดใช้งาน</p>
                </div>
                <div class="p-3.5 bg-white dark:bg-slate-900 rounded-xl border border-gray-100 dark:border-slate-800">
                    <div class="font-mono font-bold text-indigo-600 dark:text-indigo-400 mb-1">user_sessions</div>
                    <p class="text-gray-500 dark:text-slate-400">ติดตามเซสชันที่กำลังออนไลน์ อุปกรณ์ IP, User-Agent และสั่งยกเลิกเซสชันทางไกล</p>
                </div>
                <div class="p-3.5 bg-white dark:bg-slate-900 rounded-xl border border-gray-100 dark:border-slate-800">
                    <div class="font-mono font-bold text-indigo-600 dark:text-indigo-400 mb-1">password_resets</div>
                    <p class="text-gray-500 dark:text-slate-400">โทเค็นสำหรับรีเซ็ตรหัสผ่านทางอีเมลพร้อมเวลาหมดอายุ (Token Expiration)</p>
                </div>
                <div class="p-3.5 bg-white dark:bg-slate-900 rounded-xl border border-gray-100 dark:border-slate-800">
                    <div class="font-mono font-bold text-indigo-600 dark:text-indigo-400 mb-1">activity_logs</div>
                    <p class="text-gray-500 dark:text-slate-400">บันทึกพฤติกรรมและการเข้าใช้งานระบบทั่วไปของผู้ใช้ทุกคน</p>
                </div>
                <div class="p-3.5 bg-white dark:bg-slate-900 rounded-xl border border-gray-100 dark:border-slate-800">
                    <div class="font-mono font-bold text-indigo-600 dark:text-indigo-400 mb-1">audit_logs</div>
                    <p class="text-gray-500 dark:text-slate-400">บันทึกการเปลี่ยนแปลงระดับฟิลด์ JSON เปรียบเทียบ Old/New Values สด</p>
                </div>
                <div class="p-3.5 bg-white dark:bg-slate-900 rounded-xl border border-gray-100 dark:border-slate-800">
                    <div class="font-mono font-bold text-indigo-600 dark:text-indigo-400 mb-1">system_settings</div>
                    <p class="text-gray-500 dark:text-slate-400">ค่าคอนฟิกระบบแบบ Key-Value ทั้งข้อมูลเว็บ, ความปลอดภัย และโซเชียล OAuth</p>
                </div>
                <div class="p-3.5 bg-white dark:bg-slate-900 rounded-xl border border-gray-100 dark:border-slate-800">
                    <div class="font-mono font-bold text-indigo-600 dark:text-indigo-400 mb-1">translations</div>
                    <p class="text-gray-500 dark:text-slate-400">พจนานุกรมคำแปลภาษาไทยและอังกฤษในฐานข้อมูล รองรับการแก้ไขแบบ Real-time</p>
                </div>
                <div class="p-3.5 bg-white dark:bg-slate-900 rounded-xl border border-gray-100 dark:border-slate-800">
                    <div class="font-mono font-bold text-indigo-600 dark:text-indigo-400 mb-1">notifications</div>
                    <p class="text-gray-500 dark:text-slate-400">ระบบการแจ้งเตือนกระดิ่งด้านบน พร้อมสถานะอ่านแล้ว/ยังไม่ได้อ่าน</p>
                </div>
                <div class="p-3.5 bg-white dark:bg-slate-900 rounded-xl border border-gray-100 dark:border-slate-800">
                    <div class="font-mono font-bold text-indigo-600 dark:text-indigo-400 mb-1">rate_limits</div>
                    <p class="text-gray-500 dark:text-slate-400">บันทึกความถี่การส่งคำขอตาม IP & Endpoint ป้องกัน Brute-force และ DoS</p>
                </div>
            </div>
        </div>

        <!-- Relational Flow Diagram -->
        <div class="p-5 rounded-2xl bg-gray-50 dark:bg-slate-900/60 border border-gray-100 dark:border-slate-800 space-y-3">
            <h4 class="text-xs font-bold uppercase tracking-wider text-gray-600 dark:text-slate-400 flex items-center gap-2">
                <i class="ph ph-git-fork text-indigo-500 text-base"></i> Core Entity Relationships (ความสัมพันธ์ระหว่างตาราง)
            </h4>
            <div class="p-4 bg-white dark:bg-slate-900 rounded-xl border border-gray-100 dark:border-slate-800 font-mono text-xs text-gray-700 dark:text-slate-300 overflow-x-auto">
                <div class="space-y-1.5 whitespace-nowrap">
                    <div><span class="text-indigo-600 dark:text-indigo-400 font-bold">roles</span> (1) &longleftrightarrow; (N) <span class="text-indigo-600 dark:text-indigo-400 font-bold">role_permissions</span> (N) &longleftrightarrow; (1) <span class="text-indigo-600 dark:text-indigo-400 font-bold">permissions</span></div>
                    <div><span class="text-indigo-600 dark:text-indigo-400 font-bold">roles</span> (1) &longleftrightarrow; (N) <span class="text-blue-600 dark:text-blue-400 font-bold">users</span> (role_id)</div>
                    <div><span class="text-blue-600 dark:text-blue-400 font-bold">users</span> (1) &longleftrightarrow; (N) <span class="text-cyan-600 dark:text-cyan-400 font-bold">user_sessions</span> (user_id)</div>
                    <div><span class="text-blue-600 dark:text-blue-400 font-bold">users</span> (1) &longleftrightarrow; (N) <span class="text-purple-600 dark:text-purple-400 font-bold">activity_logs</span> (user_id)</div>
                    <div><span class="text-blue-600 dark:text-blue-400 font-bold">users</span> (1) &longleftrightarrow; (N) <span class="text-emerald-600 dark:text-emerald-400 font-bold">audit_logs</span> (user_id)</div>
                    <div><span class="text-blue-600 dark:text-blue-400 font-bold">users</span> (1) &longleftrightarrow; (N) <span class="text-rose-600 dark:text-rose-400 font-bold">notifications</span> (user_id)</div>
                    <div><span class="text-blue-600 dark:text-blue-400 font-bold">users</span> (1) &longleftrightarrow; (N) <span class="text-amber-600 dark:text-amber-400 font-bold">password_resets</span> (email)</div>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- SECTION 5: Security & Core Architecture -->
    <!-- ========================================== -->
    <div id="sec-security" class="scroll-mt-32 space-y-4 pt-4 border-t border-gray-100 dark:border-slate-800">
        <div class="flex items-center gap-3 border-b border-gray-100 dark:border-slate-800 pb-3">
            <span class="w-8 h-8 rounded-xl bg-indigo-100 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold text-sm">5</span>
            <div>
                <h3 class="text-lg font-bold text-gray-800 dark:text-white">
                    <?= __('settings.ds_nav_security') ?>
                </h3>
                <p class="text-xs text-gray-500 dark:text-slate-400">Hashids Masking, Two-Factor Authentication, Rate Limiting & Multi-channel CSRF</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- 5.1 Hashids Obfuscation -->
            <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 space-y-2">
                <div class="flex items-center gap-2 text-indigo-600 dark:text-indigo-400 font-bold text-sm">
                    <i class="ph ph-hash text-lg"></i>
                    <span>5.1 Mandatory Hashids Masking</span>
                </div>
                <p class="text-xs text-gray-600 dark:text-slate-400 leading-relaxed">
                    ห้ามเปิดเผย Primary Key ตัวเลข (<code class="font-mono">id</code>) สู่ภายนอก ต้องใช้ <code class="text-indigo-500 font-mono">Security::encodeId()</code> เสมอ และใน Controller ต้องถอดรหัสด้วย <code class="text-indigo-500 font-mono">Security::decodeId()</code>
                </p>
                <div class="p-2.5 bg-gray-50 dark:bg-slate-800/80 rounded-xl font-mono text-[11px] text-gray-700 dark:text-slate-300">
                    <span class="text-emerald-500">GET</span> /users/<span class="text-indigo-500 font-bold">B3r9Q1zp</span>/edit<br>
                    <span class="text-amber-500 text-[10px]">Zero sequential ID enumeration</span>
                </div>
            </div>

            <!-- 5.2 Auth & 2FA -->
            <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 space-y-2">
                <div class="flex items-center gap-2 text-emerald-600 dark:text-emerald-400 font-bold text-sm">
                    <i class="ph ph-fingerprint text-lg"></i>
                    <span>5.2 TOTP 2FA & Superadmin Protection</span>
                </div>
                <p class="text-xs text-gray-600 dark:text-slate-400 leading-relaxed">
                    รองรับ Google Authenticator และมีนโยบาย <code class="font-mono text-emerald-500">force_2fa_admin</code> พร้อมการป้องกันบัญชี <code class="font-mono">superadmin</code> ไม่ให้ผู้ใช้ระดับต่ำกว่าแก้ไขหรือลบ
                </p>
                <div class="p-2.5 bg-gray-50 dark:bg-slate-800/80 rounded-xl font-mono text-[11px] text-gray-700 dark:text-slate-300">
                    <span class="text-emerald-500">CSRF</span> Multi-channel check:<br>
                    <span class="text-gray-400">$_POST, X-CSRF-TOKEN, JSON</span>
                </div>
            </div>

            <!-- 5.3 Sliding Rate Limiting -->
            <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 space-y-2">
                <div class="flex items-center gap-2 text-amber-600 dark:text-amber-400 font-bold text-sm">
                    <i class="ph ph-shield-warning text-lg"></i>
                    <span>5.3 Sliding Window Rate Limiting</span>
                </div>
                <p class="text-xs text-gray-600 dark:text-slate-400 leading-relaxed">
                    คำนวณเวลาย้อนหลังเทียบกับ <code class="font-mono">updated_at</code> แบบ Sliding Window ป้องกันการสะสมความผิดพลาดข้ามวัน และ Reset ทันทีเมื่อล็อกอินสำเร็จ
                </p>
                <div class="p-2.5 bg-gray-50 dark:bg-slate-800/80 rounded-xl font-mono text-[11px] text-gray-700 dark:text-slate-300 space-y-1">
                    <div>Endpoint: <span class="text-amber-500">POST /login</span></div>
                    <div>RateLimiter::reset('/login') on success</div>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- SECTION 6: User & RBAC Permission Matrix -->
    <!-- ========================================== -->
    <div id="sec-rbac" class="scroll-mt-32 space-y-4 pt-4 border-t border-gray-100 dark:border-slate-800">
        <div class="flex items-center gap-3 border-b border-gray-100 dark:border-slate-800 pb-3">
            <span class="w-8 h-8 rounded-xl bg-indigo-100 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold text-sm">6</span>
            <div>
                <h3 class="text-lg font-bold text-gray-800 dark:text-white">
                    <?= __('settings.ds_nav_rbac') ?>
                </h3>
                <p class="text-xs text-gray-500 dark:text-slate-400">Role-Based Access Control, Permission Middlewares & Protected Accounts</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Role Hierarchy -->
            <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 space-y-3">
                <h4 class="font-bold text-gray-800 dark:text-white text-sm flex items-center gap-2">
                    <i class="ph ph-users-three text-indigo-500 text-lg"></i> ลำดับขั้นของบทบาท (Role Hierarchy)
                </h4>
                <div class="space-y-2 text-xs">
                    <div class="p-2.5 bg-indigo-50/50 dark:bg-indigo-950/20 rounded-xl border border-indigo-100 dark:border-indigo-900/40 flex items-center justify-between">
                        <div>
                            <span class="font-bold text-indigo-700 dark:text-indigo-400">Superadmin</span>
                            <p class="text-gray-500 dark:text-slate-400 text-[11px]">สิทธิ์สูงสุดของระบบ ข้ามการตรวจสอบสิทธิ์ทั้งหมด เข้าถึงทุกโมดูล</p>
                        </div>
                        <span class="px-2 py-0.5 rounded text-[10px] font-mono bg-indigo-100 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-300">Protected</span>
                    </div>
                    <div class="p-2.5 bg-blue-50/50 dark:bg-blue-950/20 rounded-xl border border-blue-100 dark:border-blue-900/40 flex items-center justify-between">
                        <div>
                            <span class="font-bold text-blue-700 dark:text-blue-400">Admin</span>
                            <p class="text-gray-500 dark:text-slate-400 text-[11px]">ผู้ดูแลระบบ จัดการผู้ใช้ สิทธิ์ และการตั้งค่าระบบตามที่ได้รับมอบหมาย</p>
                        </div>
                        <span class="px-2 py-0.5 rounded text-[10px] font-mono bg-blue-100 dark:bg-blue-900/50 text-blue-700 dark:text-blue-300">2FA Enforced</span>
                    </div>
                    <div class="p-2.5 bg-gray-50 dark:bg-slate-800/60 rounded-xl border border-gray-100 dark:border-slate-800 flex items-center justify-between">
                        <div>
                            <span class="font-bold text-gray-700 dark:text-slate-300">Manager / Staff / User</span>
                            <p class="text-gray-500 dark:text-slate-400 text-[11px]">กลุ่มบทบาทปฏิบัติการ ถูกจำกัดการเข้าถึงตาม Permissions ในตาราง <code class="font-mono">role_permissions</code></p>
                        </div>
                        <span class="px-2 py-0.5 rounded text-[10px] font-mono bg-gray-200 dark:bg-slate-700 text-gray-700 dark:text-slate-300">RBAC Guard</span>
                    </div>
                </div>
            </div>

            <!-- Permission Syntax & Guard -->
            <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 space-y-3">
                <h4 class="font-bold text-gray-800 dark:text-white text-sm flex items-center gap-2">
                    <i class="ph ph-key text-emerald-500 text-lg"></i> รูปแบบสิทธิ์ & Middleware Guard
                </h4>
                <p class="text-xs text-gray-600 dark:text-slate-300 leading-relaxed">
                    สิทธิ์ถูกจัดเก็บในรูปแบบ <code class="font-mono font-bold text-emerald-600 dark:text-emerald-400">module:action</code> และบังคับใช้ผ่าน <code class="font-mono text-indigo-500">PermissionMiddleware</code> ในการลงทะเบียนเส้นทาง:
                </p>
                <div class="p-3 bg-gray-900 text-gray-200 rounded-xl font-mono text-[11px] space-y-1 overflow-x-auto">
                    <div class="text-emerald-400">// ตัวอย่างการป้องกันเส้นทาง:</div>
                    <div>$router->get('/users', [UserController::class, 'index'], [</div>
                    <div class="pl-4">AuthMiddleware::class,</div>
                    <div class="pl-4">[PermissionMiddleware::class, <span class="text-amber-400">'users'</span>, <span class="text-amber-400">'view'</span>]</div>
                    <div>]);</div>
                </div>
                <div class="text-[11px] text-gray-500 dark:text-slate-400 flex items-center gap-1.5 pt-1">
                    <i class="ph ph-shield-check text-emerald-500 text-sm"></i>
                    <span>หากผู้ใช้ไม่มีสิทธิ์ ระบบจะแสดงหน้า <strong>403 Forbidden</strong> ทันที</span>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- SECTION 7: UI/UX Standards & Design System -->
    <!-- ========================================== -->
    <div id="sec-uiux" class="scroll-mt-32 space-y-4 pt-4 border-t border-gray-100 dark:border-slate-800">
        <div class="flex items-center gap-3 border-b border-gray-100 dark:border-slate-800 pb-3">
            <span class="w-8 h-8 rounded-xl bg-indigo-100 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold text-sm">7</span>
            <div>
                <h3 class="text-lg font-bold text-gray-800 dark:text-white">
                    <?= __('settings.ds_nav_uiux') ?>
                </h3>
                <p class="text-xs text-gray-500 dark:text-slate-400">Google Font Prompt, 5 Dynamic Theme Palettes, YouTube Progress Bar & Layout Standards</p>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 text-xs">
            <!-- Theme Card 1 -->
            <div class="p-3.5 bg-white dark:bg-slate-900 rounded-xl border border-gray-100 dark:border-slate-800 space-y-1.5">
                <span class="font-bold text-gray-800 dark:text-white flex items-center gap-1.5">
                    <span class="w-3 h-3 rounded-full bg-blue-500 inline-block"></span> 1. Default (Slate / Blue)
                </span>
                <p class="text-gray-500 dark:text-slate-400 text-[11px]">แถบด้านข้าง Slate 900 เน้นความน่าเชื่อถือ มินิมอล เรียบหรู เหมาะกับงานหลังบ้าน</p>
            </div>
            <!-- Theme Card 2 -->
            <div class="p-3.5 bg-white dark:bg-slate-900 rounded-xl border border-gray-100 dark:border-slate-800 space-y-1.5">
                <span class="font-bold text-gray-800 dark:text-white flex items-center gap-1.5">
                    <span class="w-3 h-3 rounded-full bg-emerald-500 inline-block"></span> 2. Modern Emerald
                </span>
                <p class="text-gray-500 dark:text-slate-400 text-[11px]">โทนเขียวมรกต สดใส สบายตา สะท้อนความสดใหม่และสถานะออนไลน์</p>
            </div>
            <!-- Theme Card 3 -->
            <div class="p-3.5 bg-white dark:bg-slate-900 rounded-xl border border-gray-100 dark:border-slate-800 space-y-1.5">
                <span class="font-bold text-gray-800 dark:text-white flex items-center gap-1.5">
                    <span class="w-3 h-3 rounded-full bg-purple-500 inline-block"></span> 3. Royal Purple
                </span>
                <p class="text-gray-500 dark:text-slate-400 text-[11px]">ม่วงหรูหรา สง่างาม เพิ่มความโดดเด่นและมิติระดับพรีเมียมให้กับหน้าจอ</p>
            </div>
            <!-- Theme Card 4 -->
            <div class="p-3.5 bg-white dark:bg-slate-900 rounded-xl border border-gray-100 dark:border-slate-800 space-y-1.5">
                <span class="font-bold text-gray-800 dark:text-white flex items-center gap-1.5">
                    <span class="w-3 h-3 rounded-full bg-rose-500 inline-block"></span> 4. Crimson Rose
                </span>
                <p class="text-gray-500 dark:text-slate-400 text-[11px]">โทนกุหลาบแดงชมพู มีชีวิตชีวา สะดุดตา และกระตุ้นความกระตือรือร้น</p>
            </div>
        </div>

        <!-- Strict UI Conventions -->
        <div class="p-4 rounded-2xl bg-indigo-50/50 dark:bg-indigo-950/20 border border-indigo-100 dark:border-indigo-900/40 text-xs text-gray-700 dark:text-slate-300 space-y-2">
            <h4 class="font-bold uppercase tracking-wider text-indigo-700 dark:text-indigo-400 flex items-center gap-1.5">
                <i class="ph ph-check-square-offset text-base"></i> ข้อกำหนดเฉพาะด้าน UI/UX (Strict UI Rules)
            </h4>
            <ul class="list-disc list-inside space-y-1 text-gray-600 dark:text-slate-300">
                <li><strong>ห้ามใช้สัญลักษณ์สกุลเงินบาท:</strong> ห้ามใช้สัญลักษณ์ <code>สัญลักษณ์เงินบาท (Baht Sign)</code> ทุกที่ ให้ใช้คำว่า "บาท" หรือ "THB" เสมอ</li>
                <li><strong>ความโค้งของปุ่มและการ์ด:</strong> ใช้ความโค้งเหลี่ยมมนแบบมาตรฐาน (<code class="font-mono">rounded-md</code> สำหรับปุ่มและอินพุต, <code class="font-mono">rounded-2xl</code> สำหรับการ์ด)</li>
                <li><strong>ปุ่มดำเนินการในตาราง (Table Actions):</strong> ใช้ปุ่มขนาดกะทัดรัด <code class="font-mono">p-1 rounded</code> พร้อมไอคอนขนาด <code class="font-mono">text-lg</code> (18px) ชิดขวาเสมอ</li>
                <li><strong>ลบข้อมูลด้วย AJAX + SweetAlert2:</strong> ห้ามใช้ HTML form submit ในการลบข้อมูล ให้ใช้ <code class="font-mono">axios.post</code> ร่วมกับ <code class="font-mono">Swal.fire</code> เสมอ</li>
                <li><strong>แถบนำทางด้านข้าง Sticky ตอนเลื่อนหน้า:</strong> ในหน้าจอตั้งค่าที่มีแท็บเมนู ต้องคงสถานะลอยติดด้านบน (<code class="font-mono">lg:sticky lg:top-6 self-start z-10</code>) ไม่เลื่อนหาย</li>
                <li><strong>YouTube-Style Progress Bar:</strong> แถบโหลดสีน้ำเงิน-ม่วงบนสุดของหน้าจอ ไร้แสงฟุ้งสีขาว ไม่กระพริบ และรองรับ Chromium Paint-Holding</li>
            </ul>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- SECTION 8: Routes & APIs Map -->
    <!-- ========================================== -->
    <div id="sec-routes" class="scroll-mt-32 space-y-4 pt-4 border-t border-gray-100 dark:border-slate-800">
        <div class="flex items-center gap-3 border-b border-gray-100 dark:border-slate-800 pb-3">
            <span class="w-8 h-8 rounded-xl bg-indigo-100 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold text-sm">8</span>
            <div>
                <h3 class="text-lg font-bold text-gray-800 dark:text-white">
                    <?= __('settings.ds_nav_routes') ?>
                </h3>
                <p class="text-xs text-gray-500 dark:text-slate-400">Complete URL Routing Map: Core UMS MVC & Stateless API Endpoints</p>
            </div>
        </div>

        <div class="overflow-x-auto rounded-xl border border-gray-100 dark:border-slate-800">
            <table class="w-full text-left text-xs">
                <thead class="bg-gray-50 dark:bg-slate-900/80 text-gray-500 dark:text-slate-400 font-semibold border-b border-gray-100 dark:border-slate-800">
                    <tr>
                        <th class="px-4 py-3">Module Group</th>
                        <th class="px-4 py-3">HTTP Method & URI Pattern</th>
                        <th class="px-4 py-3">Controller & Target</th>
                        <th class="px-4 py-3">Security & Guard</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-slate-800 text-gray-700 dark:text-slate-300 font-mono text-[11px]">
                    <tr>
                        <td class="px-4 py-2.5 font-sans font-semibold text-gray-900 dark:text-white">Authentication</td>
                        <td class="px-4 py-2.5 text-indigo-600 dark:text-indigo-400">GET|POST /login, /login/2fa, /logout</td>
                        <td class="px-4 py-2.5">AuthController</td>
                        <td class="px-4 py-2.5 text-emerald-600 dark:text-emerald-400 font-sans">RateLimiter + CSRF</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-2.5 font-sans font-semibold text-gray-900 dark:text-white">Registration & Verify</td>
                        <td class="px-4 py-2.5 text-indigo-600 dark:text-indigo-400">GET|POST /register, /verify-email</td>
                        <td class="px-4 py-2.5">AuthController</td>
                        <td class="px-4 py-2.5 text-emerald-600 dark:text-emerald-400 font-sans">RateLimiter + CSRF</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-2.5 font-sans font-semibold text-gray-900 dark:text-white">Social OAuth 2.0</td>
                        <td class="px-4 py-2.5 text-indigo-600 dark:text-indigo-400">GET /auth/google, /auth/line (+ callbacks)</td>
                        <td class="px-4 py-2.5">SocialAuthController</td>
                        <td class="px-4 py-2.5 text-emerald-600 dark:text-emerald-400 font-sans">State & CSRF Verification</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-2.5 font-sans font-semibold text-gray-900 dark:text-white">Password Recovery</td>
                        <td class="px-4 py-2.5 text-indigo-600 dark:text-indigo-400">/forgot-password, /reset-password</td>
                        <td class="px-4 py-2.5">AuthController</td>
                        <td class="px-4 py-2.5 text-emerald-600 dark:text-emerald-400 font-sans">Token Expiration + RateLimit</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-2.5 font-sans font-semibold text-gray-900 dark:text-white">Dashboard</td>
                        <td class="px-4 py-2.5 text-indigo-600 dark:text-indigo-400">GET /dashboard</td>
                        <td class="px-4 py-2.5">DashboardController</td>
                        <td class="px-4 py-2.5 text-emerald-600 dark:text-emerald-400 font-sans">AuthMiddleware</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-2.5 font-sans font-semibold text-gray-900 dark:text-white">Users Management</td>
                        <td class="px-4 py-2.5 text-indigo-600 dark:text-indigo-400">/users, /users/data, /users/create, /{id}/edit</td>
                        <td class="px-4 py-2.5">UserController</td>
                        <td class="px-4 py-2.5 text-emerald-600 dark:text-emerald-400 font-sans">Auth + RBAC (users:view|edit)</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-2.5 font-sans font-semibold text-gray-900 dark:text-white">Roles & Permissions</td>
                        <td class="px-4 py-2.5 text-indigo-600 dark:text-indigo-400">/roles, /roles/create, /{id}/edit, /{id}/delete</td>
                        <td class="px-4 py-2.5">RoleController</td>
                        <td class="px-4 py-2.5 text-emerald-600 dark:text-emerald-400 font-sans">Auth + RBAC (roles:view|edit)</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-2.5 font-sans font-semibold text-gray-900 dark:text-white">Activity & Audit Logs</td>
                        <td class="px-4 py-2.5 text-indigo-600 dark:text-indigo-400">/logs/activity, /logs/audit (+ /data)</td>
                        <td class="px-4 py-2.5">LogController</td>
                        <td class="px-4 py-2.5 text-emerald-600 dark:text-emerald-400 font-sans">AuthMiddleware</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-2.5 font-sans font-semibold text-gray-900 dark:text-white">System Settings</td>
                        <td class="px-4 py-2.5 text-indigo-600 dark:text-indigo-400">GET|POST /settings/{tab}, /translations/update</td>
                        <td class="px-4 py-2.5">SettingController</td>
                        <td class="px-4 py-2.5 text-emerald-600 dark:text-emerald-400 font-sans">Auth + CSRF Check</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-2.5 font-sans font-semibold text-gray-900 dark:text-white">Profile & 2FA Setup</td>
                        <td class="px-4 py-2.5 text-indigo-600 dark:text-indigo-400">/profile, /profile/password, /2fa/*, /sessions</td>
                        <td class="px-4 py-2.5">ProfileController</td>
                        <td class="px-4 py-2.5 text-emerald-600 dark:text-emerald-400 font-sans">AuthMiddleware + CSRF</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-2.5 font-sans font-semibold text-gray-900 dark:text-white">Internal APIs</td>
                        <td class="px-4 py-2.5 text-cyan-600 dark:text-cyan-400">/api/notifications, /api/search (Ctrl+K)</td>
                        <td class="px-4 py-2.5">NotificationController, SearchController</td>
                        <td class="px-4 py-2.5 text-cyan-600 dark:text-cyan-400 font-sans">Session Auth</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-2.5 font-sans font-semibold text-gray-900 dark:text-white">Stateless JWT APIs</td>
                        <td class="px-4 py-2.5 text-purple-600 dark:text-purple-400">POST /api/v1/login, GET /api/v1/me</td>
                        <td class="px-4 py-2.5">App\Controllers\Api\AuthController</td>
                        <td class="px-4 py-2.5 text-purple-600 dark:text-purple-400 font-sans">Bearer JWT Token</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- SECTION 9: Notifications & Preloading System -->
    <!-- ========================================== -->
    <div id="sec-notifications" class="scroll-mt-32 space-y-6 pt-4 border-t border-gray-100 dark:border-slate-800">
        <div class="flex items-center gap-3 border-b border-gray-100 dark:border-slate-800 pb-3">
            <span class="w-8 h-8 rounded-xl bg-indigo-100 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold text-sm">9</span>
            <div>
                <h3 class="text-lg font-bold text-gray-800 dark:text-white">
                    <?= __('settings.ds_notif_title') ?>
                </h3>
                <p class="text-xs text-gray-500 dark:text-slate-400"><?= __('settings.ds_notif_desc') ?></p>
            </div>
        </div>

        <!-- Interactive Playground Cards -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <!-- 9.1 SweetAlert2 Toast with TimerProgressBar & Icons -->
            <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 space-y-3 shadow-xs">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-lg">
                        <i class="ph ph-bell"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-900 dark:text-white text-sm"><?= __('settings.ds_notif_toast_title') ?></h4>
                        <p class="text-xs text-gray-500 dark:text-slate-400"><?= __('settings.ds_notif_toast_desc') ?></p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2 pt-1">
                    <button type="button" onclick="testToast('success')" class="px-3 py-2 text-xs font-medium text-emerald-700 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-950/40 hover:bg-emerald-100 dark:hover:bg-emerald-900/60 rounded-xl border border-emerald-200 dark:border-emerald-800/60 transition-colors flex items-center justify-center gap-1.5">
                        <i class="ph ph-check-circle text-base"></i>
                        <span><?= __('settings.ds_btn_toast_success') ?></span>
                    </button>
                    <button type="button" onclick="testToast('error')" class="px-3 py-2 text-xs font-medium text-rose-700 dark:text-rose-300 bg-rose-50 dark:bg-rose-950/40 hover:bg-rose-100 dark:hover:bg-rose-900/60 rounded-xl border border-rose-200 dark:border-rose-800/60 transition-colors flex items-center justify-center gap-1.5">
                        <i class="ph ph-x-circle text-base"></i>
                        <span><?= __('settings.ds_btn_toast_error') ?></span>
                    </button>
                    <button type="button" onclick="testToast('warning')" class="px-3 py-2 text-xs font-medium text-amber-700 dark:text-amber-300 bg-amber-50 dark:bg-amber-950/40 hover:bg-amber-100 dark:hover:bg-amber-900/60 rounded-xl border border-amber-200 dark:border-amber-800/60 transition-colors flex items-center justify-center gap-1.5">
                        <i class="ph ph-warning-circle text-base"></i>
                        <span><?= __('settings.ds_btn_toast_warning') ?></span>
                    </button>
                    <button type="button" onclick="testToast('info')" class="px-3 py-2 text-xs font-medium text-blue-700 dark:text-blue-300 bg-blue-50 dark:bg-blue-950/40 hover:bg-blue-100 dark:hover:bg-blue-900/60 rounded-xl border border-blue-200 dark:border-blue-800/60 transition-colors flex items-center justify-center gap-1.5">
                        <i class="ph ph-info text-base"></i>
                        <span><?= __('settings.ds_btn_toast_info') ?></span>
                    </button>
                </div>

                <div class="p-3 bg-gray-50 dark:bg-slate-800/80 rounded-xl font-mono text-[11px] text-gray-700 dark:text-slate-300">
                    <span class="text-indigo-500 font-semibold">// Universal Toast Call:</span><br>
                    Swal.fire({ toast: true, position: 'top', icon: 'success', title: '...' });
                </div>
            </div>

            <!-- 9.2 Preloading Modal with Spinner & Progress Bar -->
            <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 space-y-3 shadow-xs">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-lg">
                        <i class="ph ph-spinner animate-spin"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-900 dark:text-white text-sm"><?= __('settings.ds_notif_preloader_title') ?></h4>
                        <p class="text-xs text-gray-500 dark:text-slate-400"><?= __('settings.ds_notif_preloader_desc') ?></p>
                    </div>
                </div>

                <div class="pt-1">
                    <button type="button" onclick="testPreloader()" class="w-full px-4 py-2.5 text-xs font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl transition-all shadow-xs flex items-center justify-center gap-2">
                        <i class="ph ph-circle-notch animate-spin text-base"></i>
                        <span><?= __('settings.ds_btn_test_preloader') ?></span>
                    </button>
                </div>

                <div class="p-3 bg-gray-50 dark:bg-slate-800/80 rounded-xl font-mono text-[11px] text-gray-700 dark:text-slate-300">
                    <span class="text-indigo-500 font-semibold">// Preloader Modal with Timer:</span><br>
                    Swal.fire({ title: 'กำลังประมวลผล...', didOpen: () => Swal.showLoading() });
                </div>
            </div>

            <!-- 9.3 Button Preloading -->
            <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 space-y-3 shadow-xs">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 flex items-center justify-center text-lg">
                        <i class="ph ph-cursor-click"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-900 dark:text-white text-sm"><?= __('settings.ds_notif_button_loading_title') ?></h4>
                        <p class="text-xs text-gray-500 dark:text-slate-400"><?= __('settings.ds_notif_button_loading_desc') ?></p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 pt-1">
                    <button type="button" onclick="testButtonLoadingDemo(this)" class="px-4 py-2.5 text-xs font-medium text-white bg-primary-600 hover:bg-primary-700 rounded-xl transition-all shadow-xs flex items-center justify-center gap-2">
                        <i class="ph ph-floppy-disk text-base"></i>
                        <span><?= __('settings.ds_btn_test_button_save') ?></span>
                    </button>
                    <button type="button" onclick="testButtonLoadingCustomDemo(this)" data-loading-text="<?= __('settings.ds_btn_test_button_sync') ?>" class="px-4 py-2.5 text-xs font-medium text-gray-700 dark:text-slate-300 bg-gray-100 dark:bg-slate-800 hover:bg-gray-200 dark:hover:bg-slate-700 rounded-xl transition-all shadow-xs flex items-center justify-center gap-2">
                        <i class="ph ph-arrows-clockwise text-base"></i>
                        <span><?= __('settings.ds_btn_test_button_sync') ?></span>
                    </button>
                </div>

                <div class="p-3 bg-gray-50 dark:bg-slate-800/80 rounded-xl font-mono text-[11px] text-gray-700 dark:text-slate-300">
                    <span class="text-indigo-500 font-semibold">// Button Preloading:</span><br>
                    Automated on all &lt;form&gt; submissions across the system!
                </div>
            </div>

            <!-- 9.4 TopProgressBar with Icon Badge -->
            <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 space-y-3 shadow-xs">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-purple-50 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 flex items-center justify-center text-lg">
                        <i class="ph ph-activity"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-900 dark:text-white text-sm"><?= __('settings.ds_notif_top_progress_title') ?></h4>
                        <p class="text-xs text-gray-500 dark:text-slate-400"><?= __('settings.ds_notif_top_progress_desc') ?></p>
                    </div>
                </div>

                <div class="pt-1">
                    <button type="button" onclick="testTopProgressBarDemo()" class="w-full px-4 py-2.5 text-xs font-medium text-purple-700 dark:text-purple-300 bg-purple-50 dark:bg-purple-950/40 hover:bg-purple-100 dark:hover:bg-purple-900/60 rounded-xl border border-purple-200 dark:border-purple-800/60 transition-colors flex items-center justify-center gap-2">
                        <i class="ph ph-hourglass-high text-base"></i>
                        <span><?= __('settings.ds_btn_test_top_progress') ?></span>
                    </button>
                </div>

                <div class="p-3 bg-gray-50 dark:bg-slate-800/80 rounded-xl font-mono text-[11px] text-gray-700 dark:text-slate-300">
                    <span class="text-indigo-500 font-semibold">// TopProgressBar:</span><br>
                    window.TopProgressBar.start(); // Auto-runs on link clicks
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function copyDesignSpecMarkdown() {
    const rawMarkdown = `# System & UI/UX Design Specification\n## Standard Core System (PHP UMS)\n\nPure PHP 8.4+ MVC Architecture Specification.\nDetailed modules: Core UMS, RBAC, Auth 2FA, Activity & Audit Logs, System Settings, Profile & Sessions, UI/UX Design Standards.\n\nStrict Rules: Zero Raw ID Exposure (Hashids), Shared Hosting Ready, 100% Bilingual Parity (TH/EN), Zero Baht Currency Symbol.`;
    
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(rawMarkdown).then(() => {
            showDesignSpecToast();
        }).catch(() => {
            fallbackCopy(rawMarkdown);
        });
    } else {
        fallbackCopy(rawMarkdown);
    }
}

function fallbackCopy(text) {
    const textArea = document.createElement("textarea");
    textArea.value = text;
    textArea.style.position = "fixed";
    textArea.style.left = "-9999px";
    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();
    try {
        document.execCommand('copy');
        showDesignSpecToast();
    } catch (err) {
        console.error('Fallback copy failed', err);
    }
    document.body.removeChild(textArea);
}

function showDesignSpecToast() {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            toast: true,
            position: 'top',
            icon: 'success',
            title: '<?= addslashes(__('settings.ds_copied_toast')) ?>',
            showConfirmButton: false,
            timer: 2000,
            timerProgressBar: true
        });
    } else {
        alert('<?= addslashes(__('settings.ds_copied_toast')) ?>');
    }
}

function testToast(type) {
    if (typeof Swal !== 'undefined') {
        const samples = {
            success: 'บันทึกข้อมูลเรียบร้อยแล้ว',
            error: 'เกิดข้อผิดพลาดในการเชื่อมต่อ กรุณาลองใหม่อีกครั้ง',
            warning: 'กรุณาตรวจสอบข้อมูลก่อนดำเนินการต่อ',
            info: 'ระบบได้ทำการซิงค์ข้อมูลล่าสุดแล้ว'
        };
        Swal.fire({
            toast: true,
            position: 'top',
            icon: type,
            title: samples[type] || 'Notification test',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            customClass: {
                popup: 'dark:bg-slate-800 dark:text-white border border-gray-100 dark:border-slate-700 shadow-xl'
            }
        });
    }
}

function testPreloader() {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'กำลังประมวลผลข้อมูล...',
            text: 'ระบบกำลังบันทึกและประมวลผล กรุณารอสักครู่',
            allowOutsideClick: false,
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: () => {
                Swal.showLoading();
            }
        }).then(() => {
            testToast('success');
        });
    }
}

function testButtonLoadingDemo(btn) {
    const originalHtml = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="ph ph-circle-notch animate-spin text-base"></i><span>กำลังบันทึก...</span>';
    setTimeout(() => {
        btn.disabled = false;
        btn.innerHTML = originalHtml;
        testToast('success');
    }, 2500);
}

function testButtonLoadingCustomDemo(btn) {
    const originalHtml = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="ph ph-circle-notch animate-spin text-base"></i><span>กำลังซิงค์ข้อมูล...</span>';
    setTimeout(() => {
        btn.disabled = false;
        btn.innerHTML = originalHtml;
        testToast('info');
    }, 2500);
}

function testTopProgressBarDemo() {
    if (window.TopProgressBar) {
        TopProgressBar.start();
        setTimeout(() => {
            TopProgressBar.set(70);
        }, 1000);
        setTimeout(() => {
            TopProgressBar.finish();
            testToast('success');
        }, 2500);
    } else {
        testToast('info');
    }
}
</script>
