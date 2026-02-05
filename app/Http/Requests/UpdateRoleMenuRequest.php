<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRoleMenuRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'role_id' => 'required|exists:roles,id',
            'menu_id' => [
                'required',
                'exists:menus,id',
                Rule::unique('role_menus')->where(function ($query) {
                    return $query->where('role_id', $this->role_id);
                })->ignore($this->route('role_menu'))
            ],
            'is_active' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'menu_id.unique' => 'This menu is already assigned to the role',
        ];
    }
}
