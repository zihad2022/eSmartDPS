<?php

namespace App\Actions\Admin\Backups;

use App\Actions\Admin\Settings\GetAdminSettingsAction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

class GenerateDatabaseBackupAction
{
    public function __construct(
        private readonly GetAdminSettingsAction $getSettings,
    ) {
    }

    public function execute(): array
    {
        if (! function_exists('gzopen')) {
            throw new RuntimeException('The PHP zlib extension is required to create compressed backups.');
        }

        $disk = Storage::disk('local');
        $directory = 'private/admin-backups';
        $disk->makeDirectory($directory);

        $filename = sprintf(
            'database-%s-%s.jsonl.gz',
            now()->format('Y-m-d_H-i-s'),
            Str::lower(Str::random(6)),
        );
        $relativePath = $directory.'/'.$filename;
        $absolutePath = $disk->path($relativePath);
        $handle = gzopen($absolutePath, 'wb9');

        if ($handle === false) {
            throw new RuntimeException('Unable to create the database backup file.');
        }

        try {
            $this->writeLine($handle, [
                'type' => 'metadata',
                'format' => 'esmartdps-jsonl-v1',
                'database_driver' => DB::getDriverName(),
                'created_at' => now()->toIso8601String(),
            ]);

            foreach ($this->tables() as $table) {
                $count = DB::table($table)->count();
                $this->writeLine($handle, [
                    'type' => 'table',
                    'name' => $table,
                    'rows' => $count,
                ]);

                DB::table($table)->cursor()->each(function (object $row) use ($handle, $table): void {
                    $this->writeLine($handle, [
                        'type' => 'row',
                        'table' => $table,
                        'data' => (array) $row,
                    ]);
                });
            }
        } catch (Throwable $exception) {
            gzclose($handle);
            $disk->delete($relativePath);
            throw $exception;
        }

        gzclose($handle);

        $settings = $this->getSettings->execute();
        $settings->forceFill([
            'last_backup_at' => now(),
            'last_backup_path' => $relativePath,
        ])->save();

        $this->pruneOldBackups(20);

        return [
            'name' => $filename,
            'path' => $relativePath,
            'size' => $disk->size($relativePath),
        ];
    }

    /** @return list<string> */
    private function tables(): array
    {
        $driver = DB::getDriverName();

        $tables = match ($driver) {
            'mysql', 'mariadb' => collect(DB::select('SHOW TABLES'))
                ->map(fn (object $row): string => (string) array_values((array) $row)[0]),
            'sqlite' => collect(DB::select("SELECT name FROM sqlite_master WHERE type = 'table' AND name NOT LIKE 'sqlite_%'"))
                ->pluck('name'),
            'pgsql' => collect(DB::select("SELECT tablename FROM pg_tables WHERE schemaname = 'public'"))
                ->pluck('tablename'),
            default => throw new RuntimeException("Database backups are not supported for the [{$driver}] driver."),
        };

        return $tables->filter()->sort()->values()->all();
    }

    /** @param resource $handle */
    private function writeLine($handle, array $payload): void
    {
        $encoded = json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);

        if (gzwrite($handle, $encoded."\n") === false) {
            throw new RuntimeException('Unable to write to the database backup file.');
        }
    }

    private function pruneOldBackups(int $keep): void
    {
        $disk = Storage::disk('local');
        $files = collect($disk->files('private/admin-backups'))
            ->filter(fn (string $path): bool => str_ends_with($path, '.jsonl.gz'))
            ->sortByDesc(fn (string $path): int => $disk->lastModified($path))
            ->values();

        $files->slice($keep)->each(fn (string $path) => $disk->delete($path));
    }
}
