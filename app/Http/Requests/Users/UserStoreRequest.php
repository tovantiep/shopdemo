<?php

namespace App\Http\Requests\Users;

use Illuminate\Foundation\Http\FormRequest;
use JetBrains\PhpStorm\ArrayShape;

class UserStoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    #[ArrayShape([])] public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'avatar' => 'required|image|mimes:jpg,png,jpeg|max:2048',
            'email' => 'required|string|max:255|email|unique:users,email',
            'password' => 'required|string|max:255|confirmed',
            'gender' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
        ];
    }
}
