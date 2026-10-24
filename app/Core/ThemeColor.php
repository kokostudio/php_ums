<?php

namespace App\Core;

/**
 * ThemeColor — ตัวช่วยคำนวณสีธีม (Sidebar / Primary Palette)
 *
 * ระบบให้ผู้ใช้เลือกสี Sidebar ได้อิสระผ่าน Color Picker (HEX)
 * โดยคำนวณ "primary" scale (50–950) และสีประกอบที่เกี่ยวข้องโดยอัตโนมัติ
 * เพื่อให้ UI ทั้งระบบ (primary-*, bg-sidebar) สอดคล้องกับสีที่ผู้ใช้เลือก
 */
class ThemeColor
{
    /**
     * ความสว่างสูงสุดของพื้นหลัง Sidebar ที่ยังอ่านตัวอักษรสีขาวได้ชัด
     * (ค่าต่ำกว่านี้ = ตัวอักษรสีขาวจะอ่านยากเกินไป)
     */
    public const MAX_BACKGROUND_LIGHTNESS = 0.62;

    /**
     * อัตราส่วนคอนทราสต์ขั้นต่ำ (WCAG AA) ระหว่างตัวอักษรสีขาวกับพื้นหลัง Sidebar
     */
    public const MIN_TEXT_CONTRAST = 4.5;

    /**
     * ตรวจสอบว่าเป็น HEX สีที่ถูกต้องหรือไม่ (รองรับ #RGB และ #RRGGBB)
     */
    public static function isValid(string $hex): bool
    {
        return (bool) preg_match('/^#?([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/', trim($hex));
    }

    /**
     * Normalize HEX ให้อยู่ในรูปแบบ #rrggbb (ตัวพิมพ์เล็ก)
     * คืนค่า null เมื่อรูปแบบไม่ถูกต้อง
     */
    public static function normalize(string $hex): ?string
    {
        $hex = trim($hex);
        if (!self::isValid($hex)) {
            return null;
        }

        $hex = ltrim($hex, '#');
        if (strlen($hex) === 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }

        return '#' . strtolower($hex);
    }

    /**
     * แปลง HEX เป็น RGB array
     *
     * @return array{0:int,1:int,2:int}
     */
    public static function hexToRgb(string $hex): array
    {
        $hex = self::normalize($hex) ?? '#000000';

        return [
            (int) hexdec(substr($hex, 1, 2)),
            (int) hexdec(substr($hex, 3, 2)),
            (int) hexdec(substr($hex, 5, 2)),
        ];
    }

    /**
     * แปลง RGB array เป็น HEX
     *
     * @param array{0:int|float,1:int|float,2:int|float} $rgb
     */
    public static function rgbToHex(array $rgb): string
    {
        return sprintf(
            '#%02x%02x%02x',
            (int) round(max(0.0, min(255.0, (float) $rgb[0]))),
            (int) round(max(0.0, min(255.0, (float) $rgb[1]))),
            (int) round(max(0.0, min(255.0, (float) $rgb[2])))
        );
    }

    /**
     * แปลง HEX เป็น HSL array [h:0-360, s:0-1, l:0-1]
     *
     * @return array{0:float,1:float,2:float}
     */
    public static function hexToHsl(string $hex): array
    {
        [$r, $g, $b] = self::hexToRgb($hex);
        $r /= 255;
        $g /= 255;
        $b /= 255;

        $max = max($r, $g, $b);
        $min = min($r, $g, $b);
        $l   = ($max + $min) / 2;
        $d   = $max - $min;

        if ($d == 0.0) {
            return [0.0, 0.0, $l];
        }

        $s = $l > 0.5 ? $d / (2.0 - $max - $min) : $d / ($max + $min);

        if ($max == $r) {
            $h = fmod((($g - $b) / $d) * 60.0, 360.0);
        } elseif ($max == $g) {
            $h = (($b - $r) / $d) * 60.0 + 120.0;
        } else {
            $h = (($r - $g) / $d) * 60.0 + 240.0;
        }

        if ($h < 0) {
            $h += 360.0;
        }

        return [$h, $s, $l];
    }

    /**
     * แปลง HSL เป็น HEX
     */
    public static function hslToHex(float $h, float $s, float $l): string
    {
        $h = fmod(fmod($h, 360.0) + 360.0, 360.0);
        $s = max(0.0, min(1.0, $s));
        $l = max(0.0, min(1.0, $l));

        $c = (1 - abs(2 * $l - 1)) * $s;
        $x = $c * (1 - abs(fmod($h / 60.0, 2.0) - 1));
        $m = $l - $c / 2;

        if ($h < 60) {
            [$r, $g, $b] = [$c, $x, 0.0];
        } elseif ($h < 120) {
            [$r, $g, $b] = [$x, $c, 0.0];
        } elseif ($h < 180) {
            [$r, $g, $b] = [0.0, $c, $x];
        } elseif ($h < 240) {
            [$r, $g, $b] = [0.0, $x, $c];
        } elseif ($h < 300) {
            [$r, $g, $b] = [$x, 0.0, $c];
        } else {
            [$r, $g, $b] = [$c, 0.0, $x];
        }

        return self::rgbToHex([
            (int) round(($r + $m) * 255),
            (int) round(($g + $m) * 255),
            (int) round(($b + $m) * 255),
        ]);
    }

