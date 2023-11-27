<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;
use JetBrains\PhpStorm\ArrayShape;

class ProductStoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    #[ArrayShape([])] public function rules(): array
    {
        return [
            'category_id' => 'required|integer',
            'name' => 'required|string|max:255|unique:products,name',
            'code' => 'required|string|max:20',
            'size' => 'required|array',
            'color' => 'required|string|max:255',
            'price' => 'required|integer',
            'price_discount' => 'nullable|integer',
            'quantity' => 'required|integer',
            'description' => 'string|max:255',
        ];
    }
}
