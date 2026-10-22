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
   - **Verification & DB Sync**: Before completing work, run `php compare_lang.php` (must output `[]` with 0 missing keys) and run `php sync_lang.php` to synchronize translations to the MySQL `translations` table.

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
<!-- rules:end -->
