<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMenuRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'master_menu_id' => 'required|exists:master_menus,id',
            'name'           => 'required|string|max:255|unique:menus,name',
            'icon'           => 'nullable|string|max:255',
            'ordering'       => 'nullable|integer',
            'url'            => 'required|string|max:255',
            'description'    => 'nullable|string',
            'is_active'      => 'boolean',
        ];
    }
}
