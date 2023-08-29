<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubCategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'parentId' =>  $this->parent_id,
            'name' => $this->name,
            'parent' => $this->parent_id ? $this->whenLoaded('parent') : NULL,
            'products' => $this->whenLoaded('products', function() {
                return $this->products->count();
            })
        ];
    }
}
