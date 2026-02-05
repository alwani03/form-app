<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class IncidentFormDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                                    => $this->id,
            'user_id'                               => $this->user_id,
            'user'                                  => $this->user, // Assuming relationship exists
            'form_id'                               => $this->form_id,
            'report_no'                             => $this->report_no,
            'destination_group'                     => $this->destination_group,
            'incident_date'                         => $this->incident_date,
            'incident_desc'                         => $this->incident_desc,
            'incident_type'                         => $this->incident_type,
            'impact_description'                    => $this->impact_description,
            'pic_user_id'                           => $this->pic_user_id,
            'incident_root_cause'                   => $this->incident_root_cause,
            'action_plan'                           => $this->action_plan,
            'incident_resolution'                   => $this->incident_resolution,
            'incident_status'                       => $this->incident_status,
            'approve_signature_id_department'       => $this->approve_signature_id_department,
            'resolved_at'                           => $this->resolved_at,
            'subject'                               => $this->subject,
            'summary'                               => $this->summary,
            'chronology'                            => $this->chronology,
            'actions_taken'                         => $this->actions_taken,
            'conclusion'                            => $this->conclusion,
            'status_remarks'                        => $this->status_remarks,
            'created_at'                            => $this->created_at,
            'updated_at'                            => $this->updated_at,
            'deleted_at'                            => $this->deleted_at,
        ];
    }
}
