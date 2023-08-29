<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInvoiceRequest extends FormRequest
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
            'customer_id' => ['sometimes', 'nullable', 'exists:customers,id'],
            'number' => ['nullable', 'sometimes'],
            'invoice_date' => ['sometimes', 'nullable', 'date', 'before:tomorrow', 'date_format:d-m-Y H:i:s'],
            'tax' => ['sometimes'],
            'notes' => ['nullable', 'sometimes'],
            'items' => ['sometimes'],
            'items.*.productId' => ['sometimes', 'exists:products,id'],
            'items.*.price' => ['sometimes', 'numeric'],
            'items.*.quantity' => ['sometimes', 'integer', 'min:1'],
            'items.*.notes' => ['nullable','sometimes'],
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {

        $items = [];

        if (request()->has('items')) {
            $data = [];
            foreach ($this->items as $item) {
                $item['product_id'] = $item['productId'] ?? null;

                $data[] = $item;
             }
             $items['invoice_items'] =  $data;
        }

        if (request()->has('customerId')) {
            $items['customer_id'] = $this->customerId;
        }

        if (request()->has('invoiceDate')) {
            $items['invoice_date'] = $this->invoiceDate;
        }

        $this -> merge($items);
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
// use trans instead on Lang
        return [
            'invoice_date.date_format' => 'The :attribute field must match the format d-m-Y H:i:s, some thing like 13-08-2023 05:06:03',
            // .
            // 'oldpassword.required' => Lang::get('userpasschange.oldpasswordrequired'),
        ];
    }
}
