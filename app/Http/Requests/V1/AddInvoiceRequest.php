<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class AddInvoiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'customer_id' => ['required', 'exists:customers,id'],
            'number' => ['nullable'],
            'invoice_date' => ['required', 'date', 'before:tomorrow', 'date_format:d-m-Y H:i:s'],
            'tax' => ['required'],
            'notes' => ['nullable'],
            'items' => ['required'],
            'items.*.productId' => ['required', 'exists:products,id'],
            'items.*.price' => ['required', 'numeric'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.notes' => ['nullable'],
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        $data = [];
        if (request()->has('items')) {
            foreach ($this->items as $item) {
                $item['product_id'] = $item['productId'] ?? null;

                $data[] = $item;
             }
        }

        $temp = count($data) == 0 ? Null : $data;
        $this->merge([
            'customer_id' => $this->customerId ?? Null,
            'invoice_date' => $this->invoiceDate ?? Null,
            'invoice_items' => $temp
        ]);
    }
}


