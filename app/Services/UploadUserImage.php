<?php

namespace App\Services;
use Image;


use Illuminate\Support\Facades\Storage;

class UploadUserImage {
    protected String $path;
    public function __construct(protected UploadImage $service)
    {
        $this->service = $service;
        $this->path = 'public/users/avatar/';
    }

    public function upload(Object $file, String $fileName) {

        // $path = 'public/users/avatar/';

        $this->service->upload($file, $this->path, $fileName);
        // $image_x_1 = Image::make($file)
        //     ->heighten(150, function ($constraint) {
        //         $constraint->upsize();
        //     })
        //     ->encode($file->getClientOriginalExtension());


        // Storage::put($path.$fileName, $image_x_1->__toString());
    }

    public function deleteOld(String $fileName) {
        // $path = 'public/users/avatar/';

        // if(Storage::exists($path.$fileName)) Storage::delete($path.$fileName);
        $this->service->deleteOld($this->path, $fileName);
    }
}
