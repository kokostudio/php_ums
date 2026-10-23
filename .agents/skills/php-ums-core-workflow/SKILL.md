---
name: php-ums-core-workflow
description: >-
  Workflow and standards for developing the PHP UMS (User Management System)
  Core project. Covers MVC conventions, routing, ID encryption (Hashids),
  clean URLs, settings tab pattern, and system-wide coding rules. Use when
  adding new modules, routes, controllers, views, or settings sections to
  this project.
---

# PHP UMS Core – Development Workflow

## Overview
PHP UMS is a **Pure PHP 8.4+ MVC Core System** (no framework) serving as the
central management hub for User, Role, Log, Profile, and System Settings.
All standards below are mandatory and enforced via `rules.md` in the project
root.

## Tech Stack
- **Backend:** PHP 8.4+, PSR-4 autoload via Composer
- **Database:** MySQL + PDO (Prepared Statements only)
- **Frontend:** Tailwind CSS CDN (dark mode via `class`), DataTables (server-side), Axios, SweetAlert2, Phosphor Icons
- **Font:** Google Font **Prompt** (wght 300–700) — รองรับทั้งภาษาไทย + อังกฤษ, ใช้เป็นมาตรฐานเดียวของทั้งระบบ
- **Auth:** Session-based with CSRF protection (`Security::checkCsrf()`)
- **ID Encryption:** `hashids/hashids` library via `Security::encodeId()` / `decodeId()`

---

## Workflow

### 1. Adding a New Module / Feature

0. **Inspect Rules First (CRITICAL)** — Always read and review all rules in `GEMINI.md` and `rules.md` before analyzing, planning, designing, or writing any code.
1. **Plan first** — create or update `implementation_plan.md` artifact and get approval before writing code.
2. **Create Controller** in `app/Controllers/` extending `App\Core\Controller`.
3. **Register Routes** in `public/index.php` using clean URL pattern (see Rule: Clean URL).
4. **Create Views** in `app/Views/{module}/` — use `app.php` layout via `$this->view()`.
5. **Apply Hashids** on all ID-bearing URLs and form values (see Rule: ID Encryption).
6. **Update CHANGELOG.md** (Thai language only) after completing the feature.

### 2. Adding a New Route

Always use **clean URLs** — never query strings for navigation:

```php
// ✅ Correct
$router->get('/settings/{tab}', [SettingController::class, 'index'], [AuthMiddleware::class]);
$router->post('/settings/{tab}', [SettingController::class, 'update'], [AuthMiddleware::class]);

// ❌ Wrong
$router->get('/settings', ...); // then read $_GET['tab'] inside controller
```

**Controller Parameter Matching (PHP 8+):**
The parameter name in your controller method MUST exactly match the dynamic segment in the route to avoid PHP 8 named parameter errors.
```php
// Route: /users/{id}/edit
public function edit($id) { ... }
```

Route parameter must be **whitelisted** in the controller:
```php
public function index($tab = 'general') {
    $tab = in_array($tab, ['general', 'login', 'email']) ? $tab : 'general';
    // ...
}
```

### 3. ID Encryption Standard (Hashids)

All numeric database IDs must be **encoded before leaving the backend** and **decoded immediately upon arrival**:

```php
// Encode before sending to UI (tables, links, dropdown values)
$row['hash_id'] = Security::encodeId((int)$row['id']);

// Decode immediately in controller methods that receive IDs
public function edit($id) {
    $realId = Security::decodeId($id);
    if (!$realId) {
        Session::setFlash('error', 'Invalid ID.');
        $this->redirect('/module');
    }
    // use $realId for DB queries
}
```

Never use `$_POST['role_id']` directly — decode it first:
```php
$roleIdHash = $_POST['role_id'] ?? null;
$roleId = $roleIdHash ? Security::decodeId($roleIdHash) : null;
```

### 4. Settings Tab Pattern

When a Settings-like page needs multiple sections:

