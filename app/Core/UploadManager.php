<?php

namespace App\Core;

/**
 * UploadManager — ตัวจัดการอัปโหลดไฟล์กลางของระบบ
 *
 * มาตรการความปลอดภัย (ป้องกัน RCE ผ่านไฟล์ปลอม):
 *   1. ตรวจ extension กับ whitelist ที่ผู้เรียกกำหนด
 *   2. ตรวจ MIME จริงของไฟล์ (finfo) และบังคับให้ "ตรงคู่กับ extension"
 *      (กัน polyglot เช่น GIF header + โค้ด PHP ที่ตั้งชื่อ .php หรือ .php ตั้งชื่อ .jpg)
 *   3. สร้างชื่อไฟล์ใหม่แบบสุ่มเสมอ (ไม่ใช้ชื่อจากผู้อัปโหลด) — กัน path traversal และการเดาชื่อ
 *   4. จำกัดขนาดไฟล์ตามที่ผู้เรียกกำหนด
 */
class UploadManager
{
    /**
     * MIME types ที่ยอมรับได้ของแต่ละ extension (ต้องตรงคู่กันเสมอ)
     */
    private const MIME_BY_EXTENSION = [
        'jpg'  => ['image/jpeg'],
        'jpeg' => ['image/jpeg'],
        'png'  => ['image/png'],
        'gif'  => ['image/gif'],
        'webp' => ['image/webp'],
        'ico'  => ['image/x-icon', 'image/vnd.microsoft.icon', 'image/png'],
    ];

    /**
     * ขนาดสูงสุดของรูป base64 ที่ decode แล้ว (5 MB)
     */
    private const MAX_BASE64_BYTES = 5242880;

    /**
     * Handle a single file upload.
     *
     * @param array $file The $_FILES['input_name'] array
     * @param string $destinationDir The directory where the file should be saved
     * @param array $allowedExtensions Array of allowed file extensions (without dot)
     * @param int $maxSize Maximum file size in bytes (default 2MB)
     * @return string|null The generated filename on success, null if no file was uploaded
     * @throws \Exception If the file is invalid or upload fails
     */
    public static function handle(array $file, string $destinationDir, array $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'], int $maxSize = 2097152): ?string
    {
        if (!isset($file['error']) || $file['error'] === UPLOAD_ERR_NO_FILE) {
            return null; // No file uploaded
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new \Exception(self::getErrorMessage($file['error']));
        }

        if ($file['size'] > $maxSize) {
            throw new \Exception(self::tr('messages.upload_too_large', 'File size exceeds the limit of ' . ($maxSize / 1024 / 1024) . 'MB.'));
        }

        // 1) Extension whitelist — เอาจากชื่อไฟล์ แต่ใช้เพื่อเลือก MIME คู่ที่ยอมรับเท่านั้น
        $ext = strtolower(pathinfo((string) $file['name'], PATHINFO_EXTENSION));
        if (!empty($allowedExtensions) && !in_array($ext, $allowedExtensions, true)) {
            throw new \Exception(self::tr('messages.upload_invalid_extension', 'Invalid file extension. Allowed: ' . implode(', ', $allowedExtensions)));
        }

        // 2) MIME จริงของไฟล์ต้องตรงคู่กับ extension (กัน polyglot / ไฟล์ปลอม)
        if (!self::mimeMatchesExtension((string) $file['tmp_name'], $ext)) {
            throw new \Exception(self::tr('messages.upload_invalid_type', 'File content does not match its extension.'));
        }

        if (!is_dir($destinationDir)) {
            if (!mkdir($destinationDir, 0755, true)) {
                throw new \Exception(self::tr('messages.upload_failed', 'Failed to create upload directory.'));
            }
        }

        // 3) ชื่อไฟล์ใหม่แบบสุ่มเสมอ
        $filename = bin2hex(random_bytes(16)) . '.' . $ext;
        $destination = rtrim($destinationDir, '/\\') . DIRECTORY_SEPARATOR . $filename;

        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            throw new \Exception(self::tr('messages.upload_failed', 'Failed to move uploaded file.'));
        }

        return $filename;
    }

