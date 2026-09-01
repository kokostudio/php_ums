<?php

namespace App\Core;

class UploadManager
{
    /**
     * Handle a single file upload.
     *
     * @param array $file The $_FILES['input_name'] array
     * @param string $destinationDir The directory where the file should be saved
     * @param array $allowedExtensions Array of allowed file extensions (without dot)
     * @param int $maxSize Maximum file size in bytes (default 2MB)
     * @return string|null The generated filename on success, null on failure or if no file was uploaded
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
            throw new \Exception("File size exceeds the limit of " . ($maxSize / 1024 / 1024) . "MB.");
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!empty($allowedExtensions) && !in_array($ext, $allowedExtensions)) {
            throw new \Exception("Invalid file extension. Allowed extensions: " . implode(', ', $allowedExtensions));
        }

        if (!is_dir($destinationDir)) {
            if (!mkdir($destinationDir, 0755, true)) {
                throw new \Exception("Failed to create upload directory.");
            }
        }

        $filename = md5(uniqid('', true)) . '.' . $ext;
        $destination = rtrim($destinationDir, '/\\') . DIRECTORY_SEPARATOR . $filename;

        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            throw new \Exception("Failed to move uploaded file.");
        }

        return $filename;
    }

    /**
     * Map PHP upload error codes to readable messages.
     */
    private static function getErrorMessage(int $code): string
    {
        switch ($code) {
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                return "File is too large.";
            case UPLOAD_ERR_PARTIAL:
                return "File was only partially uploaded.";
            case UPLOAD_ERR_NO_TMP_DIR:
                return "Missing a temporary folder.";
            case UPLOAD_ERR_CANT_WRITE:
                return "Failed to write file to disk.";
            case UPLOAD_ERR_EXTENSION:
                return "A PHP extension stopped the file upload.";
            default:
                return "Unknown upload error.";
        }
    }
}