1. Add a route: `GET /settings/{tab}` and `POST /settings/{tab}`
2. Controller method accepts `$tab` parameter with whitelist validation
3. View reads `$tab` from the passed variable (not `$_GET`)
4. Sidebar links use clean URLs: `/settings/general`, `/settings/login`
5. Form `action` uses clean URL: `/settings/general`
6. After save, redirect to `/settings/{$tab}`

### 5. DataTables (Instant Load / Client-Side Preferred)

- **Preferred Architecture:** Always use **Client-Side DataTables** (fetch data in PHP and render `<tbody>` directly in the HTML view) to prevent layout jumping, FOUC, and skeleton loaders. This ensures 100% instant loading (like `/roles`, `/users`, `/logs`).
- **Large Datasets:** For logs or large datasets, use a `LIMIT 1000` (or similar) in the SQL query instead of Server-Side AJAX pagination, to maintain the instant load experience without crashing the browser.
- **Server-Side AJAX:** Should ONLY be used if explicitly required for massive datasets (100k+ rows) where a 1000-row limit is unacceptable. If used, encode `hash_id` in the data endpoint and never expose raw `id`.
- **Custom Filtering:** For Client-Side tables, use `$.fn.dataTable.ext.search.push()` to implement custom dropdown filters (like Role or Status) that trigger via `table.draw()`.
- **DOM Layout Standard:** If defining a custom DataTable layout (e.g. hiding default search), always append `dom: 'rt<"flex flex-col md:flex-row justify-between items-center mt-4"lip><"clear">',` to maintain the standard positioning of length, info, and pagination at the bottom.

### 6. Form Conventions

- All forms include `<input type="hidden" name="csrf_token" value="<?= Session::getCsrfToken() ?>">`
- Backend always calls `Security::checkCsrf()` as the first line of POST handlers
- Use `Security::sanitize()` on all text inputs before storing
- File uploads: validate MIME type with `mime_content_type()`, store in `public/uploads/`
- **Global Loading State:** All form submissions automatically trigger a loading spinner (`ph-circle-notch`) and disable the submit button via a global JS listener. If a form should NOT trigger this (e.g., live search forms), you MUST add the `data-no-loading` attribute to the `<form>` tag.

### 7. UI & Typography Standards

**Font (Prompt):**
```html
<!-- ใน <head> ของทุก layout -->
<link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">
```
```js
tailwind.config = { theme: { extend: { fontFamily: { sans: ['Prompt', 'sans-serif'] } } } }
```
```css
body { font-family: 'Prompt', sans-serif; }
```

**Dark Mode & Scrollbar:**
- Dark mode is class-based (`darkMode: 'class'` in Tailwind config). Preference stored in `localStorage.theme`.
- **Scrollbar MUST match the theme dynamically:** Use custom thin 6px scrollbars. The track background must hide white flashes (e.g., adding `bg-slate-50 dark:bg-slate-900` to the `<html>` element). Thumb colors must adapt to dark/light modes.
- Firefox & WebKit must adhere to the dynamic theme color via PHP-injected CSS variables in the `<style>` block if necessary.

**Icons & Shapes:**
- Icons: Phosphor Icons (Outline: `<i class="ph ph-{name}"></i>`, Filled: `<i class="ph-fill ph-{name}"></i>`)
- **Shapes (CRITICAL):** UI themes (cards, inputs, buttons) must use square-like slight rounding (`rounded-md`). Do NOT use heavily rounded (`rounded-xl` or `rounded-2xl`) unless specifically requested.

**Color System:**
- Primary: `#3b82f6` (blue-500)
- Secondary: `#64748b` (slate-500)
- ห้ามใช้สีนอก palette โดยไม่มีเหตุผล

**Form Validation:**
- **Validation Messages:** Validation error messages MUST appear immediately below their respective inputs (e.g., `<p id="field-error" class="text-xs text-red-500 mt-1"></p>`). Do not rely solely on top-level alerts for field-specific errors.

