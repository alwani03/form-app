<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMenuRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'           => ['required', 'string', 'max:255', Rule::unique('menus')->ignore($this->route('menu'))],
            'url'            => 'required|string|max:255',
            'description'    => 'nullable|string',
            'is_active'      => 'boolean',
        ];
    }
}