    /**
     * ผสมสี HEX สองสีเข้าด้วยกัน (t = สัดส่วนของสีที่สอง, 0–1)
     */
    public static function mix(string $hexA, string $hexB, float $t): string
    {
        $t = max(0.0, min(1.0, $t));
        [$r1, $g1, $b1] = self::hexToRgb($hexA);
        [$r2, $g2, $b2] = self::hexToRgb($hexB);

        return self::rgbToHex([
            (int) round($r1 + ($r2 - $r1) * $t),
            (int) round($g1 + ($g2 - $g1) * $t),
            (int) round($b1 + ($b2 - $b1) * $t),
        ]);
    }

    /**
     * ปรับความสว่างของสี (amount > 0 = สว่างขึ้น, < 0 = เข้มลง)
     */
    public static function shade(string $hex, float $amount): string
    {
        if ($amount >= 0) {
            return self::mix($hex, '#ffffff', $amount);
        }

        return self::mix($hex, '#000000', abs($amount));
    }

    /**
     * คำนวณความสว่างสัมพัทธ์ตามมาตรฐาน WCAG (0 = มืดสนิท, 1 = ขาวสนิท)
     */
    public static function relativeLuminance(string $hex): float
    {
        [$r, $g, $b] = self::hexToRgb($hex);

        $channels = [];
        foreach ([$r, $g, $b] as $value) {
            $c = $value / 255;
            $channels[] = $c <= 0.03928 ? $c / 12.92 : pow(($c + 0.055) / 1.055, 2.4);
        }

        return 0.2126 * $channels[0] + 0.7152 * $channels[1] + 0.0722 * $channels[2];
    }

    /**
     * คำนวณอัตราส่วนคอนทราสต์ (WCAG) ระหว่างสีสองสี
     */
    public static function contrastRatio(string $hexA, string $hexB): float
    {
        $l1 = self::relativeLuminance($hexA);
        $l2 = self::relativeLuminance($hexB);

        $lighter = max($l1, $l2);
        $darker  = min($l1, $l2);

        return ($lighter + 0.05) / ($darker + 0.05);
    }

    /**
     * เลือกสีตัวอักษรที่อ่านได้ชัดบนพื้นหลังที่กำหนด
     * (คืนค่า HEX สีขาวหรือสีเข้มตามความสว่างของพื้นหลัง)
     */
    public static function contrastColor(string $background): string
    {
        [, , $l] = self::hexToHsl($background);

        return $l > 0.58 ? '#0f172a' : '#ffffff';
    }

    /**
     * บังคับให้พื้นหลัง Sidebar อ่านได้ชัดเจน
     *
     * แถบเมนูใช้ตัวอักษรสีขาวเป็นหลัก หากผู้ใช้เลือกสีที่สว่างเกินไป
     * ระบบจะปรับความสว่างลง (โดยคง Hue และ Saturation เดิมไว้) จนกว่า
     * ตัวอักษรสีขาวจะผ่านเกณฑ์คอนทราสต์ WCAG AA
     */
    public static function enforceReadableBackground(string $hex): string
    {
        $normalized = self::normalize($hex);
        if ($normalized === null) {
            return '#0f172a';
        }

        if (self::contrastRatio($normalized, '#ffffff') >= self::MIN_TEXT_CONTRAST) {
            return $normalized;
        }

        // ไล่ความสว่างลงจนกว่าจะผ่านเกณฑ์ (คง Hue/Saturation เดิม)
        [$h, $s, $l] = self::hexToHsl($normalized);
        for ($step = 0; $step < 100; $step++) {
            $l -= 0.01;
            if ($l <= 0.02) {
                break;
            }
            $candidate = self::hslToHex($h, $s, $l);
            if (self::contrastRatio($candidate, '#ffffff') >= self::MIN_TEXT_CONTRAST) {
                return $candidate;
            }
        }

        return '#000000';
    }

    /**
     * หาสี Accent (primary-500) ที่เหมาะสมกับพื้นหลัง Sidebar
     * โดยคง Hue ของสีที่ผู้ใช้เลือกไว้ และปรับ Saturation/Lightness
     * ให้ตัดกับพื้นหลังได้ชัดเจน
     */
    public static function deriveAccent(string $background): string
    {
        [$h, $s, $l] = self::hexToHsl($background);

        // สีเทาสนิท (s น้อย) ให้เพิ่มความอิ่มสีเล็กน้อยเพื่อให้มีชีวิต
        $s = $s < 0.12 ? 0.30 : min(0.95, $s * 1.08);

        // พื้นหลังเข้ม → accent ต้องสว่างขึ้นเพื่อให้เห็นชัด
        $accentL = max(0.42, min(0.74, 0.62 - $l * 0.45));

        return self::hslToHex($h, $s, $accentL);
    }

