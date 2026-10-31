<!-- graft:start -->
## Graft — repo context graph

This repo is indexed in `graft/`: small linked markdown nodes that explain each
system and carry exact file:line spans, kept in sync with the code through git.

For ANY task here — understanding how something works, finding where code lives,
or scoping a change — get context from the graph before grepping or opening
source files. Re-ask freely (it's cheap) and reuse literal identifiers you
already have (symbol, error string, file name) as the query. New to this repo?
Run `graft map` first — a token-budgeted orientation (dir clusters, hubs,
hotspots), no LLM, no key.

- Run `graft ask "<your question>" --source` → ranked nodes with the relevant
  code spans inlined (each hit's ≤8-line crux by default; `--full` for whole
  definitions when the crux isn't enough). Match the tool to the task shape:
  for understanding or editing, the top node IS the answer — cite its
  `covers:` file:line spans and edit straight from `--source`. For
  exhaustive tasks ("every occurrence / every caller of this pattern"), ranked
  results are top-N, not complete — run `graft grep "<literal>"` instead
  (exhaustive over indexed files, grouped by enclosing symbol), falling back
  to raw `grep -rn` only for unindexed files.
- `graft skeleton <file>` → every definition's signature + span, ~10× cheaper
  than reading the file; use it to skim an API surface.
- `graft callers <symbol>` gives precomputed, exact edges — who calls this.
  Add `--direction out` for what it calls, or `--depth N` to walk
  transitively for the full blast radius. For structural questions, skip
  ranking and use this directly.
- Or browse: `graft/INDEX.md` lists every node; follow the links.
- Monorepos and folders of multiple repos rank fairly across sub-projects —
  hits carry `[scope/]` labels naming which one they're from. Narrow with
  `graft ask "<task>" --in <scope>/` once you know where you're working.

If a returned span is truncated ("+N more lines"), open the file at that exact
range before finalizing. Only open source files when a node genuinely lacks a
needed detail, and then at the exact file:line the node points to — never
re-read whole files.

After big code changes, refresh the graph with `graft build` (deterministic,
no API key, $0).
<!-- graft:end -->

<!-- rules:start -->
## AI Agent Rules

1. **MANDATORY RULES INSPECTION FIRST (กฎข้อที่ 1: ต้องอ่านกฎทุกครั้งก่อนเริ่มทำงาน)**:
   Every time before starting any task, analyzing requirements, writing code, or making modifications, the AI Agent **MUST READ AND REVIEW ALL RULES in `GEMINI.md` and `rules.md` without exception**. Never assume, guess, or bypass architecture rules. Always align with the system's strict standards.

2. **ENCODING & MOJIBAKE PREVENTION**: This project uses Thai language heavily. NEVER use Windows PowerShell commands (like `Set-Content`, `Out-File`, `echo`) or inline PowerShell scripts to write or modify files containing Thai (multi-byte) characters. PowerShell can silently corrupt the encoding (causing mojibake/ภาษาต่างดาว). Always use the native `replace_file_content` tool, `write_to_file` tool, or write safe PHP scripts to handle file modifications.

3. **MANDATORY COMPLETE LOCALIZATION (กฎการใส่ภาษาให้ครบ 100% ทุกครั้งที่สร้างหรือแก้ไขสิ่งใหม่)**:
   Whenever creating or modifying any new feature, module, page, view (`*.php`), modal, popup, table header, form, button, Flash message, validation error, or JavaScript alert:
   - **No Hardcoded Text**: NEVER leave hardcoded Thai or English strings in Views or JS. Always use `__('namespace.key')` (or `addslashes(__('namespace.key'))` in JS strings).
   - **100% Symmetric TH & EN Parity**: When adding any translation key to `app/Lang/th/<namespace>.php`, you MUST add the identical key with accurate English translation to `app/Lang/en/<namespace>.php` in the same step.
   - **Important `Lang::trans()` Behavior**: `__('...')` returns the raw key string `$key` (not `null`) when a key is missing. Therefore, every single key passed to `__('...')` MUST exist in both `app/Lang/th/` and `app/Lang/en/`.
   - **Verification & DB Sync**: Before completing work, run `php cli/compare_lang.php` (must output `[]` with 0 missing keys) and run `php cli/sync_lang.php` to synchronize translations to the MySQL `translations` table.

4. **BUTTON PRELOADING & DOUBLE-SUBMIT PREVENTION (กฎสถานะ Preloading ของปุ่มและการป้องกันการกดซ้ำ)**:
   All action buttons and form submission buttons (Save, Update, Create, Delete, Sync, AJAX requests) MUST implement a preloading/loading state:
   - **Prevent Double Submit**: When clicked or submitted, disable the button immediately (`btn.disabled = true;`) to prevent race conditions or duplicate submissions.
   - **Spinner & Loading Text**: Show an animated spinner icon (e.g., `<i class="ph ph-circle-notch animate-spin text-base mr-2"></i>` or `<i class="ph ph-spinner animate-spin mr-2"></i>`) and localized loading text (e.g., `<?= __('common.saving') ?>`, `<?= __('common.please_wait') ?>`, or contextual loading label).
   - **Re-enable on Completion**: Re-enable the button and restore its original content/state when processing completes or upon error (in `catch` / `finally`).

5. **SWEETALERT2 TOAST POSITION: TOP-CENTER (กฎการแจ้งเตือน SweetAlert2 Toast ตำแหน่งบนกลาง)**:
   Whenever displaying Toast notifications with SweetAlert2 (`Swal.fire({ toast: true, ... })` or `Swal.mixin`):
   - **Mandatory Position**: The position MUST STRICTLY be set to **`top` (center top / บนกลาง)**: `position: 'top'`. NEVER use `top-end`, `top-right`, `bottom-right`, `center`, or any other position.
   - **Standard Options**: `toast: true, position: 'top', showConfirmButton: false, timer: 3000, timerProgressBar: true`.
   - **Dark Mode Support**: Must support dark mode styling (e.g., `customClass: { popup: 'dark:bg-slate-800 dark:text-white border border-gray-100 dark:border-slate-700 shadow-xl' }`).
   - **No Native Dialogs**: NEVER use browser native `alert()`, `confirm()`, or `prompt()`.

6. **NO BAHT SYMBOL (฿) RULE**: Do NOT use the Thai Baht currency symbol `฿` anywhere in the project (UI, tables, mock data, charts, reports, translations, or code). Always omit it or use 'บาท' / 'THB' as specified by context. Remove all instances of `฿`.

7. **SIDEBAR VISIBILITY & DIRECT NAVIGATION**: Whenever developing or adding any new system/module, NEVER leave it hidden in collapsed menus or expect the user to type URLs in chat or the browser address bar. Always add it prominently and visibly to the sidebar navigation (`app/Views/layouts/app.php`), keeping it immediately accessible with clear direct links to all sub-pages, dashboards, and test/demo features.

8. **AJAX DELETE OPERATIONS & GET FALLBACKS**: NEVER use traditional HTML `form.submit()` for delete operations. Always use `axios.post` with SweetAlert2 (`Swal.fire`) confirmation dialogs (matching `users/index.php` and `roles/index.php`) so the browser does not navigate to `/delete` URLs or display raw JSON. Controllers must handle both AJAX (JSON response) and standard POST (redirect with flash). In `public/index.php`, always register a GET fallback redirect for `/resource/{id}/delete` back to the index view to prevent 404 errors if users reload or directly navigate.

9. **RATE LIMITING & 429 PREVENTION**: Always call `RateLimiter::reset('/login')` upon successful login and 2FA verification. `RateLimiter` must use a sliding time window (relative to `updated_at`) so failed attempts do not accumulate indefinitely and lock users out unexpectedly.

10. **YOUTUBE-STYLE PRELOADER PROGRESS BAR**: All layouts (`app/Views/layouts/*.php`) and page navigation flows must maintain the YouTube-style top preloader progress bar (`app/Views/layouts/partials/progress_bar.php`). Always include `<?php require __DIR__ . '/partials/progress_bar.php'; ?>` right before `</body>` in any layout (preserving Chromium Paint-Holding and eliminating white flashes). It automatically handles link clicks, form submissions, page transitions, and non-polling AJAX requests.

11. **STICKY SIDE NAVIGATION & TABS ON SCROLL**: In multi-column views with side navigation or tabs alongside long forms/content (such as `settings/index.php`), always ensure the sidebar/tab card is sticky on scroll (`lg:sticky lg:top-6 self-start z-10` with `items-start` on the parent grid container) so navigation stays immediately accessible without requiring users to scroll all the way back to the top.

12. **SOCIAL LOGIN & VECTOR SVG STANDARDS (กฎมาตรฐาน Social Login และโลโก้ SVG)**: All brand logos and provider icons (Google, LINE, Facebook, GitHub, etc.) MUST use pure inline vector SVGs or verified local assets. NEVER use external hotlinks (such as Wikimedia Commons or external CDNs) which can fail offline or get blocked. All social OAuth flows must support clean callback URLs and state verification, while account disconnect flows must use SweetAlert2 confirmation dialogs (never browser native `confirm()`).

13. **CLIPBOARD COPY UX & FEEDBACK (กฎมาตรฐานการคัดลอกลงคลิปบอร์ด)**: All copy-to-clipboard interactions (e.g. OAuth Redirect URIs, tokens, codes) must provide immediate dual feedback: temporary icon change to a green checkmark, accompanied by a SweetAlert2 Toast at `position: 'top'` with localized text (`__('settings.copied_to_clipboard')`), and include fallback execution for non-HTTPS/HTTP localhost contexts.

14. **MANDATORY SERVER-SIDE DATATABLES PROCESSING (กฎการทำงานของ DataTable ต้องเป็นแบบ Server-Side เสมอ)**: All DataTables in the project (Users, Roles, Logs, Modules, etc.) MUST strictly operate using **Server-Side Processing (`serverSide: true, processing: true`)**.
   - **Dedicated AJAX Endpoints:** Every table must have a dedicated AJAX endpoint (e.g. `GET /resource/data`) handled by its controller using `\App\Core\DataTableHelper::process()` with parameterized PDO queries.
   - **Database-Level Operations:** Pagination (`LIMIT :start, :length`), multi-column search (`LIKE`), and sorting (`ORDER BY`) must be executed at the MySQL database layer for high scalability and minimal client memory overhead.
   - **ID Obfuscation (Hashids):** Primary keys in server-side responses MUST be encoded using `\App\Core\Security::encodeId($id)` before returning JSON to the frontend. Never expose sequential integer IDs.
   - **Standard DOM Layout:** Custom layouts must include `dom: 'rt<"flex flex-col md:flex-row justify-between items-center mt-4"lip><"clear">',` to maintain uniform placement of Length, Info, and Pagination controls at the bottom of the table.
   - **Global Localization:** Never define individual `language:` blocks per view; adhere to the global configuration in `layouts/app.php`.

15. **FIXED MODAL HEADER & FOOTER WITH SCROLLABLE BODY RULE (กฎโครงสร้าง Modal แบบ Fixed Header/Footer และเนื้อหากลางเลื่อนได้)**: All modal dialogs throughout the project MUST strictly follow the fixed header, scrollable middle body, and fixed footer flexbox architecture:
   - **Height Restriction & Container:** The modal panel must use `flex flex-col max-h-[85vh] md:max-h-[90vh] overflow-hidden` (if a `<form>` wraps the contents, the form must also include `flex flex-col h-full max-h-[85vh] md:max-h-[90vh] overflow-hidden`).
   - **Fixed Header:** The top header must be `flex-shrink-0 border-b border-gray-100 dark:border-slate-800 px-6 py-4 bg-white dark:bg-slate-900` with title and close button. It must NEVER scroll out of view.
   - **Scrollable Middle Body:** Content area in the middle must be `flex-1 overflow-y-auto p-6` (only the middle content scrolls when exceeding screen height).
   - **Fixed Footer:** Action buttons (Save, Cancel, Submit) must remain pinned at the bottom using `flex-shrink-0 border-t border-gray-100 dark:border-slate-800 px-6 py-4 bg-white dark:bg-slate-900 flex justify-end gap-3`, ensuring action buttons are always immediately visible and clickable without scrolling.

16. **MULTI-TIER TRANSLATION SYSTEM & ZERO RAW KEYS (กฎระบบภาษา 2 ชั้น และการป้องกัน Raw Key หลุดสู่หน้าจอ)**:
   - **Zero Raw Translation Keys**: NEVER allow raw translation keys (such as `settings.preset_slate`, `auth.username`, etc.) to be rendered or leaked to the user interface.
   - **Two-Tier Architecture (`Lang::load()`)**: `App\Core\Lang::load()` MUST ALWAYS load disk language files (`app/Lang/{locale}/{namespace}.php`) as the mandatory **Baseline**, overlaid with dynamic database translations from the MySQL `translations` table. This guarantees that UI labels always display human-readable text even before DB sync runs or if the database is temporarily unreachable.
   - **100% Symmetric Parity & Mandatory Sync**: Every single key used in views or controllers must be defined symmetrically in both `app/Lang/th/<namespace>.php` and `app/Lang/en/<namespace>.php`. Run `php cli/compare_lang.php` (must output `[]`) and run `php cli/sync_lang.php` to persist changes into the MySQL `translations` table.

17. **LOGIN BUTTON COLOR LOCK (ห้ามเปลี่ยนสีปุ่มเข้าสู่ระบบหน้า Login เด็ดขาด)**:
   - The Sign-In submit button on the Login page (`app/Views/auth/login.php`) MUST strictly keep its original blue styling: `bg-blue-600 hover:bg-blue-700 shadow-lg shadow-blue-500/30 dark:shadow-blue-900/20 focus:ring-blue-500`.
   - **NEVER** change the login button to `bg-sidebar` or `primary-*` theme colors under any circumstances.

18. **MODULAR ROUTING ARCHITECTURE & PHP_HEALTH COMPATIBILITY (กฎสถาปัตยกรรมระบบ Route แบบแยก Domain และความเข้ากันได้กับ php_health)**:
   - **Domain-Driven Modular Routes**: Core routes must strictly be separated by domain into individual files in `app/Routes/` (e.g. `01_auth.php`, `02_users.php`, `03_roles.php`, etc.). NEVER define inline route definitions in `public/index.php`. Keep `public/index.php` minimal and clean.
   - **Auto-loading Extension Modules**: The system must automatically detect and load module routes from `app/Modules/*/routes.php` via `App\Core\RouteRegistrar` (or `$router->loadRoutes()`) without requiring any manual route registration in core files.
   - **Cross-Compatibility with php_health**: The Router MUST support dual-signature syntax:
     1. Array attributes style (matching `php_health`): `$router->group(['prefix' => '/module', 'middlewares' => [...]], function(Router $r) { ... })`
     2. Shorthand style: `$router->group('/prefix', Groups::auth(), function(Router $r) { ... })`
     3. Helper method: `$router->deleteWithFallback($path, $handler, $redirectUrl)` to guarantee Rule #8 compliance.
   - **PHP 8+ Parameter Reflection Safety**: The router must inspect handler parameter requirements using reflection (`ReflectionFunctionAbstract`) to prevent `Fatal error: Uncaught Error: Unknown named parameter` when invoking closures without parameter definitions for routes containing dynamic URI segments (such as `{id}`).
   - **Module Isolation**: ถ้าไฟล์ route ของ Module ใดพัง ระบบจะ log (`Logger::error`) แล้วข้าม Module นั้น — เส้นทางอื่นทั้งระบบต้องไม่ล่มตาม

19. **GLOBAL CSRF & SECURITY HEADERS (กฎความปลอดภัยระดับระบบ — CSRF อัตโนมัติและ Security Headers)**:
   - **CSRF บังคับอัตโนมัติ**: Router ตรวจ CSRF token ให้ทุก POST/PUT/PATCH/DELETE แล้ว — **ห้ามเรียก `Security::checkCsrf()` เองใน controller/method ใหม่** (ซ้ำซ้อน) ข้อยกเว้นมีเพียง `/api/*` (JWT) และ `/install/*` ก่อนล็อกติดตั้ง
   - **Upload ผ่าน UploadManager เท่านั้น**: ทุกไฟล์อัปโหลดต้องผ่าน `App\Core\UploadManager` (extension whitelist คู่กับ MIME จริง + ชื่อไฟล์สุ่ม) — ห้าม `move_uploaded_file`/เขียน base64 ลงดิสก์เองใน controller
   - **Redirect ต้องผ่านตัวกลาง**: ใช้ `$this->redirect()` / `App\Core\Redirect::to()` (relative path เท่านั้น) และ `redirectExternal($url, $allowedHosts)` สำหรับ URL ภายนอก — ห้าม `header('Location: ...')` ตรง
   - **Exception ห้ามกลืนเงียบ**: ทุก catch ต้องบันทึกด้วย `Logger::error()` (หรือ `Logger::warning()`) ก่อนตอบ error กลับ ห้าม `catch (\Throwable $e) {}` เด็ดขาด
   - **ตรวจงานก่อนจบ**: รัน `vendor/bin/phpunit` (ต้อง OK ทุก test) + `php cli/compare_lang.php` (ต้อง `[]`) ทุกครั้งก่อนส่งมอบงาน

20. **AJAX DUAL-RESPONSE CONTRACT (กฎสัญญา AJAX สองทาง)**:
   - ฟอร์มที่มี `data-ajax="true"` ต้องใช้ `respond()` เท่านั้น — **ห้าม `Session::setFlash()` + `$this->redirect()` ในเมธอดที่รับฟอร์ม AJAX** (ทำให้เกิด toast สำเร็จปลอม)
   - ข้อผิดพลาด validation ส่งกลับเป็น `errors: {field: [messages]}` เพื่อให้ engine ไฮไลต์รายฟิลด์
   - เมื่อ session หมดอายุระหว่าง AJAX: AuthMiddleware ตอบ 401 + `session_expired: true` → engine แจ้งเตือนและพาไป `/login` อัตโนมัติ (ห้ามเขียน logic นี้ซ้ำใน controller)
<!-- rules:end -->
