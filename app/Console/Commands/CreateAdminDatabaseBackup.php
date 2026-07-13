<?php

namespace App\Console\Commands;

use App\Actions\Admin\Backups\GenerateDatabaseBackupAction;
use App\Actions\Admin\Settings\GetAdminSettingsAction;
use Illuminate\Console\Command;

class CreateAdminDatabaseBackup extends Command
{
    protected $signature = 'admin:backup {--force : Create a backup even when automatic backup is disabled or not yet due}';

    protected $description = 'Create a compressed logical database backup for the admin panel';

    public function handle(
        GetAdminSettingsAction $getSettings,
        GenerateDatabaseBackupAction $generate,
    ): int {
        $settings = $getSettings->execute();

        if (! $this->option('force') && (! $settings->backup_enabled || ! $this->isDue($settings))) {
            $this->components->info('No automatic database backup is due.');

            return self::SUCCESS;
        }

        $backup = $generate->execute();
        $this->components->info("Database backup created: {$backup['name']}");

        return self::SUCCESS;
    }

    private function isDue($settings): bool
    {
        if (! $settings->last_backup_at) {
            return true;
        }

        return match ($settings->backup_frequency) {
            'weekly' => $settings->last_backup_at->lte(now()->subWeek()),
            'monthly' => $settings->last_backup_at->lte(now()->subMonthNoOverflow()),
            default => $settings->last_backup_at->lte(now()->subDay()),
        };
    }
}
