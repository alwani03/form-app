<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateFormRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'form_no' => [
                'required',
                'string',
                'max:255',
                Rule::unique('form_requests')->ignore($this->route('form_request'))
            ],
            'form_name'             => 'required|string|max:255',
            'document_type_id'      => 'required|exists:document_type_configs,id',
            'type'                  => 'nullable|array',
            'status'                => 'nullable|in:pending,process,finished,reject',
        ];
    }
}
