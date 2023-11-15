<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;
use JetBrains\PhpStorm\ArrayShape;

class ProductIndexRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    #[ArrayShape([])] public function rules(): array
    {
        return [
            'name' => 'string|max:255',
            'image' => 'image|mimes:jpg,png,jpeg|max:2048',
            'color' => 'string|max:255',
            'price' => 'integer',
            'quantity' => 'integer',
            'description' => 'string|max:255',
        ];
    }

}
