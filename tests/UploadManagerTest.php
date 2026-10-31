<?php

declare(strict_types=1);

namespace Tests;

use App\Core\UploadManager;
use PHPUnit\Framework\TestCase;

final class UploadManagerTest extends TestCase
{
    private string $tmpDir = '';

    protected function setUp(): void
    {
        $this->tmpDir = sys_get_temp_dir() . '/ums_upload_test_' . bin2hex(random_bytes(4));
        mkdir($this->tmpDir, 0755, true);
    }

    protected function tearDown(): void
    {
        foreach (glob($this->tmpDir . '/*') ?: [] as $f) {
            @unlink($f);
        }
        @rmdir($this->tmpDir);
    }

    /**
     * สร้าง fake uploaded file array สำหรับทดสอบ (ไม่ได้ผ่าน move_uploaded_file จริง —
     * handle() จะ fail ที่ move step สำหรับกรณี valid, เราจึงทดสอบ "ก่อน move" ด้วยการ
     * คาดหวัง exception ของกรณี invalid ซึ่งเกิดก่อน move เสมอ)
     */
    private function fakeUpload(string $name, string $realPath): array
    {
        return [
            'name'     => $name,
            'type'     => 'image/jpeg',
            'tmp_name' => $realPath,
            'error'    => UPLOAD_ERR_OK,
            'size'     => (int) filesize($realPath),
        ];
    }

    private function makeRealImage(string $path): void
    {
        // GIF89a header จริง (finfo จะ detect เป็น image/gif)
        file_put_contents($path, 'GIF89a' . str_repeat("\x00", 32));
    }

    public function testRejectsExecutableExtensionEvenWithImageContent(): void
    {
        // polyglot: GIF header + ชื่อ .php — ต้องถูกปฏิเสธที่ extension whitelist
        $tmp = $this->tmpDir . '/evil.gif';
        $this->makeRealImage($tmp);
        $file = $this->fakeUpload('evil.php', $tmp);

        $this->expectException(\Exception::class);
        UploadManager::handle($file, $this->tmpDir, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
    }

    public function testRejectsMismatchedMimeAndExtension(): void
    {
        // ไฟล์ text ธรรมดาอ้างว่าเป็น .jpg — MIME จริงไม่ตรงคู่ ต้องปฏิเสธ
        $tmp = $this->tmpDir . '/fake.txt';
        file_put_contents($tmp, '<?php echo "not an image";');
        $file = $this->fakeUpload('fake.jpg', $tmp);

        $this->expectException(\Exception::class);
        UploadManager::handle($file, $this->tmpDir, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
    }

    public function testBase64SniffsMagicBytesNotDeclaredType(): void
    {
        // อ้างว่าเป็น png แต่ข้อมูลจริงเป็น PHP code — ต้องปฏิเสธ (magic bytes ไม่ใช่รูป)
        $this->expectException(\Exception::class);
        UploadManager::handleBase64Image(
            'data:image/png;base64,' . base64_encode('<?php evil();'),
            $this->tmpDir
        );
    }

    public function testBase64AcceptsRealPngData(): void
    {
        $png = "\x89PNG\r\n\x1a\n" . str_repeat("\x00", 16);
        $saved = UploadManager::handleBase64Image(
            'data:image/png;base64,' . base64_encode($png),
            $this->tmpDir,
            'test_'
        );
        $this->assertStringStartsWith('test_', $saved);
        $this->assertStringEndsWith('.png', $saved);
        $this->assertFileExists($this->tmpDir . '/' . $saved);
    }

    public function testBase64RejectsOversizedData(): void
    {
        // ~6MB ของข้อมูลเกิน limit 5MB
        $big = str_repeat("\x89PNG", 1) . str_repeat('A', 6 * 1024 * 1024);
        $this->expectException(\Exception::class);
        UploadManager::handleBase64Image(
            'data:image/png;base64,' . base64_encode($big),
            $this->tmpDir
        );
    }
}
