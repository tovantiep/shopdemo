<?php

namespace App\Http\Requests\Users;

use Illuminate\Foundation\Http\FormRequest;
use JetBrains\PhpStorm\ArrayShape;

class UserUpdateRequest extends FormRequest
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
            'email' => 'email|max:255|unique:users,email,' . $this->user->id,
            'avatar' => 'image|mimes:jpg,png,jpeg|max:2048',
        ];
    }

}
