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

### 5. DataTables (Server-Side Processing)

- Always use **server-side** processing for data tables (never client-side for large datasets)
- The `/data` endpoint returns JSON with `draw`, `recordsTotal`, `recordsFiltered`, `data`
- Encode `hash_id` in the data endpoint, never expose raw `id`
- Use `$_GET['search']['value']`, `$_GET['start']`, `$_GET['length']` for filtering/pagination

### 6. Form Conventions

- All forms include `<input type="hidden" name="csrf_token" value="<?= Session::getCsrfToken() ?>">`
- Backend always calls `Security::checkCsrf()` as the first line of POST handlers
- Use `Security::sanitize()` on all text inputs before storing
- File uploads: validate MIME type with `mime_content_type()`, store in `public/uploads/`

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

**Dark Mode:**
- Dark mode is class-based (`darkMode: 'class'` in Tailwind config)
- Preference stored in `localStorage.theme` (`'dark'` or `'light'`)
- All views must include `dark:` variants for **all** color classes
- Init script in layout prevents FOUC (Flash of Unstyled Content)

**Scrollbar:**
- Custom thin 6px scrollbar in both layouts
- Light: `slate-300` track thumb → hover `blue-500`
- Dark: `slate-700` track thumb → hover `blue-500`
- Firefox: `scrollbar-width: thin`

**Icons:** Phosphor Icons
- Outline: `<i class="ph ph-{name}"></i>`
- Filled: `<i class="ph-fill ph-{name}"></i>`

**Color System:**
- Primary: `#3b82f6` (blue-500)
- Secondary: `#64748b` (slate-500)
- ห้ามใช้สีนอก palette โดยไม่มีเหตุผล

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
- **Ultra Dark Mode:** Use translucent backgrounds for cards/modals in dark mode (e.g., `dark:bg-black/20 dark:backdrop-blur-md`).

### 11. Pinned Sidebar Menus (Favorites)
- **Feature:** The sidebar supports a "Pinned Menus" feature where users can pin frequently used submenus to the top.
- **UI:** A pin icon appears when hovering over a submenu item. Do NOT rely on Tailwind dynamic arbitrary classes (like `group-hover/menu`) for elements appended via JS, as Tailwind CDN may fail to parse them. Use standard CSS in `<style>` instead (e.g., `.submenu-item-link:hover .pin-menu-btn { opacity: 1; }`).
- **Event Bubbling:** Always use `event.stopPropagation()` on the pin button's `onclick` handler to prevent the browser from navigating to the parent link.
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
