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

**ENCODING & MOJIBAKE PREVENTION**: This project uses Thai language heavily. NEVER use Windows PowerShell commands (like `Set-Content`, `Out-File`, `echo`) or inline PowerShell scripts to write or modify files containing Thai (multi-byte) characters. PowerShell can silently corrupt the encoding (causing mojibake/ภาษาต่างดาว). Always use the native `replace_file_content` tool, `write_to_file` tool, or write safe PHP scripts to handle file modifications.

**NO BAHT SYMBOL (฿) RULE**: Do NOT use the Thai Baht currency symbol `฿` anywhere in the project (UI, tables, mock data, charts, reports, translations, or code). Always omit it or use 'บาท' / 'THB' as specified by context. Remove all instances of `฿`.

**SIDEBAR VISIBILITY & DIRECT NAVIGATION**: Whenever developing or adding any new system/module, NEVER leave it hidden in collapsed menus or expect the user to type URLs in chat or the browser address bar. Always add it prominently and visibly to the sidebar navigation (`app/Views/layouts/app.php`), keeping it immediately accessible with clear direct links to all sub-pages, dashboards, and test/demo features.

**AJAX DELETE OPERATIONS & GET FALLBACKS**: NEVER use traditional HTML `form.submit()` for delete operations. Always use `axios.post` with SweetAlert2 (`Swal.fire`) confirmation dialogs (matching `users/index.php` and `roles/index.php`) so the browser does not navigate to `/delete` URLs or display raw JSON. Controllers must handle both AJAX (JSON response) and standard POST (redirect with flash). In `public/index.php`, always register a GET fallback redirect for `/resource/{id}/delete` back to the index view to prevent 404 errors if users reload or directly navigate.

**RATE LIMITING & 429 PREVENTION**: Always call `RateLimiter::reset('/login')` upon successful login and 2FA verification. `RateLimiter` must use a sliding time window (relative to `updated_at`) so failed attempts do not accumulate indefinitely and lock users out unexpectedly.

**YOUTUBE-STYLE PRELOADER PROGRESS BAR**: All layouts (`app/Views/layouts/*.php`) and page navigation flows must maintain the YouTube-style top preloader progress bar (`app/Views/layouts/partials/progress_bar.php`). Always include `<?php require __DIR__ . '/partials/progress_bar.php'; ?>` right before `</body>` in any layout (preserving Chromium Paint-Holding and eliminating white flashes). It automatically handles link clicks, form submissions, page transitions, and non-polling AJAX requests.

**STICKY SIDE NAVIGATION & TABS ON SCROLL**: In multi-column views with side navigation or tabs alongside long forms/content (such as `settings/index.php`), always ensure the sidebar/tab card is sticky on scroll (`lg:sticky lg:top-6 self-start z-10` with `items-start` on the parent grid container) so navigation stays immediately accessible without requiring users to scroll all the way back to the top.
<!-- rules:end -->
