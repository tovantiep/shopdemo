<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;
use JetBrains\PhpStorm\ArrayShape;

class OrderStoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    #[ArrayShape([])] public function rules(): array
    {
        return [
            'user_id' => 'required|integer',
            'total_amount' => 'required|integer',
            'total_quantity' => 'required|integer',
            'status' => 'string|in:0,1,2',
            'code' => 'string',
        ];
    }
}
