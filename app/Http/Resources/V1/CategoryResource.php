<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use App\Http\Resources\V1\ProductResource;
use App\Http\Resources\V1\SubCategoryResource;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        if ($this->parent_id) {
            $category = $this->loadMissing('parent');
            return [
                'id' => $this->id,
                'parent' => [
                    'id' => $category->parent_id,
                   'name' => $category->parent->name,
                ],
                'name' => $this->name,
                'products_count' => $this->whenLoaded('products', function () {
                    return $this->products->count();
                }),
                'products' => ProductResource::collection($this->whenLoaded('products')),
                'children' => SubCategoryResource::collection($this->whenLoaded('children')) ?? [],
            ];
        } else {
            return [
                'id' => $this->id,
                'name' => $this->name,
                'products_count' => $this->whenLoaded('products', function () {
                    return $this->products->count();
                }),
                'products' => ProductResource::collection($this->whenLoaded('products')),
                'children' => SubCategoryResource::collection($this->whenLoaded('children')) ?? [],
            ];
        }

    }
}
