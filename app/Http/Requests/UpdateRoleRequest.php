<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'role' => ['required', 'string', 'max:255', Rule::unique('roles')->ignore($this->route('role'))],
            'description' => 'nullable|string',
            'is_active' => 'integer|in:0,1',
        ];
    }
}
