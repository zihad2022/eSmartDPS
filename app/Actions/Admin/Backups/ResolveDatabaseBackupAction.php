<?php

namespace App\Actions\Admin\Backups;

use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ResolveDatabaseBackupAction
{
    public function execute(string $filename): string
    {
        $safeName = basename($filename);

        if ($safeName !== $filename || ! preg_match('/^database-\d{4}-\d{2}-\d{2}_\d{2}-\d{2}-\d{2}(?:-[a-z0-9]{6})?\.jsonl\.gz$/', $safeName)) {
            throw new NotFoundHttpException;
        }

        $path = 'private/admin-backups/'.$safeName;

        if (! Storage::disk('local')->exists($path)) {
            throw new NotFoundHttpException;
        }

        return $path;
    }
}
