<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;
use JetBrains\PhpStorm\ArrayShape;

class OrderIndexRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    #[ArrayShape([])] public function rules(): array
    {
        return [
            'order_item_id' => 'array',
            'user_id' => 'integer',
            'total_amount' => 'integer',
            'total_quantity' => 'integer',
            'status' => 'boolean',
            'order_item_id.*' => 'integer',
        ];
    }

}