### 8. Date & Time Formatting Standards

- **Standard Date Format:** `d/m/Y` (เช่น `30/12/2026`)
- **Standard Datetime Format:** `d/m/Y H:i` หรือ `d/m/Y H:i:s` (เช่น `30/12/2026 15:30:00`)
- **PHP Backend Helper:**
```php
// แสดงวันที่: 30/12/2026
<?= formatDate($user['created_at']) ?>

// แสดงวันและเวลา: 30/12/2026 15:30
<?= formatDate($user['last_login'], true) ?>

// แสดงวัน เวลา และวินาที: 30/12/2026 15:30:45
<?= formatDate($log['created_at'], true, true) ?>
```
- **JavaScript / DataTables Helper:**
```javascript
// ใน DataTables columns render:
{
    data: 'created_at',
    render: function(data) {
        return formatDate(data, true); // d/m/Y H:i
    }
}
```

### 9. Multi-Language (i18n) Workflow & Standards

- **Languages:** Thai (`th`) as default primary, English (`en`) as secondary
- **Database Storage:** Translations are stored in the `translations` table, editable via the `/settings/translations` UI.
- **Usage in Views / Controllers:**
```php
// Basic translation
<?= __('common.save') ?>
<?= __('users.title') ?>

// With placeholder replacement (database value should be "Welcome :name")
<?= __('dashboard.welcome_user', ['name' => $username]) ?>

// Read current active locale
$locale = \App\Core\Lang::locale(); // 'th' or 'en'
```
- **Language Switch Route:** `GET /lang/{locale}` (e.g. `/lang/th`, `/lang/en`)
- **DataTables Translation:** DO NOT define `language: {}` blocks inside `DataTable()` initialization. DataTables is already configured globally in `layouts/app.php` to handle translations automatically.

---

## Common Mistakes

1. **Exposing raw IDs** — always encode with `Security::encodeId()` before sending to UI.
2. **Using `?tab=` query strings** — always use `/settings/{tab}` clean URL pattern.
3. **Hardcoding Text in Views** — always use `__('module.key')` helper instead of raw text.
4. **Hardcoding Date Formats** — always use `formatDate($date)` (backend) or `formatDate(data)` (frontend).
5. **Forgetting CSRF** — every POST handler must call `Security::checkCsrf()` first.
6. **Writing Business Logic in Controllers** — move complex logic to Services/Repositories.
7. **Skipping whitelist on URL segments** — always validate `$tab`, `$id` segments before use.

### 10. Dynamic Theme & Tailwind Conventions (UI/UX)
- **Dynamic Palette:** `layouts/app.php` injects the current theme color into Tailwind's `primary` color scale. Use `primary-*` classes for accents, active states, and hover effects (e.g. `text-primary-400`, `bg-primary-900/20`).
- **Global Color Scope:** Do not override base Tailwind colors (blue, gray, slate) globally.
- **Buttons:** Main action buttons should use `bg-sidebar` to match the current sidebar theme. Destructive buttons should use standard red (`bg-red-500`).
- **Confirmation Dialogs:** Destructive actions MUST trigger **SweetAlert2 (`Swal.fire`)** confirmation dialogs instead of the browser's native `confirm()`.
- **Ultra Dark Mode:** Use translucent backgrounds for cards/modals in dark mode (e.g., `dark:bg-black/20 dark:backdrop-blur-md`).

