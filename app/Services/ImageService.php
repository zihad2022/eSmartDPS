<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ImageService
{
    public function uploadImage(UploadedFile $file, $folder = 'uploads')
    {
        return $file->store($folder, 'public');
    }

    public function updateImage($newFile, $folder = 'uploads')
    {
        return $this->uploadImage($newFile, $folder);
    }

    public function deleteImage($filePath)
    {
        if ($filePath && Storage::disk('public')->exists($filePath)) {
            Storage::disk('public')->delete($filePath);
        }
    }
}
