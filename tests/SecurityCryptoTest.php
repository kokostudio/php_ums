<?php

declare(strict_types=1);

namespace Tests;

use App\Core\Security;
use PHPUnit\Framework\TestCase;

final class SecurityCryptoTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        $_ENV['APP_KEY'] = 'testing_app_key_for_unit_tests_only';
    }

    public function testEncryptDecryptRoundTrip(): void
    {
        $token = Security::encrypt('session-12345');
        $this->assertSame('v2.', substr($token, 0, 3));
        $this->assertSame('session-12345', Security::decrypt($token));
    }

    public function testDecryptRejectsTamperedToken(): void
    {
        $token = Security::encrypt('session-12345');
        $tampered = $token === '' ? 'x' : substr($token, 0, -2) . dechex((int) hexdec(substr($token, -1)) ^ 1) . substr($token, -1);
        // GCM ต้องตรวจจับการแก้ไข ciphertext/tag ได้ (tamper-proof)
        $this->assertNotSame('session-12345', Security::decrypt($tampered));
    }

    public function testDecryptRejectsLegacyAndGarbageTokens(): void
    {
        $this->assertNull(Security::decrypt('not-a-token'));
        $this->assertNull(Security::decrypt('v2.bogusdata'));
        $this->assertNull(Security::decrypt(''));
    }

    public function testEncodeDecodeIdRoundTrip(): void
    {
        $hash = Security::encodeId(42, 'users');
        $this->assertSame(8, strlen($hash));
        $this->assertSame(42, Security::decodeId($hash, 'users'));
    }

    public function testDecodeIdRejectsInvalidHash(): void
    {
        $this->assertNull(Security::decodeId('zzzzzzzzzz', 'users'));
        $this->assertNull(Security::decodeId('', 'users'));
    }

    public function testHashPasswordVerifies(): void
    {
        $hash = Security::hashPassword('SuperSecret#2026');
        $this->assertTrue(Security::verifyPassword('SuperSecret#2026', $hash));
        $this->assertFalse(Security::verifyPassword('WrongPassword', $hash));
    }

    public function testSanitizeStripsHtml(): void
    {
        $this->assertSame(
            '&lt;script&gt;alert(1)&lt;/script&gt;',
            Security::sanitize('<script>alert(1)</script>')
        );
    }
}
