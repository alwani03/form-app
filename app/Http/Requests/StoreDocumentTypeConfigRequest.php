<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDocumentTypeConfigRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => 'required|string|max:255',
            'document_name' => 'required|string|max:255',
            'approval' => 'boolean',
            'setting' => 'nullable|array',
            'is_active' => 'integer|in:0,1',
        ];
    }
}
