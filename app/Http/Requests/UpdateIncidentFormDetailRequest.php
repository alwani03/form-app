<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateIncidentFormDetailRequest extends FormRequest
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
            'user_id'                               => ['sometimes', 'exists:users,id'],
            'form_id'                               => ['sometimes', 'integer'],
            'report_no'                             => ['sometimes', 'string', 'max:30'],
            'destination_group'                     => ['nullable', 'integer'],
            'incident_date'                         => ['nullable', 'date'],
            'incident_desc'                         => ['nullable', 'string'],
            'incident_type'                         => ['sometimes', 'integer'],
            'impact_description'                    => ['nullable', 'string'],
            'pic_user_id'                           => ['nullable', 'integer'],
            'incident_root_cause'                   => ['nullable', 'string'],
            'action_plan'                           => ['nullable', 'string'],
            'incident_resolution'                   => ['nullable', 'string'],
            'incident_status'                       => ['sometimes', 'string'],
            'approve_signature_id_department'       => ['nullable', 'integer'],
            'resolved_at'                           => ['nullable', 'date'],
            'subject'                               => ['nullable', 'string', 'max:255'],
            'summary'                               => ['nullable', 'string'],
            'chronology'                            => ['nullable', 'string'],
            'actions_taken'                         => ['nullable', 'string'],
            'conclusion'                            => ['nullable', 'string'],
            'status_remarks'                        => ['nullable', 'string', 'max:255'],
        ];
    }
}