### 11. Pinned Sidebar Menus (Favorites)
- **Feature:** The sidebar supports a "Pinned Menus" feature where users can pin frequently used submenus to the top.
- **UI:** A pin icon appears when hovering over a submenu item. Do NOT rely on Tailwind dynamic arbitrary classes (like `group-hover/menu`) for elements appended via JS, as Tailwind CDN may fail to parse them. Use standard CSS in `<style>` instead (e.g., `.submenu-item-link:hover .pin-menu-btn { opacity: 1; }`).
- **Event Bubbling (CRITICAL BUG FIX):** Do NOT use `onclick="event.preventDefault(); event.stopPropagation();"` directly in inline HTML, as it breaks in some browsers (e.g. Firefox) or propagates unintentionally, causing the pinned menu to fail. ALWAYS pass the event explicitly: `onclick="togglePinMenu(event, '${url}')"` and handle `e.preventDefault(); e.stopPropagation();` inside the JavaScript function.
- **Z-Index (CRITICAL BUG FIX):** ALWAYS add `z-10` to dynamically appended buttons (like `.pin-menu-btn` and `.unpin-menu-btn`) so they sit above parent flex containers and remain clickable.
- **Storage:** Pinned menu states (href arrays) are stored entirely client-side in `localStorage` under `pinnedMenus`.
- **Behavior:** The DOM is manipulated dynamically via JS in `layouts/app.php` to clone pinned menus into the top `#pinned-menus-container` on load and on click. No backend changes are required for this UI feature.

### 12. Sidebar Behavior (Collapse / Expand)
- **Feature:** The sidebar can be collapsed to save screen space, displaying only icons.
- **State:** Toggled via `html.sidebar-collapsed` and persisted in `localStorage.sidebarCollapsed`.
- **HTML Structure:** All text inside sidebar links (`<a>` tags) MUST be wrapped in a `<span>` tag (e.g., `<i class="..."></i> <span>Dashboard</span>`). This is mandatory so the CSS can target and hide the text (`display: none`) when the sidebar is collapsed, leaving only the icon visible.
- **Pin Icons:** When the sidebar is collapsed, pin icons are hidden automatically via CSS to prevent UI clutter.

### 13. System Font Size Scaling & Real-Time UI
- **Tailwind Rem Scaling:** The system supports a 3-level font size scaling (14px, 16px, 18px). This is achieved by dynamically injecting a Tailwind class (like `text-[16px]`) onto the `<html>` tag.
- **Global Effect:** Because Tailwind utilizes `rem` for spacing, padding, and fonts, changing the root font size scales the entire UI proportionally and flawlessly.
- **Real-time Active State:** For Settings pages with radio/card selectors (e.g., Font Size, Theme), use JavaScript (jQuery) to instantly toggle the active Tailwind classes (`border-primary-*`, `bg-primary-*`) upon click. Always provide a real-time preview (e.g., swapping the class on `<html>`) before the user submits the form.

### 14. PHP 8.1+ Null Safety (Strict Types)
- **String Functions Compatibility:** In PHP 8.1+, passing `null` to string-expecting functions like `htmlspecialchars()` or `preg_match()` throws a Fatal Error / TypeError.
- **Null Coalescing Operator:** ALWAYS use the null coalescing operator `?? ''` when working with potentially null values from the database (e.g., `$session['ip_address'] ?? ''`) before rendering them or using them in string operations.

### 15. Auth Layout & Premium Background Patterns
- **Layout:** Authentication pages (login, register) use `layouts/auth.php`.
- **Background Setting:** The background pattern is customizable via Settings (`login_pattern`).
- **Premium Aesthetics:** Available patterns include `dots` (Dots & Orbs), `grid` (Minimal Grid), `mesh` (Soft Mesh), `lines` (Diagonal Lines), `glass` (Glassmorphism), and `none`.
- **Implementation:** These patterns rely on absolute positioning, `pointer-events-none`, and Tailwind utility classes (no custom CSS animations that require compilation) to ensure they work perfectly via Tailwind CDN. Never place these patterns inside the login form card itself; they belong in the layout's right-side background panel.

### 16. Profile & Authentication Features
- **Social Login:** The system supports OAuth 2.0 Social Logins (Google and LINE). Flow is handled natively via cURL in `SocialAuthController.php` to minimize dependencies.
- **Auto Registration:** Social logins automatically create a new user and assign the default system role if the email or provider ID doesn't exist.
- **Extended Profile:** User profiles include First Name (`first_name`) and Last Name (`last_name`). Always include these fields in user creation/editing forms and profile settings.

