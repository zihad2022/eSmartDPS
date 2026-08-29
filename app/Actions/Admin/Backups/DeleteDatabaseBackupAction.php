<?php

namespace App\Actions\Admin\Backups;

use Illuminate\Support\Facades\Storage;

class DeleteDatabaseBackupAction
{
    public function __construct(
        private readonly ResolveDatabaseBackupAction $resolve,
    ) {}

    public function execute(string $filename): void
    {
        Storage::disk('local')->delete($this->resolve->execute($filename));
    }
}
