<?php

declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;

/**
 * กฎข้อที่ 3 / Rule 16: ภาษาไทย-อังกฤษต้องสมมาตร 1:1 ครบ 100% (0 missing keys)
 * (mirror ของ php cli/compare_lang.php สำหรับรันใน CI อัตโนมัติ)
 */
final class LanguageParityTest extends TestCase
{
    private const LANG_FILES = [
        'auth.php', 'common.php', 'dashboard.php', 'logs.php', 'messages.php',
        'profile.php', 'roles.php', 'settings.php', 'users.php',
    ];

    public function testThEnKeysAreSymmetric(): void
    {
        $problems = [];

        foreach (self::LANG_FILES as $file) {
            $en = require dirname(__DIR__) . '/app/Lang/en/' . $file;
            $th = require dirname(__DIR__) . '/app/Lang/th/' . $file;

            $missingInEn = array_values(array_diff(array_keys($th), array_keys($en)));
            $missingInTh = array_values(array_diff(array_keys($en), array_keys($th)));

            if ($missingInEn !== []) {
                $problems[] = "{$file} missing in EN: " . implode(', ', $missingInEn);
            }
            if ($missingInTh !== []) {
                $problems[] = "{$file} missing in TH: " . implode(', ', $missingInTh);
            }
        }

        $this->assertSame([], $problems, 'Language parity check failed (Rule 3): ' . implode('; ', $problems));
    }
}
