<?php

namespace App\Services;

use Illuminate\Support\Str;

class UploadService
{

    public static function upload($file, $folder, $name)
    {
        if ($file) {


            $directoryPath = 'uploads/' . $folder;
            $publicURL = 'uploads/' . $folder;

            $fileName = $name;
            $send = $file->move($directoryPath, $fileName);

            if ($send) {
                //$asset = env('APP_URL');
                return asset($publicURL . '/' . $fileName); // str_replace('uploads/','',asset($publicURL.'/'.$fileName));
            }
        }

        return 'Error';
    }
}
