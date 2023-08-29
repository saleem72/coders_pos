<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'category' => $this->whenLoaded('category', function() {
                return $this->category->name;
            }),
            'unit' => $this->whenLoaded('unit', function() {
                return $this->unit->name;
            }),
            'image' => $this->image ? [
                'small' => asset('storage/products/images/'.$this->image.'_small.'.$this->image_extension),
                'medium' => asset('storage/products/images/'.$this->image.'_medium.'.$this->image_extension),
                'large' => asset('storage/products/images/'.$this->image.'_large.'.$this->image_extension),
            ] : Null,

        ];
    }
}
