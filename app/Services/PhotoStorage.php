<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class PhotoStorage
{
    /**
     * Simpan foto asli dan thumbnail lebar 320px
     *
     * @return array{photo_path: string, thumb_path: string}
     */
    public function store(UploadedFile $file): array
    {
        $filename = Str::random(40);
        $ext = $file->getClientOriginalExtension() ?: 'jpg';

        $photoFilename = $filename.'.'.$ext;
        $thumbFilename = $filename.'_thumb.jpg';

        $photoPath = $file->storeAs('services', $photoFilename);

        $manager = new ImageManager(new Driver);
        $image = $manager->decodePath($file->getRealPath());
        $image->scaleDown(width: 320);
        $encodedThumb = (string) $image->encodeUsingFileExtension('jpg', quality: 80);

        $thumbPath = 'services/thumbs/'.$thumbFilename;
        Storage::disk('local')->put($thumbPath, $encodedThumb);

        return [
            'photo_path' => $photoPath,
            'thumb_path' => $thumbPath,
        ];
    }

    public function delete(string $photoPath, string $thumbPath): void
    {
        Storage::disk('local')->delete([$photoPath, $thumbPath]);
    }
}
