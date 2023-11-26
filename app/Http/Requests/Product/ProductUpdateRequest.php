<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;
use JetBrains\PhpStorm\ArrayShape;

class ProductUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    #[ArrayShape([])] public function rules(): array
    {
        return [
            'name' => 'string|max:255|unique:products,name',
            'code' => 'string|max:20',
            'size' => 'array',
            'image' => 'image|mimes:jpg,png,jpeg|max:2048',
            'color' => 'string|max:255',
            'price' => 'integer',
            'price_discount' => 'integer',
            'quantity' => 'integer',
            'description' => 'string|max:255',
        ];
    }

}
