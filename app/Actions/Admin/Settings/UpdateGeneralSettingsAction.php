<?php

namespace App\Actions\Admin\Settings;

use App\Models\AdminSetting;
use App\Services\ActivityLogger;
use App\Services\ImageService;
use Illuminate\Http\UploadedFile;

class UpdateGeneralSettingsAction
{
    public function __construct(private readonly ImageService $imageService) {}

    public function execute(AdminSetting $settings, array $data): AdminSetting
    {
        $originalFiles = collect(['site_logo', 'favicon', 'graph_thumbnail'])
            ->mapWithKeys(fn (string $field): array => [$field => $settings->{$field}])
            ->all();
        $uploadedFiles = [];

        try {
            foreach (array_keys($originalFiles) as $field) {
                if (($data[$field] ?? null) instanceof UploadedFile) {
                    $data[$field] = $this->imageService->uploadImage($data[$field], 'settings');
                    $uploadedFiles[$field] = $data[$field];
                } else {
                    unset($data[$field]);
                }
            }

            $settings->update($data);
        } catch (\Throwable $exception) {
            foreach ($uploadedFiles as $path) {
                $this->imageService->deleteImage($path);
            }

            throw $exception;
        }

        foreach ($uploadedFiles as $field => $newPath) {
            if ($originalFiles[$field] && $originalFiles[$field] !== $newPath) {
                $this->imageService->deleteImage($originalFiles[$field]);
            }
        }

        ActivityLogger::log('General settings updated.');

        return $settings->refresh();
    }
}
