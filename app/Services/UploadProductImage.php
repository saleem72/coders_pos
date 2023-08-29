<?php

namespace App\Services;
use Image;


use Illuminate\Support\Facades\Storage;

class UploadProductImage {
    public function upload(Object $file, String $imageId) {

        $path = 'public/products/images/';
        $samllFileName = $imageId.'_small.'.$file->getClientOriginalExtension();
        $meduimFileName = $imageId.'_medium.'.$file->getClientOriginalExtension();
        $largeFileName = $imageId.'_large.'.$file->getClientOriginalExtension();

        $image_x_1 = Image::make($file)
            ->heighten(60, function ($constraint) {
                $constraint->upsize();
            })
            ->encode($file->getClientOriginalExtension());

        $image_x_2 = Image::make($file)
            ->heighten(100, function ($constraint) {
                $constraint->upsize();
            })
            ->encode($file->getClientOriginalExtension());

        $image_x_3 = Image::make($file)
            ->heighten(150, function ($constraint) {
                $constraint->upsize();
            })
            ->encode($file->getClientOriginalExtension());


        Storage::put($path.$samllFileName, $image_x_1->__toString());
        Storage::put($path.$meduimFileName, $image_x_2->__toString());
        Storage::put($path.$largeFileName, $image_x_3->__toString());
    }

    public function deleteOld(String $imageId, String $extension) {
        $path = 'public/products/images/';
        $samllFileName = $imageId.'_small.'.$extension;
        $meduimFileName = $imageId.'_medium.'.$extension;
        $largeFileName = $imageId.'_large.'.$extension;

        if(Storage::exists($path.$samllFileName)) {

            Storage::delete($path.$samllFileName);

        }

        if(Storage::exists($path.$meduimFileName)) Storage::delete($path.$meduimFileName);
        if(Storage::exists($path.$largeFileName)) Storage::delete($path.$largeFileName);
    }
}
