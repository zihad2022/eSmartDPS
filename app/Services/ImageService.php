<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ImageService
{
    /**
     * Upload an image to the specified folder in the public disk.
     *
     * @param  UploadedFile $file   The uploaded file instance
     * @param  string       $folder The folder where the file will be stored (default: 'uploads')
     * @return string               The path of the stored file
     */
    public function uploadImage(UploadedFile $file, string $folder = 'uploads'): string
    {
        // Store the file in the "public" disk and return the path
        return $file->store($folder, 'public');
    }

    /**
     * Update an existing image by uploading a new file.
     *
     * @param  UploadedFile $newFile The new uploaded file
     * @param  string       $folder  The folder where the file will be stored
     * @return string                The path of the newly stored file
     *
     * Note: This method only uploads the new file.
     *       Deleting the old file must be handled separately before calling this method.
     */
    public function updateImage(UploadedFile $newFile, string $folder = 'uploads'): string
    {
        return $this->uploadImage($newFile, $folder);
    }

    /**
     * Delete an image from the storage if it exists.
     *
     * @param  string|null $filePath The path of the file to delete
     *
     * This method checks whether the file exists in the "public" disk before deleting it.
     */
    public function deleteImage(?string $filePath): void
    {
        if ($filePath && Storage::disk('public')->exists($filePath)) {
            Storage::disk('public')->delete($filePath);
        }
    }
}
