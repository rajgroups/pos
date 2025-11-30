<?php

namespace App\Helpers;

class ImageHelper
{
    public const BASE_PATH = 'upload/';

    // Folder names
    public const CATEGORY = 'category/';
    public const USER     = 'user/';
    public const PRODUCT  = 'product/';

    /**
     * Build full storage path
     */
    public static function path(string $directory): string
    {
        return self::BASE_PATH . $directory;
    }

    /**
     * Upload image and return FULL URL
     */
    public static function uploadImage($image, $oldImage = null, $directory)
    {
        if (!$image) {
            return $oldImage ? self::fullUrl($directory, $oldImage) : null;
        }

        $path = self::path($directory);

        // Ensure trailing slash
        if (!str_ends_with($path, '/')) {
            $path .= '/';
        }

        // Create folder if missing
        if (!file_exists(public_path($path))) {
            mkdir(public_path($path), 0777, true);
        }

        // Generate unique filename
        $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();

        // Upload file
        $image->move(public_path($path), $imageName);

        // Remove old file
        if ($oldImage && file_exists(public_path($path . $oldImage))) {
            @unlink(public_path($path . $oldImage));
        }

        // Return FULL URL
        return self::BASE_PATH.$directory.$imageName;
        // return self::fullUrl($directory, $imageName);
    }

    /**
     * Convert filename to full URL
     */
    public static function fullUrl(string $directory, string $filename): string
    {
        $path = self::path($directory);

        if (!str_ends_with($path, '/')) {
            $path .= '/';
        }

        return asset($path . $filename);
    }

    public static function deleteImage(?string $image, string $directory): bool
    {
        if (!$image) {
            return false;
        }

        // Image stored like: upload/category/filename.jpg
        $relative = str_replace(self::BASE_PATH . $directory, '', $image);

        $path = public_path(self::BASE_PATH . $directory . $relative);

        if (file_exists($path)) {
            @unlink($path);
            return true;
        }

        return false;
    }

}
