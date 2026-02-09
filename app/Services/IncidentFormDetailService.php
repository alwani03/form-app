<?php

namespace App\Services;

use App\Models\IncidentFormDetail;
use App\Enums\ActivityType;
use Illuminate\Support\Facades\DB;

class IncidentFormDetailService
{
    public function __construct(
        protected LogActivityService $logActivityService
    ) {}

    public function index($params, bool $skipLog = false)
    {
        $query = IncidentFormDetail::query()->with('formRequest');
        $remark = $this->logActivityService->generateRemark(ActivityType::LIST, 'Incident Form Details');
        $searchDetails = [];

        if (isset($params['report_no'])) {
            $query->where('report_no', 'like', '%' . $params['report_no'] . '%');
            $searchDetails[] = "Report No: {$params['report_no']}";
        }

        if (isset($params['incident_type'])) {
            $query->where('incident_type', $params['incident_type']);
            $searchDetails[] = "Type: {$params['incident_type']}";
        }

        if (isset($params['incident_status'])) {
            $query->where('incident_status', $params['incident_status']);
            $searchDetails[] = "Status: {$params['incident_status']}";
        }

        if (!empty($searchDetails)) {
            $remark = $this->logActivityService->generateRemark(ActivityType::SEARCH, 'Incident Form Detail', implode(', ', $searchDetails));
        }

        if (!$skipLog) {
            $this->logActivityService->log($remark);
        }

        return $query->latest()->paginate($params['per_page'] ?? 10);
    }

    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {
            // Ensure initial status
            $data['incident_status'] = $data['incident_status'] ?? '03'; // Default to Not Closed

            $incident = IncidentFormDetail::create($data);
            
            // If form_id exists, ensure its status is pending (optional, but good for consistency)
            if ($incident->formRequest) {
                $incident->formRequest->update(['status' => 'pending']);
            }

            $remark = $this->logActivityService->generateRemark(ActivityType::CREATE, 'Incident Form Detail', "Report No: {$incident->report_no}");
            $this->logActivityService->log($remark);

            return $incident;
        });
    }

    public function find($id, bool $skipLog = false)
    {
        $incident = IncidentFormDetail::with('formRequest')->findOrFail($id);

        if (!$skipLog) {
            $this->logActivityService->log(
                $this->logActivityService->generateRemark(ActivityType::READ, 'Incident Form Detail', "Report No: {$incident->report_no}")
            );
        }

        return $incident;
    }

    public function update($id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $incident = $this->find($id);
            $incident->update($data);

            $remark = $this->logActivityService->generateRemark(ActivityType::UPDATE, 'Incident Form Detail', "Report No: {$incident->report_no}");
            $this->logActivityService->log($remark);

            return $incident;
        });
    }

    public function delete($id)
    {
        return DB::transaction(function () use ($id) {
            $incident = $this->find($id);
            $reportNo = $incident->report_no;
            $incident->delete();

            $remark = $this->logActivityService->generateRemark(ActivityType::DELETE, 'Incident Form Detail', "Report No: {$reportNo}");
            $this->logActivityService->log($remark);

            return $incident;
        });
    }

    public function process($id)
    {
        return DB::transaction(function () use ($id) {
            $incident = $this->find($id);
            
            // Update incident status to Progress (02)
            $incident->update([
                'incident_status' => '02'
            ]);

            // Update form request status to process
            if ($incident->formRequest) {
                $incident->formRequest->update(['status' => 'process']);
            }

            $remark = $this->logActivityService->generateRemark(ActivityType::UPDATE, 'Incident Form Detail', "Process Report No: {$incident->report_no}");
            $this->logActivityService->log($remark);

            return $incident;
        });
    }

    public function complete($id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $incident = $this->find($id);
            
            // Update incident status to Closed (01)
            $incident->update([
                'incident_status' => '01',
                'resolved_at' => now(),
                'incident_resolution' => $data['incident_resolution'] ?? $incident->incident_resolution,
                'incident_root_cause' => $data['incident_root_cause'] ?? $incident->incident_root_cause,
                'action_plan' => $data['action_plan'] ?? $incident->action_plan,
            ]);

            // Update form request status to finished
            if ($incident->formRequest) {
                $incident->formRequest->update(['status' => 'finished']);
            }

            $remark = $this->logActivityService->generateRemark(ActivityType::UPDATE, 'Incident Form Detail', "Complete Report No: {$incident->report_no}");
            $this->logActivityService->log($remark);

            return $incident;
        });
    }
}
