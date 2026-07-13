<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Admin\Backups\DeleteDatabaseBackupAction;
use App\Actions\Admin\Backups\GenerateDatabaseBackupAction;
use App\Actions\Admin\Backups\ResolveDatabaseBackupAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Throwable;

class AdminBackupController extends Controller
{
    public function store(GenerateDatabaseBackupAction $generate): RedirectResponse
    {
        try {
            $backup = $generate->execute();
        } catch (Throwable $exception) {
            report($exception);

            return back()->with('error', 'The database backup could not be created. Check the database and storage configuration.');
        }

        return back()->with('success', "Database backup [{$backup['name']}] created successfully.");
    }

    public function download(string $backup, ResolveDatabaseBackupAction $resolve): BinaryFileResponse
    {
        return Storage::disk('local')->download($resolve->execute($backup));
    }

    public function destroy(string $backup, DeleteDatabaseBackupAction $delete): RedirectResponse
    {
        $delete->execute($backup);

        return back()->with('success', 'Database backup deleted successfully.');
    }
}