### 17. Advanced Security (Password Policy, 2FA & RBAC)
- **Password Complexity:** All newly created or updated passwords MUST pass the `\App\Core\Security::validatePasswordComplexity($password)` check. This helper validates against the settings defined in the database (`pwd_require_upper`, `pwd_require_lower`, `pwd_require_number`, `pwd_require_special`, `password_min_length`).
- **Two-Factor Authentication (2FA):** 
  - Managed via `google2fa_secret`.
  - The `AuthMiddleware` enforces a "Force 2FA for Admins" policy if `Settings::get('force_2fa_admin')` is enabled, redirecting admins to their profile page to configure 2FA before allowing access to the dashboard.
  - Session handles the state via `$_SESSION['2fa_enabled']`. Always make sure `2fa_enabled` is correctly updated in the Session when a user enables/disables 2FA in the Profile controller.
- **RBAC Superadmin Protection:**
  - Users with usernames `superadmin` or `admin` are protected core accounts.
  - Lower roles MUST NOT be able to delete or edit these accounts.
  - Backend controllers (like `UserController`) must explicitly reject updates or deletes to these accounts if the requester is not a superadmin themselves.
  - The UI (e.g. `users/index.php`) must visually disable the edit/delete buttons for these accounts.

---

## 🎯 Quick Checklist for New Features / Modules
To ensure absolute compliance with the system architecture, always verify the following when creating anything new:

### 1. Routing & Controllers (Backend)
- [ ] Use **Clean URLs** exclusively (e.g., `/users/create`), NEVER query strings (`?page=create`).
- [ ] Controller method parameter names MUST match the Route named parameters exactly (e.g., `{id}` -> `function edit($id)`).
- [ ] Enforce **CSRF Protection** on all POST handlers (`Security::checkCsrf()`).
- [ ] Decode IDs (`decodeId`) instantly when received, and Encode IDs (`encodeId`) before returning them to the UI.
- [ ] Register **GET Fallback Redirects** in `public/index.php` for delete endpoints (`/resource/{id}/delete`) to avoid 404s on browser reloads.
- [ ] Reset login rate limits on successful auth (`RateLimiter::reset('/login')`).

### 2. Views & UI (Frontend)
- [ ] **Sidebar Exposure:** Always add new modules prominently to the sidebar navigation in `layouts/app.php` with direct links.
- [ ] **NO Hardcoded Text:** Always use the `<?= __('module.key') ?>` helper for translations.
- [ ] **Flash & Popup Translations:** All controller `Session::setFlash()` messages and `Swal.fire()` messages must be translated via `__()` helper. No raw strings allowed.
- [ ] **Date Standard:** Always use `formatDate()` (PHP) or `formatDate()` (JS) to ensure the `d/m/Y` global standard.
- [ ] **Primary Buttons:** Must use `bg-sidebar` so the color matches the dynamic theme.
- [ ] **Destructive Buttons:** Must use `bg-red-500` and MUST trigger a **SweetAlert2 (`Swal.fire`)** confirmation dialog, not native `confirm()`.
- [ ] **AJAX Deletion:** Delete actions MUST use `axios.post` with SweetAlert2, NEVER traditional HTML `form.submit()`.
- [ ] **Active/Hover States:** Must use `primary-*` classes (e.g. `bg-primary-50`) instead of hardcoding slate/blue.

