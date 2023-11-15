<?php

namespace App\Http\Requests\FeedBack;

use Illuminate\Foundation\Http\FormRequest;
use JetBrains\PhpStorm\ArrayShape;

class FeedBackIndexRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    #[ArrayShape([])] public function rules(): array
    {
        return [
            'rating' => 'integer|min:1|max:5',
            'comment' => 'string|max:255',
        ];
    }

}
