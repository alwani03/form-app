<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMasterMenuRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('master_menus', 'name')->ignore($this->route('master_menu'))
            ],
            'icon' => ['nullable', 'string', 'max:255'],
            'ordering' => ['nullable', 'integer'],
            'is_active' => 'boolean',
        ];
    }
}
