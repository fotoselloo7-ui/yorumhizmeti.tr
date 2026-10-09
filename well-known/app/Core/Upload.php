<?php
namespace App\Core;

class Upload
{
    private static array $allowedImages = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
    private static array $allowedDocs = ['application/pdf', 'image/jpeg', 'image/png', 'image/webp'];
    private static int $maxSize = 5 * 1024 * 1024; // 5MB

    public static function image(array $file, string $directory): ?string
    {
        return self::handle($file, $directory, self::$allowedImages);
    }

    /** Optional first-party video for Instagram references when third-party embeds are restricted. */
    public static function video(array $file, string $directory): ?string
    {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) return null;
        $originalExt = strtolower(pathinfo((string)($file['name'] ?? ''), PATHINFO_EXTENSION));
        if (!in_array($originalExt, ['mp4', 'webm'], true)) return null;
        $mime = (new \finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name'] ?? '');
        $pairs = ['mp4' => ['video/mp4','application/mp4'], 'webm' => ['video/webm']];
        if (!in_array($mime, $pairs[$originalExt], true)) return null;
        return self::handle($file, $directory, ['video/mp4','application/mp4','video/webm'], 80 * 1024 * 1024);
    }

    public static function receipt(array $file, string $directory): ?string
    {
        return self::handle($file, $directory, self::$allowedDocs, 10 * 1024 * 1024);
    }

    public static function attachment(array $file, string $directory): ?string
    {
        $allowed = array_merge(self::$allowedImages, self::$allowedDocs, [
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ]);
        return self::handle($file, $directory, $allowed, 10 * 1024 * 1024);
    }

    private static function handle(array $file, string $directory, array $allowedTypes, int $maxSize = 0): ?string
    {
        if ($maxSize === 0) $maxSize = self::$maxSize;

        if ($file['error'] !== UPLOAD_ERR_OK || $file['size'] === 0) {
            return null;
        }

        if ($file['size'] > $maxSize) {
            flash('error', 'Dosya boyutu çok büyük. Maksimum: ' . round($maxSize / 1024 / 1024) . 'MB');
            return null;
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);

        if (!in_array($mimeType, $allowedTypes)) {
            flash('error', 'Desteklenmeyen dosya türü: ' . $mimeType);
            return null;
        }

        $uploadDir = PUBLIC_PATH . '/uploads/' . $directory;
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        $safeExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf', 'doc', 'docx', 'mp4', 'webm'];
        if (!in_array($ext, $safeExtensions)) {
            flash('error', 'Bu dosya uzantısına izin verilmiyor: ' . $ext);
            return null;
        }

        $filename = uniqid() . '_' . time() . '.' . $ext;
        $targetPath = $uploadDir . '/' . $filename;

        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            return '/uploads/' . $directory . '/' . $filename;
        }

        return null;
    }

    public static function delete(string $path): void
    {
        $fullPath = PUBLIC_PATH . $path;
        if (file_exists($fullPath) && is_file($fullPath)) {
            unlink($fullPath);
        }
    }
}
