<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class UploadService
{
    /**
     * uploadLocal
     *
     * @param  UploadedFile $file
     * @param  string $path
     * @return array
     */
    public static function uploadLocal(UploadedFile $file, $path = 'images'): array
    {
        if (!App::environment('production')) {
            $path = 'images/dev';
        }

        $relativePath = Storage::putFile('public/' . $path, $file);

        $path = URL::to(Storage::url($relativePath));

        return [
            'image' => $path,
            'image_mime' => $file->getClientMimeType(),
            'image_size' => $file->getSize()
        ];
    }
}
