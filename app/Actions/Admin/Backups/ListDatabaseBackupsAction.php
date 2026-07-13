<?php

namespace App\Actions\Admin\Backups;

use Illuminate\Support\Facades\Storage;

class ListDatabaseBackupsAction
{
    public function execute(): array
    {
        $disk = Storage::disk('local');

        return collect($disk->files('private/admin-backups'))
            ->filter(fn (string $path): bool => str_ends_with($path, '.jsonl.gz'))
            ->map(fn (string $path): array => [
                'name' => basename($path),
                'path' => $path,
                'size' => $disk->size($path),
                'modified_at' => $disk->lastModified($path),
            ])
            ->sortByDesc('modified_at')
            ->values()
            ->all();
    }
}
