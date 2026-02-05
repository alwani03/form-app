<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreIncidentFormDetailRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'user_id'                       => ['required', 'exists:users,id'],
            'form_id'                       => ['required', 'integer'],
            'report_no'                     => ['required', 'string', 'max:30'],
            'destination_group'             => ['nullable', 'integer'],
            'incident_date'                 => ['nullable', 'date'],
            'incident_desc'                 => ['nullable', 'string'],
            'incident_type'                 => ['required', 'integer'],
            'impact_description'            => ['nullable', 'string'],
            'pic_user_id'                   => ['nullable', 'integer'],
            'incident_root_cause'           => ['nullable', 'string'],
            'action_plan'                   => ['nullable', 'string'],
            'incident_resolution'           => ['nullable', 'string'],
            'incident_status'               => ['required', 'string'],
            'approve_signature_id_department' => ['nullable', 'integer'],
            'resolved_at'                   => ['nullable', 'date'],
            'subject'                       => ['nullable', 'string', 'max:255'],
            'summary'                       => ['nullable', 'string'],
            'chronology'                    => ['nullable', 'string'],
            'actions_taken'                 => ['nullable', 'string'],
            'conclusion'                    => ['nullable', 'string'],
            'status_remarks'                => ['nullable', 'string', 'max:255'],
        ];
    }
}