### 3. DataTables & Layout Styling
- [ ] Use **Client-Side Processing** (PHP rendered `<tbody>`) as the default for instant loading and to prevent FOUC/flicker.
- [ ] Apply `LIMIT 1000` (or similar) to SQL queries if the dataset (like logs) can grow infinitely.
- [ ] DO NOT define `language:` blocks inline; it's handled globally in the layout (including beautiful HTML empty states with icons for `emptyTable` and `zeroRecords`).
- [ ] **Empty State Fallback:** NEVER add manual `<td colspan="...">No data</td>` fallbacks inside `<tbody>` when the array is empty. DataTables natively handles empty tables. Manual HTML fallbacks cause "Incorrect column count" JS crashes.
- [ ] **DOM Standard:** NEVER override `dom:` locally unless absolutely necessary (e.g. removing default search). If you do, use the perfectly balanced grouped layout: `dom: 'rt<"flex flex-col md:flex-row justify-between items-center p-4 border-t border-gray-100 dark:border-slate-800"<"flex items-center text-sm text-gray-500 dark:text-slate-400 gap-4"li>p><"clear">',`. Do NOT use the old `"lip"` grouped flex container as it distorts alignment.
- [ ] **Layout Footer Balancing:** If adjusting global layout margins, ensure `<main>` does not have excessive bottom padding (`pb-0`) that pushes the footer up, and ensure `<footer>` has symmetrical vertical padding (e.g., `py-4`) so it centers beautifully between the content separator and the bottom of the screen.

### 4. PHP 8.1+ Null Safety
- [ ] Any nullable string variable fetched from the DB MUST use the `?? ''` null coalescing operator before being passed to string functions.

### 5. Advanced Security & Error Handling
- [ ] **Rate Limiting:** Protect sensitive endpoints (Login, OTP, Password Reset) against brute-force attacks by integrating with the `rate_limits` table structure.
- [ ] **Error Logging:** Do not output raw errors in production. `APP_DEBUG=false` ensures `App\Core\ErrorHandler` logs errors to `storage/logs/error.log`.
- [ ] **Maintenance Mode:** Utilize `app/Views/errors/maintenance.php` when locking down the system for critical updates.


### 18. Encoding & Mojibake Prevention (CRITICAL)
- **The Issue:** Modifying files containing Thai characters or any multi-byte UTF-8 text using Windows PowerShell commands (`Set-Content`, `echo`, `cat >`, or `Out-File`) often results in severe encoding corruption (Mojibake / ภาษาต่างดาว). 
- **The Rule:** NEVER use PowerShell terminal commands or inline PowerShell scripts to append, edit, or create files that contain Thai characters.
- **The Solution:** 
  1. ALWAYS use the Agent's native `replace_file_content` tool for targeted edits.
  2. ALWAYS use the Agent's native `write_to_file` tool to create new files or append to files.
  3. If you must use a script to modify files, write a standalone PHP script (e.g. `file_put_contents`) that doesn't involve passing multi-byte strings through the shell execution layer.

### 19. Currency & Symbol Restrictions: NO '฿' SYMBOL (CRITICAL)
- **The Rule:** NEVER use the Thai Baht currency symbol `฿` anywhere in the project (UI, tables, mock data, charts, reports, translations, or code).
- **The Reason:** Requested by system guidelines/standards; symbol `฿` must be completely omitted or replaced with 'บาท' / 'THB' as appropriate.
- **Action:** Remove all instances of `฿` across views, translations, and templates.

### 20. Sidebar Visibility & Direct Navigation (CRITICAL UI CONVENTION)
- **The Issue:** Creating new features or modules without adding them to the sidebar leaves users unaware of how to access them, forcing manual typing of URLs or searching through chat.
- **The Rule:** Whenever developing or adding any new system/module, NEVER leave it hidden in collapsed menus or expect the user to type URLs manually.
- **The Solution:** Always add the module prominently to the sidebar navigation in `app/Views/layouts/app.php`, keeping it expanded or immediately visible, with clear direct links to all sub-pages, dashboards, and test/demo features.

