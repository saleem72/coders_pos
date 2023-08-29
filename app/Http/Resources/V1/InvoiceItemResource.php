<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "invoice_id" => $this->invoice_id,
            "product_id" => $this->product_id,
            "product" => $this->product->name,
            "price" => $this->price,
            "quantity" => $this->quantity,
            "notes" => $this->notes
        ];
    }
}
