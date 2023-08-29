<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class AddProductRequest extends FormRequest
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
            'categoryId' => ['required', 'exists:categories,id'],
            'unitId' => ['required', 'exists:units,id'],
            'name' => ['required'],
            'purchase' => ['required', 'numeric'],
            'retail' => ['required', 'numeric'],
            'quantity' => ['required', 'integer'],
            'barcode' => ['required'],
            'image' => ['bail', 'sometimes', 'file', 'mimes:jpg,jpeg']
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        if (request()->has('categoryId')) {
            $this->merge([
                'category_id' => $this->categoryId,
            ]);
        }

        if (request()->has('unitId')) {
            $this->merge([
                'unit_id' => $this->unitId,
            ]);
        }
    }
}
