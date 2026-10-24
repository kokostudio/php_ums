<?php
/**
 * ตรวจสอบสี Scrollbar ที่ผูกกับสี Sidebar
 *
 * พื้นผิวที่ Scrollbar ปรากฏ:
 *  1. แถบเมนู Sidebar  → พื้นหลัง = สี Sidebar ที่ผู้ใช้เลือก
 *  2. พื้นที่เนื้อหา    → โหมดสว่าง #f8fafc / โหมดมืด #0f172a
 */
require __DIR__ . '/../vendor/autoload.php';

use App\Core\Config;
use App\Core\Theme;
use App\Core\ThemeColor;

Config::load(__DIR__ . '/../.env');

const LIGHT_BG = '#f8fafc';
const DARK_BG  = '#0f172a';

$fail = 0;

$cases = [];
foreach (Theme::PRESETS as $key => $preset) {
    $cases[$key] = [$preset['background'], $preset['palette'][500]];
}
// สี Custom ที่ผู้ใช้เลือกได้จริง (สว่าง/มืด/สด/ขาว)
$cases['custom-violet'] = ['#7c3aed', '#520bcb'];
$cases['custom-teal']   = ['#0891b2', '#06c0ec'];
$cases['custom-amber']  = ['#a36907', '#ea9506'];
$cases['custom-green']  = ['#109437', '#22c55e'];
$cases['custom-white']  = ['#f8fafc', '#0f172a'];

foreach ($cases as $label => [$sidebar, $accent]) {
    $sb = Theme::buildScrollbarColors($sidebar, $accent);

    // 1) Scrollbar บนพื้นหลัง Sidebar
    $rSide   = ThemeColor::contrastRatio($sb['sidebarThumb'], $sidebar);
    $rSideH  = ThemeColor::contrastRatio($sb['sidebarHover'], $sidebar);

    // 2) Scrollbar บนพื้นที่เนื้อหา — โหมดสว่าง
    $rLight  = ThemeColor::contrastRatio($sb['thumb'], LIGHT_BG);
    $rLightH = ThemeColor::contrastRatio($sb['thumbHover'], LIGHT_BG);

    // 3) Scrollbar บนพื้นที่เนื้อหา — โหมดมืด
    $rDark   = ThemeColor::contrastRatio($sb['thumbDark'], DARK_BG);
    $rDarkH  = ThemeColor::contrastRatio($sb['thumbDarkHover'], DARK_BG);

    $ok = $rSide >= 1.6 && $rSideH >= 3.0
        && $rLight >= 1.6 && $rLightH >= 3.0
        && $rDark >= 1.6 && $rDarkH >= 3.0;

    if (!$ok) { $fail++; }

    printf(
        "[%s] %-14s sidebar=%-9s | side x%.2f/x%.2f | light x%.2f/x%.2f | dark x%.2f/x%.2f\n",
        $ok ? 'PASS' : 'FAIL',
        $label,
        $sidebar,
        $rSide, $rSideH,
        $rLight, $rLightH,
        $rDark, $rDarkH
    );
}

// ตรวจรูปแบบสี
foreach ($cases as $label => [$sidebar, $accent]) {
    $sb = Theme::buildScrollbarColors($sidebar, $accent);
    foreach (['thumb', 'thumbHover', 'thumbDark', 'thumbDarkHover', 'sidebarThumb', 'sidebarHover'] as $key) {
        if (!preg_match('/^#[0-9a-f]{6}$/i', $sb[$key])) {
            echo "[FAIL] {$label}.{$key} format ไม่ถูกต้อง: {$sb[$key]}\n";
            $fail++;
        }
    }
    if ($sb['track'] !== 'transparent') {
        echo "[FAIL] {$label}.track ไม่ถูกต้อง: {$sb['track']}\n";
        $fail++;
    }
}

// ตรวจว่า CSS variables ครบทุกตัว
$theme = Theme::resolve();
$vars  = Theme::cssVariables($theme);
foreach ([
    '--scrollbar-thumb', '--scrollbar-thumb-hover', '--scrollbar-thumb-dark',
    '--scrollbar-thumb-dark-hover', '--scrollbar-sidebar-thumb', '--scrollbar-sidebar-hover',
    '--scrollbar-track',
] as $name) {
    if (strpos($vars, $name . ':') === false) {
        echo "[FAIL] CSS variable {$name} ไม่ครบ\n";
        $fail++;
    }
}

echo "\n=== CSS variables (ธีมปัจจุบันจาก DB) ===\n{$vars}\n";

echo $fail === 0 ? "\nAll scrollbar checks passed.\n" : "\n{$fail} check(s) FAILED.\n";
exit($fail === 0 ? 0 : 1);
