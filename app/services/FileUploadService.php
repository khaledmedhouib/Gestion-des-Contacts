<?php
// app/services/FileUploadService.php

class FileUploadService
{
    private string $uploadDir;

    private const ALLOWED_MIMES = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
    ];

    private const MAX_SIZE = 2_097_152; // 2 MB

    public function __construct(string $uploadDir)
    {
        $this->uploadDir = rtrim($uploadDir, '/') . '/';
    }

    /**
     * Validates and moves an uploaded file.
     *
     * @param  array  $file  Entry from $_FILES
     * @return string        Relative path for storage (e.g. "uploads/photos/photo_xxx.jpg")
     * @throws \RuntimeException on any validation or IO failure
     */
    public function store(array $file): string
    {
        $this->ensureDirectory();
        $this->assertNoUploadError($file['error']);
        $this->assertSize($file['size']);

        $mime = mime_content_type($file['tmp_name']);
        $ext  = $this->resolveExtension($mime);

        $filename = uniqid('photo_', true) . '.' . $ext;
        $dest     = $this->uploadDir . $filename;

        if (!move_uploaded_file($file['tmp_name'], $dest)) {
            throw new \RuntimeException('Impossible de déplacer le fichier téléversé.');
        }

        return 'uploads/photos/' . $filename;
    }

    /**
     * Deletes a stored photo by its relative path.
     * Silently ignores missing files.
     */
    public function delete(string $relativePath, string $publicRoot): void
    {
        $absolute = rtrim($publicRoot, '/') . '/' . ltrim($relativePath, '/');

        if (file_exists($absolute)) {
            @unlink($absolute);
        }
    }

    // ── Private ───────────────────────────────────────────────────────────

    private function ensureDirectory(): void
    {
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }
    }

    private function assertNoUploadError(int $error): void
    {
        if ($error !== UPLOAD_ERR_OK) {
            throw new \RuntimeException("Erreur d'upload PHP : code {$error}.");
        }
    }

    private function assertSize(int $size): void
    {
        if ($size > self::MAX_SIZE) {
            throw new \RuntimeException('Le fichier dépasse la taille maximale autorisée (2 Mo).');
        }
    }

    private function resolveExtension(string $mime): string
    {
        if (!array_key_exists($mime, self::ALLOWED_MIMES)) {
            throw new \RuntimeException('Type de fichier non autorisé. Formats acceptés : JPEG, PNG, WebP.');
        }

        return self::ALLOWED_MIMES[$mime];
    }
}