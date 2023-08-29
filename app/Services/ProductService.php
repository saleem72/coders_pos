<?php

namespace App\Services;

use App\Models\Product;
use App\Models\User;

class ProductService {
    public function __construct(protected UploadProductImage $service)
    {
        $this->service = $service;
    }

    public function createProduct(Array $input, $image): Product {

        if ($image) {
            $imageId = hexdec(uniqid());

            $this->service->upload($image, $imageId);

            $product = Product::create([
                'category_id' => $input['category_id'],
                'unit_id' => $input['unit_id'],
                'name' => $input['name'],
                'purchase' => $input['purchase'],
                'retail' => $input['retail'],
                'quantity' => $input['quantity'],
                'barcode' => $input['barcode'],
                'image' => $imageId,
                'image_extension' => $image->getClientOriginalExtension()
            ]);

            return $product;
        } else {
            $product = Product::create([
                'category_id' => $input['category_id'],
                'unit_id' => $input['unit_id'],
                'name' => $input['name'],
                'purchase' => $input['purchase'],
                'retail' => $input['retail'],
                'quantity' => $input['quantity'],
                'barcode' => $input['barcode']
            ]);

            return $product;
        }
    }

    public function updateProduct(Product $product, Array $input, $image): Product {

        if ($image) {
            $imageId = hexdec(uniqid());
            error_log('I am here');
            self::uploadImage($product, $image, $imageId);
            $input['image'] = $imageId;
            $input['image_extension'] = $image->getClientOriginalExtension();

            $product->update($input);
            $product = Product::find($product->id);
            return $product;
        } else {
            $product->update($input);
            $product = Product::find($product->id);

            return $product;
        }
    }

    public function deleteOldImage(Product $product) {
        $this->service->deleteOld($product->image, $product->image_extension);
    }

    public function uploadImage(Product $product, Object $file, String $imageId) {
        if ($product->image) {
            self::deleteOldImage($product);
        }
        $this->service->upload($file, $imageId);
    }
}
