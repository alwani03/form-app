<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'username'        => ['required', 'string', 'max:255', Rule::unique('users')->ignore($this->route('user'))],
            'email'           => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($this->route('user'))],
            'role_id'         => 'required|exists:roles,id',
            'department_id'   => 'required|exists:departments,id',
            'is_active'       => 'boolean',
            'password'        => 'nullable|string|min:6'
        ];
    }
}
