<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'username'           => 'required|string|max:255|unique:users',
            'password'           => 'required|string|min:6',
            'email'              => 'required|string|email|max:255|unique:users',
            'role_id'            => 'required|exists:roles,id',
            'department_id'      => 'required|exists:departments,id',
            'is_active'          => 'boolean'
        ];
    }
}
