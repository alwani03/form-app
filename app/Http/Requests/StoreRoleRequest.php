<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'role' => 'required|string|max:255|unique:roles,role',
            'description' => 'nullable|string',
            'is_active' => 'integer|in:0,1',
        ];
    }
}
