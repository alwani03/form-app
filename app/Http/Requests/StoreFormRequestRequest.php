<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFormRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // die('kena');
        return [
            'form_no'           => 'required|string|max:255|unique:form_requests,form_no',
            'form_name'         => 'required|string|max:255',
            'document_type_id'  => 'required|exists:document_type_configs,id',
            'type'              => 'nullable|array',
            'status'            => 'nullable|in:pending,process,finished,reject',
        ];
    }
}
