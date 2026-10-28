<?php
/**
 * ลบ UTF-8 BOM ออกจากไฟล์ Route และตรวจสอบความถูกต้องของภาษาไทย
 * (ใช้ PHP script ตามกฎ Mojibake Prevention — ห้ามใช้ PowerShell แก้ไฟล์ภาษาไทย)
 */
$files = glob(__DIR__ . '/../app/Routes/*.php');
$files = array_merge($files, glob(__DIR__ . '/../app/Core/Route*.php'));

foreach ($files as $file) {
    $content = file_get_contents($file);

    // ลบ BOM
    if (str_starts_with($content, "\xEF\xBB\xBF")) {
        $content = substr($content, 3);
        file_put_contents($file, $content);
        echo "Removed BOM: {$file}\n";
    }

    // ตรวจสอบว่าเป็น UTF-8 ที่ถูกต้อง
    if (!mb_check_encoding($content, 'UTF-8')) {
        echo "!! INVALID UTF-8: {$file}\n";
        continue;
    }

    // ตรวจสอบว่ามีอักขระทดแทน (mojibake) หรือไม่
    if (str_contains($content, "\xEF\xBF\xBD")) {
        echo "!! REPLACEMENT CHAR FOUND: {$file}\n";
    }
}

echo "Done.\n";