    /**
     * สร้าง primary scale (50–950) จากสี Accent โดยยึดพื้นหลัง Sidebar เป็นจุดอ้างอิง
     * โครงสร้างเดียวกับ Tailwind color scale เพื่อให้ใช้ class primary-* ได้ทันที
     *
     * เกณฑ์สำคัญ: ค่าความสว่างต้องไล่จากอ่อน (50) ไปเข้ม (950) แบบสม่ำเสมอ
     * เพื่อให้ชั้นสีถูกต้องตามหลักของ Tailwind และใช้งานกับ overlay ได้ถูกต้อง
     *
     * @return array<int,string>
     */
    public static function generatePalette(string $accent, string $background): array
    {
        $accent = self::normalize($accent) ?? '#64748b';
        $bg     = self::normalize($background) ?? '#0f172a';

        [$ah, $as, $al] = self::hexToHsl($accent);
        [$bh, $bs, $bl] = self::hexToHsl($bg);

        // ความสว่างของเฉดกลาง (500) ถูกจำกัดให้อยู่ในช่วงที่อ่านได้ชัด
        $l500 = max(0.32, min(0.66, $al));

        // เฉดสว่าง (50–400) ไล่ความอ่อนลง โดยลดความอิ่มสีลงเล็กน้อย
        $lightScale = [
            50  => [0.975, 0.22],
            100 => [0.940, 0.32],
            200 => [0.875, 0.48],
            300 => [0.790, 0.68],
            400 => [0.680, 0.90],
        ];

        $palette = [];
        foreach ($lightScale as $step => [$light, $satFactor]) {
            $palette[$step] = self::hslToHex($ah, $as * $satFactor, $light);
        }

        $palette[500] = self::hslToHex($ah, $as, $l500);
        $palette[600] = self::hslToHex($ah, $as, $l500 * 0.84);
        $palette[700] = self::hslToHex($ah, $as, $l500 * 0.66);

        // เฉดเข้ม (800–950) ยึดโทนสีของพื้นหลัง Sidebar เพื่อให้ซ้อนทับกันได้กลมกลืน
        $l700 = $l500 * 0.66;
        $l800 = min($l700 * 0.80, $bl + 0.11);
        $l900 = min($l800 * 0.78, $bl + 0.04);
        $l950 = min($l900 * 0.68, $bl * 0.45);

        $palette[800] = self::hslToHex($bh, $bs, max(0.06, $l800));
        $palette[900] = self::hslToHex($bh, $bs, max(0.04, $l900));
        $palette[950] = self::hslToHex($bh, $bs, max(0.02, $l950));

        return $palette;
    }

    /**
     * ปรับสีให้มีคอนทราสต์กับพื้นหลังอย่างน้อยตามที่กำหนด
     *
     * ใช้การค้นหาแบบไบนารีโดยผสมสีเข้าหา "ขาว" (ถ้าพื้นหลังเข้ม)
     * หรือ "ดำ" (ถ้าพื้นหลังสว่าง) จนกว่าจะผ่านเกณฑ์คอนทราสต์
     *
     * @param string $hex       สีต้นทาง
     * @param string $background สีพื้นหลังที่ต้องอ่านได้ชัด
     * @param float  $minRatio  อัตราส่วนคอนทราสต์ขั้นต่ำ (WCAG)
     */
    public static function ensureContrast(string $hex, string $background, float $minRatio = 1.5): string
    {
        $color = self::normalize($hex) ?? '#64748b';
        $bg    = self::normalize($background) ?? '#0f172a';

        if (self::contrastRatio($color, $bg) >= $minRatio) {
            return $color;
        }

        // เลือกปลายทาง: ขาวถ้าพื้นหลังเข้ม, ดำถ้าพื้นหลังสว่าง
        $target = self::relativeLuminance($bg) > 0.35 ? '#000000' : '#FFFFFF';

        $low  = 0.0;
        $high = 1.0;
        $best = $target;

        for ($i = 0; $i < 20; $i++) {
            $mid  = ($low + $high) / 2;
            $test = self::mix($color, $target, $mid);

            if (self::contrastRatio($test, $bg) >= $minRatio) {
                $best = $test;
                $high = $mid;
            } else {
                $low = $mid;
            }
        }

        return $best;
    }

    /**
     * สร้างชุดสี Sidebar (พื้นหลัง/เส้นขอบ/ส่วนล่าง) จากสีที่ผู้ใช้เลือก
     *
     * @return array{bg:string,border:string,footer:string}
     */
    public static function buildSidebarColors(string $hex): array
    {
        $base = self::normalize($hex) ?? '#0f172a';

        return [
            'bg'     => $base,
            'border' => self::mix($base, '#FFFFFF', 0.12),
            'footer' => self::mix($base, '#000000', 0.25),
        ];
    }
}
