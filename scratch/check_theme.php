<?php
require __DIR__ . '/../vendor/autoload.php';

use App\Core\Config;
use App\Core\Theme;
use App\Core\ThemeColor;

Config::load(__DIR__ . '/../.env');

$colors = ['#1E1B4B', '#022C22', '#4C0519', '#0F172A', '#7C3AED', '#DC2626', '#0891B2', '#F59E0B', '#FFFFFF', '#F1F5F9', '#000000'];

$fail = 0;

foreach ($colors as $color) {
    $bg   = ThemeColor::enforceReadableBackground($color);
    $accent = ThemeColor::deriveAccent($bg);
    $p    = ThemeColor::generatePalette($accent, $bg);

    // ตรวจสอบความสว่างไล่จากอ่อนไปเข้มอย่างสม่ำเสมอ
    $lightness = [];
    foreach ([50, 100, 200, 300, 400, 500, 600, 700, 800, 900, 950] as $step) {
        [$h, $s, $l] = ThemeColor::hexToHsl($p[$step]);
        $lightness[$step] = $l;
    }

    $monotonic = true;
    $prev = $lightness[50];
    foreach ($lightness as $step => $l) {
        if ($l > $prev + 0.0001) { $monotonic = false; }
        $prev = $l;
    }

    // ตรวจสอบความอ่านได้ของตัวอักษรสีขาวบนพื้นหลัง (WCAG AA)
    $contrast = ThemeColor::contrastColor($bg);
    $ratio    = ThemeColor::contrastRatio($bg, '#ffffff');
    $readable = $contrast === '#ffffff' && $ratio >= ThemeColor::MIN_TEXT_CONTRAST;

    $status = ($monotonic && $readable) ? 'OK  ' : 'FAIL';
    if (!($monotonic && $readable)) { $fail++; }

    echo "[{$status}] pick=" . str_pad($color, 10) . " bg=" . str_pad($bg, 10)
        . " accent=" . str_pad($accent, 10)
        . " 500=" . str_pad($p[500], 10)
        . " monotonic=" . ($monotonic ? 'y' : 'n')
        . " contrast=" . str_pad(number_format($ratio, 2), 6)
        . " readable=" . ($readable ? 'y' : 'n') . "\n";
    echo "        shades: ";
    foreach ([50, 100, 200, 300, 400, 500, 600, 700, 800, 900, 950] as $step) {
        echo $step . '=' . $p[$step] . ' ';
    }
    echo "\n";
}

echo $fail === 0 ? "\nAll checks passed.\n" : "\n{$fail} check(s) FAILED.\n";
exit($fail === 0 ? 0 : 1);
