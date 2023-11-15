<?php

namespace App\Http\Requests\Users;

use Illuminate\Foundation\Http\FormRequest;
use JetBrains\PhpStorm\ArrayShape;

class UserIndexRequest extends FormRequest
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
            'email' => 'email|max:255',
        ];
    }

    /**
     * Customize messages
     *
     * @return array
     */
    #[ArrayShape([])] public function messages(): array
    {
        return [
            'email' => ':attributeはメールアドレスでなければなりません。',
        ];
    }

    /**
     * Customize Attributes
     *
     * @return string[]
     */
    #[ArrayShape([])] public function attributes(): array
    {
        return [
            'name' => 'なまえ',
            'email' => 'メールアドレス'
        ];
    }
}