### 21. Delete Operations (AJAX + SweetAlert + GET Fallback)
- **The Issue:** Traditional HTML `form.submit()` to a delete endpoint causes the browser to navigate to `/resource/{id}/delete`, rendering raw JSON. If the user refreshes, a GET request is sent, triggering a 404 error.
- **The Rule:** NEVER use `form.submit()` for table row deletions.
- **The Solution:**
  1. Always use `axios.post()` with SweetAlert2 (`Swal.fire`) confirmation dialogs (following the pattern in `users/index.php` and `roles/index.php`).
  2. On success, remove the row with DataTables API (`table.row($tr).remove().draw()`) or reload the view gracefully.
  3. Controllers must support dual responses: return JSON `['success' => true]` for AJAX, and traditional POST redirect with flash for non-AJAX.
  4. Register GET fallback routes in `public/index.php` for `/resource/{id}/delete` that redirect to `/resource` to prevent 404s on browser reloads.

### 22. Rate Limiting & Auth Lockout Prevention
- **The Issue:** Failed login attempts accumulating indefinitely across multiple days can cause users to hit HTTP 429 "Too Many Requests" prematurely.
- **The Solution:**
  1. In `RateLimiter::check()`, always check elapsed time against `updated_at` (sliding time window) rather than letting old attempts persist indefinitely.
  2. Always call `RateLimiter::reset('/login')` upon successful login and 2FA authentication in `AuthController`.

### 23. YouTube-Style Top Preloader Progress Bar (CRITICAL UX STANDARD)
- **The Issue:** In multi-page applications, clicking links or navigating between pages without immediate visual feedback makes the app feel sluggish or unresponsive.
- **The Rule:** All layouts and page navigation flows MUST maintain the YouTube-style top preloader progress bar.
- **The Solution:**
  1. Always include `<?php require __DIR__ . '/partials/progress_bar.php'; ?>` right before `</body>` in all layout files (`app/Views/layouts/*.php`) to avoid parser-blocking DOM execution.
  2. The component automatically intercepts internal `<a>` clicks, form submissions, and non-polling Axios AJAX calls.
  3. Fully compliant with Chromium Paint-Holding: avoids `beforeunload` style changes to eliminate white screen flashes during navigation.

### 24. Sticky Side Navigation & Tabs on Scroll (LAYOUT CONVENTION)
- **The Issue:** On long forms or multi-tab settings screens, scrolling down causes the navigation sidebar/tabs to disappear off-screen, forcing users to scroll all the way back to the top to switch tabs.
- **The Rule:** In multi-column layouts with side navigation or tabs (e.g. `settings/index.php`), the side navigation MUST remain sticky during scrolling on desktop screens.
- **The Solution:**
  1. Add `items-start` to the parent Grid container (e.g. `<div class="grid grid-cols-1 lg:grid-cols-4 gap-6 items-start">`) to cancel CSS grid's default `stretch`.
  2. Add `lg:sticky lg:top-6 self-start z-10` to the sidebar/tab column so it locks comfortably 24px below the top navbar during scrolling on desktop (`lg:`), while stacking normally on mobile.

### 25. Rule 1: Mandatory Rules Inspection First (อ่านกฎทุกครั้งก่อนเริ่มทำงาน)
- **The Issue:** Modifying code or proposing architectural changes without reviewing existing standards leads to regression, broken patterns, inconsistent UX, or security holes.
- **The Rule:** Before beginning ANY task — analyzing, designing, planning, or writing code — the AI agent and developers MUST inspect `rules.md` and `GEMINI.md` without exception.
- **The Solution:** Always verify compliance with the project's core guidelines, quick checklist, encoding standards, and security requirements before and after modifying files.

### 26. Complete Bilingual Localization (ใส่ภาษาให้ครบ 100% ทั้ง TH/EN)
- **The Issue:** Hardcoding text strings or leaving one language translation missing results in broken UI, fallback errors, or inconsistent localization.
- **The Rule:** Never hardcode text strings in views, templates, modals, buttons, table headers, JavaScript, flash messages, or controllers. All text must use `__('namespace.key')`.
- **The Solution:**
  1. Whenever creating any new feature or module, define translation keys in BOTH `app/Lang/th/{file}.php` and `app/Lang/en/{file}.php` simultaneously.
  2. Enforce 1:1 symmetry (0 missing keys) between Thai and English dictionaries.
  3. Run `php compare_lang.php` to verify parity (must return empty array `[]`).
  4. Run `php sync_lang.php` to synchronize new keys into the database cache/system.

