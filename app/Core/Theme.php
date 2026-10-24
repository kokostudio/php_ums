<?php

namespace App\Core;

/**
 * Theme — ตัวกลางรวมธีมสีของระบบ (Sidebar + Primary Palette)
 *
 * รองรับ 2 รูปแบบ:
 *  - Preset : ใช้ชุดสีที่กำหนดไว้ล่วงหน้า (slate, black, indigo, emerald, rose)
 *  - Custom : ใช้สีที่ผู้ใช้เลือกเองจาก Color Picker แล้วสร้าง Palette อัตโนมัติ
 *
 * ทุก Layout ต้องใช้ Theme::resolve() เพื่อให้สี Sidebar / Primary ตรงกันทั้งระบบ
 */
class Theme
{
    /**
     * ชุดสีสำเร็จรูป (ค่าต่าง ๆ เรียงจาก 50 = อ่อนที่สุด ไป 950 = เข้มที่สุด)
     */
    public const PRESETS = [
        'slate' => [
            'label'      => 'Slate Dark',
            'background' => '#0F172A',
            'border'     => '#1E293B',
            'footer'     => '#020617',
            'accent'     => '#64748B',
            'palette'    => [
                50 => '#F8FAFC', 100 => '#F1F5F9', 200 => '#E2E8F0', 300 => '#CBD5E1',
                400 => '#94A3B8', 500 => '#64748B', 600 => '#475569', 700 => '#334155',
                800 => '#1E293B', 900 => '#0F172A', 950 => '#020617',
            ],
        ],
        'black' => [
            'label'      => 'Midnight',
            'background' => '#0A0A0A',
            'border'     => '#171717',
            'footer'     => '#060606',
            'accent'     => '#737373',
            'palette'    => [
                50 => '#FAFAFA', 100 => '#F5F5F5', 200 => '#E5E5E5', 300 => '#D4D4D4',
                400 => '#A3A3A3', 500 => '#737373', 600 => '#525252', 700 => '#404040',
                800 => '#262626', 900 => '#171717', 950 => '#0A0A0A',
            ],
        ],
        'indigo' => [
            'label'      => 'Deep Indigo',
            'background' => '#1E1B4B',
            'border'     => '#312E81',
            'footer'     => '#1E1B4B',
            'accent'     => '#6366F1',
            'palette'    => [
                50 => '#EEF2FF', 100 => '#E0E7FF', 200 => '#C7D2FE', 300 => '#A5B4FC',
                400 => '#818CF8', 500 => '#6366F1', 600 => '#4F46E5', 700 => '#4338CA',
                800 => '#3730A3', 900 => '#312E81', 950 => '#1E1B4B',
            ],
        ],
        'emerald' => [
            'label'      => 'Emerald',
            'background' => '#022C22',
            'border'     => '#064E3B',
            'footer'     => '#022C22',
            'accent'     => '#10B981',
            'palette'    => [
                50 => '#ECFDF5', 100 => '#D1FAE5', 200 => '#A7F3D0', 300 => '#6EE7B7',
                400 => '#34D399', 500 => '#10B981', 600 => '#059669', 700 => '#047857',
                800 => '#065F46', 900 => '#064E3B', 950 => '#022C22',
            ],
        ],
        'rose' => [
            'label'      => 'Rose Crimson',
            'background' => '#4C0519',
            'border'     => '#881337',
            'footer'     => '#4C0519',
            'accent'     => '#F43F5E',
            'palette'    => [
                50 => '#FFF1F2', 100 => '#FFE4E6', 200 => '#FECDD3', 300 => '#FDA4AF',
                400 => '#FB7185', 500 => '#F43F5E', 600 => '#E11D48', 700 => '#BE123C',
                800 => '#9F1239', 900 => '#881337', 950 => '#4C0519',
            ],
        ],
    ];

    /**
     * สีเริ่มต้นสำหรับ Custom Mode
     */
    public const DEFAULT_CUSTOM_COLOR = '#1E1B4B';

