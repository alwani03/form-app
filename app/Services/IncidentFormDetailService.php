<?php

namespace App\Services;

use App\Models\IncidentFormDetail;
use Illuminate\Support\Facades\DB;

class IncidentFormDetailService
{
    public function index($params)
    {
        $query = IncidentFormDetail::query();

        if (isset($params['report_no'])) {
            $query->where('report_no', 'like', '%' . $params['report_no'] . '%');
        }

        if (isset($params['incident_type'])) {
            $query->where('incident_type', $params['incident_type']);
        }

        if (isset($params['incident_status'])) {
            $query->where('incident_status', $params['incident_status']);
        }

        return $query->latest()->paginate($params['per_page'] ?? 10);
    }

    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {
            return IncidentFormDetail::create($data);
        });
    }

    public function find($id)
    {
        return IncidentFormDetail::findOrFail($id);
    }

    public function update($id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $incident = $this->find($id);
            $incident->update($data);
            return $incident;
        });
    }

    public function delete($id)
    {
        return DB::transaction(function () use ($id) {
            $incident = $this->find($id);
            $incident->delete();
            return $incident;
        });
    }
}
