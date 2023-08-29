<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
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
            'customer' => [
                'id' => $this->customer->id,
                'name' => $this->customer->name
            ],
            'invoiceDate' => $this->invoice_date,
            'tax' => $this->tax,
            'subtotal' => $this->subtotal,
            'notes' => $this->notes,
        ];
    }
}