### 27. Button Preloading & Double-Submit Prevention (สถานะ Preloading ของปุ่ม)
- **The Issue:** Submitting forms or triggering AJAX actions without disabling buttons or providing immediate visual feedback leads to double submissions, duplicate database records, or user confusion.
- **The Rule:** Every action and form submission button MUST have an immediate loading state upon click.
- **The Solution:**
  1. Immediately disable the button (`btn.disabled = true;`).
  2. Add an animated spinning icon (`<i class="ph ph-circle-notch animate-spin mr-2"></i>` or `<i class="ph ph-spinner animate-spin mr-2"></i>`).
  3. Display a localized loading message (e.g. `__('common.saving')`, `__('common.please_wait')`).
  4. Restore normal button state on AJAX completion or error.

### 28. SweetAlert2 Toast Notification Standard: Top-Center (แจ้งเตือน SweetAlert2 Toast ตำแหน่งบนกลาง)
- **The Issue:** Inconsistent alert positions (e.g. bottom-right, top-end) or using native browser `alert()` disrupts the user experience and breaks dark mode styling.
- **The Rule:** All lightweight Toast notifications must strictly be positioned at top-center (`position: 'top'`). Never use native browser dialogs (`alert()`, `confirm()`).
- **The Solution:**
  1. Configure SweetAlert2 toast with:
     ```javascript
     const Toast = Swal.mixin({
         toast: true,
         position: 'top',
         showConfirmButton: false,
         timer: 3000,
         timerProgressBar: true,
         didOpen: (toast) => {
             toast.addEventListener('mouseenter', Swal.stopTimer);
             toast.addEventListener('mouseleave', Swal.resumeTimer);
         }
     });
     ```
  2. Ensure dark mode styles (`background`, `color`) match the application theme.
  3. Dangerous or irreversible actions (such as delete) must use standard centered modal dialogs (`Swal.fire({ icon: 'warning', showCancelButton: true, ... })`), never Toast.

### 29. Social Login & Inline Vector SVGs (CRITICAL UI & ASSET STANDARD)
- **The Issue:** Relying on external image hotlinks (such as Wikimedia Commons or remote CDNs) for brand logos can lead to broken images, SSL revocation errors (e.g. `CRYPT_E_REVOCATION_OFFLINE`), slow loading, or adblocker restrictions.
- **The Rule:** NEVER load brand logos (Google, LINE, Facebook, GitHub, etc.) from external hotlinks. Always embed clean inline vector SVGs or serve verified local assets.
- **The Architecture:**
  1. The system natively supports 4 social providers: Google, LINE, Facebook, and GitHub.
  2. Provider credentials (Client ID, Secret, Redirect URI, Enabled toggle) are configured in `/settings/social` and managed in `system_settings`.
  3. OAuth callback URLs follow clean convention: `/auth/{provider}/callback`.
  4. Account linking/unlinking in `/profile` must prompt confirmation using SweetAlert2 dialogs, never native browser `confirm()`.

### 30. Copy to Clipboard Standard & Top-Center Feedback (UX CONVENTION)
- **The Issue:** Copying text/tokens without clear visual and toast feedback confuses users and leaves them unsure if the copy succeeded.
- **The Rule:** All copy-to-clipboard interactions must provide dual feedback (visual icon feedback + top-center SweetAlert2 toast).
- **The Solution:**
  1. Temporarily replace the copy icon with a green checkmark (`ph-check text-green-500`) for 2 seconds.
  2. Fire a SweetAlert2 Toast at `position: 'top'` with localized text (`__('settings.copied_to_clipboard')`).
  3. Support non-secure HTTP / Localhost contexts with graceful fallback (`document.execCommand('copy')`).





