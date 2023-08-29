<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Image;

class UploadImage {
    public function upload($file, $path, $fileName) {
        $image_x_1 = Image::make($file)
            ->heighten(150, function ($constraint) {
                $constraint->upsize();
            })
            ->encode($file->getClientOriginalExtension());


        Storage::put($path.$fileName, $image_x_1->__toString());
    }

    public function deleteOld(String $path, String $fileName) {
        if(Storage::exists($path.$fileName)) Storage::delete($path.$fileName);
    }
}
