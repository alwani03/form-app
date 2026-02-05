<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMasterMenuRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:master_menus,name'],
            'icon' => ['nullable', 'string', 'max:255'],
            'ordering' => ['nullable', 'integer'],
            'is_active' => 'boolean',
        ];
    }
}
