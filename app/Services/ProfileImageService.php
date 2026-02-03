<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class ProfileImageService
{
    /**
     * Upload profile image to S3
     *
     * @param UploadedFile $file
     * @param string $userId
     * @return string|null
     */
    public function uploadProfileImage(UploadedFile $file, string $userId): ?string
    {
        try {
            $filename = 'profile_' . $userId . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = 'profile-images/' . $filename;

            // Store the file with public visibility
            Storage::disk('s3')->put($path, file_get_contents($file), [
                'visibility' => 'public',
                'ContentType' => $file->getMimeType(),
            ]);
            
            return $path;
        } catch (\Exception $e) {
            \Log::error('Failed to upload profile image: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Delete old profile image from S3
     *
     * @param string|null $path
     * @return bool
     */
    public function deleteProfileImage(?string $path): bool
    {
        if (empty($path)) {
            return true;
        }

        return Storage::disk('s3')->delete($path);
    }

    /**
     * Get full URL for profile image
     *
     * @param string|null $path
     * @return string
     */
    public function getProfileImageUrl(?string $path): string
    {
        if (empty($path)) {
            return asset('images/profile.png');
        }

        try {
            return Storage::disk('s3')->url($path);
        } catch (\Exception $e) {
            \Log::error('Failed to get profile image URL: ' . $e->getMessage());
            return asset('images/profile.png');
        }
    }
}