    /**
     * สร้างชุดสี Scrollbar ให้เข้ากับสี Sidebar ที่ผู้ใช้ตั้งค่า
     *
     * Scrollbar ปรากฏบนพื้นผิว 2 แบบ จึงต้องปรับความเข้มอ่อนต่างกัน:
     *  1. แถบเมนู Sidebar  → พื้นหลังคือสี Sidebar ที่ผู้ใช้เลือกเอง
     *  2. พื้นที่เนื้อหา    → โหมดสว่าง (#f8fafc) และโหมดมืด (#0f172a)
     *
     * สีทั้งหมดยึด "เฉดสี (Hue)" เดียวกับสี Sidebar ที่ผู้ใช้เลือก
     * ทำให้ Scrollbar กลมกลืนกับธีมเดียวกันทั้งระบบ
     *
     * @return array{
     *     thumb:string, thumbHover:string, thumbDark:string, thumbDarkHover:string,
     *     sidebarThumb:string, sidebarHover:string, track:string
     * }
     */
    public static function buildScrollbarColors(string $sidebarHex, string $accentHex): array
    {
        $sidebar = ThemeColor::normalize($sidebarHex) ?? '#0f172a';
        $accent  = ThemeColor::normalize($accentHex) ?? '#64748b';

        // ── โหมดสว่าง: พื้นหลังเนื้อหา #f8fafc → Scrollbar ต้องเข้มลง ──
        $thumb      = ThemeColor::ensureContrast(ThemeColor::mix($sidebar, '#000000', 0.30), '#f8fafc', 1.6);
        $thumbHover = ThemeColor::ensureContrast(ThemeColor::mix($sidebar, '#000000', 0.50), '#f8fafc', 3.0);

        // ── โหมดมืด: พื้นหลังเนื้อหา #0f172a → Scrollbar ต้องสว่างขึ้น ──
        $thumbDark      = ThemeColor::ensureContrast(ThemeColor::mix($accent, '#FFFFFF', 0.30), '#0f172a', 1.6);
        $thumbDarkHover = ThemeColor::ensureContrast(ThemeColor::mix($accent, '#FFFFFF', 0.55), '#0f172a', 3.0);

        // ── แถบเลื่อนภายใน Sidebar: พื้นหลังคือสี Sidebar → ต้องสว่างขึ้นเสมอ ──
        $sidebarThumb = ThemeColor::ensureContrast(ThemeColor::mix($sidebar, '#FFFFFF', 0.30), $sidebar, 1.6);
        $sidebarHover = ThemeColor::ensureContrast(ThemeColor::mix($sidebar, '#FFFFFF', 0.50), $sidebar, 3.0);

        return [
            'thumb'          => $thumb,
            'thumbHover'     => $thumbHover,
            'thumbDark'      => $thumbDark,
            'thumbDarkHover' => $thumbDarkHover,
            'sidebarThumb'   => $sidebarThumb,
            'sidebarHover'   => $sidebarHover,
            'track'          => 'transparent',
        ];
    }

    /**
     * รวบรวมค่าธีมทั้งหมดที่ Layout ต้องใช้
     *
     * @return array{
     *     mode:string, preset:string, label:string, background:string, border:string,
     *     footer:string, accent:string, palette:array<int,string>, sidebarRgb:string,
     *     primaryHex:string, textColor:string, scrollbar:array<string,string>
     * }
     */
    public static function resolve(): array
    {
        $themeKey = (string) Settings::get('sidebar_theme', 'slate');

        if ($themeKey === 'custom') {
            return self::resolveCustom((string) Settings::get('sidebar_custom_color', self::DEFAULT_CUSTOM_COLOR));
        }

        $preset = self::PRESETS[$themeKey] ?? self::PRESETS['slate'];

        return [
            'mode'       => 'preset',
            'preset'     => $themeKey,
            'label'      => $preset['label'],
            'background' => $preset['background'],
            'border'     => $preset['border'],
            'footer'     => $preset['footer'],
            'accent'     => $preset['accent'],
            'palette'    => $preset['palette'],
            'sidebarRgb' => self::toRgbChannels($preset['background']),
            'primaryHex' => $preset['palette'][500],
            'textColor'  => ThemeColor::contrastColor($preset['background']),
            'scrollbar'  => self::buildScrollbarColors($preset['background'], $preset['palette'][500]),
        ];
    }

