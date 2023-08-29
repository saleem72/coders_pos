<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryCounts extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $children_products_count = (int) $this->children_products_count ?? 0;
        $products_count = (int) $this->products_count ?? 0;
        $total = $children_products_count + $products_count ;
        return [
            'id' => $this->id,
            'name' => $this->name,
            'children_count' => $this->whenLoaded('children', function() {
                return $this->children->count();
            }),
            'children' => SubCategoryResource::collection($this->whenLoaded('children')),
            'products_count' => $this->whenLoaded('products', function() use($total) {
                return $total;
            }),
            'products' => ProductResource::collection($this->whenLoaded('products', function() {
               return $this->products->merge($this->children_products);
            })),
        ];
    }
}