    /**
     * จัดการรูปที่ส่งมาแบบ base64 (เช่น ผลจาก Cropper.js)
     *
     * @param string $dataUri เช่น "data:image/png;base64,...."
     * @param string $destinationDir โฟลเดอร์ปลายทาง
     * @param string|null $filenamePrefix คำนำหน้าชื่อไฟล์ (เช่น 'profile_1_')
     * @return string ชื่อไฟล์ที่บันทึกสำเร็จ
     * @throws \Exception กรณีข้อมูลไม่ถูกต้อง / เกินขนาด / ชนิดไฟล์ไม่รองรับ
     */
    public static function handleBase64Image(string $dataUri, string $destinationDir, ?string $filenamePrefix = null): string
    {
        // แยก mime type ที่ประกาศ ออกจากข้อมูลจริง
        $formatPart = '';
        $base64Part = $dataUri;
        if (str_starts_with($dataUri, 'data:')) {
            [$formatPart, $base64Part] = explode(',', substr($dataUri, 5), 2) + ['', ''];
        }

        $data = base64_decode($base64Part, true);
        if ($data === false || $data === '') {
            throw new \Exception(self::tr('messages.upload_invalid_type', 'Invalid image data.'));
        }

        if (strlen($data) > self::MAX_BASE64_BYTES) {
            throw new \Exception(self::tr('messages.upload_too_large', 'Image exceeds the maximum allowed size.'));
        }

        // ตัดสินชนิดไฟล์จาก magic bytes ของข้อมูลจริง (ไม่เชื่อค่าที่ client ประกาศ)
        $ext = self::sniffImageExtension($data);
        if ($ext === null) {
            throw new \Exception(self::tr('messages.upload_invalid_type', 'Unsupported image type.'));
        }

        if (!is_dir($destinationDir)) {
            if (!mkdir($destinationDir, 0755, true)) {
                throw new \Exception(self::tr('messages.upload_failed', 'Failed to create upload directory.'));
            }
        }

        $filename = ($filenamePrefix ?: '') . bin2hex(random_bytes(8)) . '.' . $ext;
        $destination = rtrim($destinationDir, '/\\') . DIRECTORY_SEPARATOR . $filename;

        if (file_put_contents($destination, $data) === false) {
            throw new \Exception(self::tr('messages.upload_failed', 'Failed to write uploaded file.'));
        }

        return $filename;
    }

    /**
     * ตรวจว่า MIME จริงของไฟล์ตรงกับคู่ของ extension หรือไม่
     */
    private static function mimeMatchesExtension(string $tmpPath, string $ext): bool
    {
        $allowedMimes = self::MIME_BY_EXTENSION[$ext] ?? null;
        if ($allowedMimes === null) {
            return false; // extension ไม่อยู่ในคู่ที่รู้จัก = ปฏิเสธ
        }

        $detected = self::detectMime($tmpPath);
        return in_array($detected, $allowedMimes, true);
    }

    /**
     * ตรวจชนิดรูปจาก magic bytes (ไม่พึ่งค่าที่ client บอกมา)
     * คืน extension ที่ตรงกับข้อมูลจริง หรือ null ถ้าไม่ใช่รูปที่รองรับ
     */
    private static function sniffImageExtension(string $data): ?string
    {
        // PNG: 89 50 4E 47
        if (str_starts_with($data, "\x89PNG")) {
            return 'png';
        }
        // JPEG: FF D8 FF
        if (str_starts_with($data, "\xFF\xD8\xFF")) {
            return 'jpg';
        }
        // GIF87a / GIF89a
        if (str_starts_with($data, 'GIF87a') || str_starts_with($data, 'GIF89a')) {
            return 'gif';
        }
        // WEBP: "RIFF"...."WEBP"
        if (str_starts_with($data, 'RIFF') && substr($data, 8, 4) === 'WEBP') {
            return 'webp';
        }
        return null;
    }

    /**
     * ตรวจ MIME ของไฟล์จากเนื้อหาจริง (finfo ก่อน, fallback mime_content_type)
     */
    private static function detectMime(string $path): string|false
    {
        if (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $path);
            finfo_close($finfo);
            if (is_string($mime) && $mime !== '') {
                return $mime;
            }
        }
        if (function_exists('mime_content_type')) {
            return mime_content_type($path);
        }
        return false;
    }

    /**
     * Map PHP upload error codes to readable messages.
     */
    private static function getErrorMessage(int $code): string
    {
        switch ($code) {
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                return self::tr('messages.upload_too_large', 'File is too large.');
            case UPLOAD_ERR_PARTIAL:
                return self::tr('messages.upload_failed', 'File was only partially uploaded.');
            case UPLOAD_ERR_NO_TMP_DIR:
                return self::tr('messages.upload_failed', 'Missing a temporary folder.');
            case UPLOAD_ERR_CANT_WRITE:
                return self::tr('messages.upload_failed', 'Failed to write file to disk.');
            case UPLOAD_ERR_EXTENSION:
                return self::tr('messages.upload_failed', 'A PHP extension stopped the file upload.');
            default:
                return self::tr('messages.upload_failed', 'Unknown upload error.');
        }
    }

    /**
     * แปลข้อความผ่าน i18n พร้อม fallback (ใช้ได้แม้ helpers ยังไม่ถูกโหลด เช่นใน unit test)
     */
    private static function tr(string $key, string $fallback): string
    {
        if (function_exists('\\__')) {
            $translated = \__($key);
            if ($translated !== $key) {
                return $translated;
            }
        }
        return $fallback;
    }
}