    /**
     * สร้างธีมจากสีที่ผู้ใช้เลือกเองผ่าน Color Picker
     */
    private static function resolveCustom(string $customColor): array
    {
        // บังคับให้พื้นหลังอ่านได้ชัด (แถบเมนูใช้ตัวอักษรสีขาว)
        $background = ThemeColor::enforceReadableBackground($customColor);
        $colors     = ThemeColor::buildSidebarColors($background);
        $accent     = ThemeColor::deriveAccent($background);
        $palette    = ThemeColor::generatePalette($accent, $background);

        return [
            'mode'       => 'custom',
            'preset'     => 'custom',
            'label'      => $background,
            'background' => $background,
            'border'     => $colors['border'],
            'footer'     => $colors['footer'],
            'accent'     => $accent,
            'palette'    => $palette,
            'sidebarRgb' => self::toRgbChannels($background),
            'primaryHex' => $palette[500],
            'textColor'  => ThemeColor::contrastColor($background),
            'scrollbar'  => self::buildScrollbarColors($background, $palette[500]),
        ];
    }

    /**
     * แปลง HEX เป็นช่อง RGB (เช่น "15 23 42") สำหรับใช้กับ CSS Variable
     */
    public static function toRgbChannels(string $hex): string
    {
        [$r, $g, $b] = ThemeColor::hexToRgb($hex);

        return "{$r} {$g} {$b}";
    }

    /**
     * สร้างชุดสีสำหรับ Tailwind config ในรูปแบบที่รองรับ opacity modifier
     * (เช่น bg-primary-900/50, shadow-primary-500/30) อย่างถูกต้อง
     *
     * @param array|null $theme
     * @return array<int,string>
     */
    public static function paletteForTailwind(?array $theme = null): array
    {
        $theme = $theme ?? self::resolve();
        $out   = [];

        foreach ($theme['palette'] as $shade => $hex) {
            $out[$shade] = 'rgb(' . self::toRgbChannels($hex) . ' / <alpha-value>)';
        }

        // DEFAULT ให้ class แบบ text-primary / bg-primary ทำงานได้ด้วย
        $out['DEFAULT'] = $out[500] ?? $out[600];

        return $out;
    }

    /**
     * สร้าง CSS Variables สำหรับธีมปัจจุบัน
     *
     * @param array|null $theme ใช้ theme ที่คำนวณไว้แล้ว (ถ้าไม่ส่งจะ resolve ใหม่)
     */
    public static function cssVariables(?array $theme = null): string
    {
        $theme = $theme ?? self::resolve();

        $vars = [
            '--sidebar-bg'     => $theme['background'],
            '--sidebar-border' => $theme['border'],
            '--sidebar-footer' => $theme['footer'],
            '--sidebar-accent' => $theme['accent'],
            '--sidebar-rgb'    => $theme['sidebarRgb'],
            '--sidebar-text'   => $theme['textColor'],
            '--primary-hex'    => $theme['primaryHex'],
        ];

        // สี Scrollbar (ผูกกับสี Sidebar ที่ตั้งค่าไว้)
        $vars['--scrollbar-thumb']            = $theme['scrollbar']['thumb'];
        $vars['--scrollbar-thumb-hover']      = $theme['scrollbar']['thumbHover'];
        $vars['--scrollbar-thumb-dark']       = $theme['scrollbar']['thumbDark'];
        $vars['--scrollbar-thumb-dark-hover'] = $theme['scrollbar']['thumbDarkHover'];
        $vars['--scrollbar-sidebar-thumb']   = $theme['scrollbar']['sidebarThumb'];
        $vars['--scrollbar-sidebar-hover']   = $theme['scrollbar']['sidebarHover'];
        $vars['--scrollbar-track']            = $theme['scrollbar']['track'];

        foreach ($theme['palette'] as $shade => $hex) {
            $vars["--primary-{$shade}"] = self::toRgbChannels($hex);
        }

        $declarations = [];
        foreach ($vars as $name => $value) {
            $declarations[] = "{$name}: {$value};";
        }

        return ':root {' . implode(' ', $declarations) . '}';
    }

    /**
     * รายการคีย์ธีมที่อนุญาตให้บันทึกจากฟอร์ม
     *
     * @return array<int,string>
     */
    public static function allowedThemeKeys(): array
    {
        return array_merge(array_keys(self::PRESETS), ['custom']);
    }
}
