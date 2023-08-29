<?php

namespace App\Services;
use Image;


use App\Models\Customer;
use Illuminate\Support\Facades\Storage;

class UploadCustomerImage {
    protected String $path;
    public function __construct(protected UploadImage $service)
    {
        $this->service = $service;
        $this->path = 'public/customers/avatar/';
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

    public function update(Customer $customer, Object $file, String $fileName) {
        if ($customer->image) {
            self::deleteOld($customer->image.'.'.$customer->image_extension);
        }

        self::upload($file, $fileName);
    }

    public function deleteOld(String $fileName) {
        // $path = 'public/users/avatar/';

        // if(Storage::exists($path.$fileName)) Storage::delete($path.$fileName);
        $this->service->deleteOld($this->path, $fileName);
    }
}
