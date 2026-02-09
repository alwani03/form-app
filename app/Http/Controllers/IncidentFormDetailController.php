<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreIncidentFormDetailRequest;
use App\Http\Requests\UpdateIncidentFormDetailRequest;
use App\Http\Resources\IncidentFormDetailResource;
use App\Services\IncidentFormDetailService;
use Illuminate\Http\Request;

class IncidentFormDetailController extends Controller
{
    protected $incidentService;

    public function __construct(IncidentFormDetailService $incidentService)
    {
        $this->incidentService = $incidentService;
    }

    public function index(Request $request)
    {
        $incidents = $this->incidentService->index($request->all(), $request->header('X-Skip-Log', false));
        return IncidentFormDetailResource::collection($incidents);
    }

    public function store(StoreIncidentFormDetailRequest $request)
    {
        $incident = $this->incidentService->create($request->validated());
        return new IncidentFormDetailResource($incident);
    }

    public function show(Request $request, $id)
    {
        $incident = $this->incidentService->find($id, filter_var($request->header('X-Skip-Log'), FILTER_VALIDATE_BOOLEAN));
        return new IncidentFormDetailResource($incident);
    }

    public function update(UpdateIncidentFormDetailRequest $request, $id)
    {
        $incident = $this->incidentService->update($id, $request->validated());
        return new IncidentFormDetailResource($incident);
    }

    public function destroy($id)
    {
        $this->incidentService->delete($id);
        return response()->json(['message' => 'Incident deleted successfully']);
    }

    public function process($id)
    {
        $incident = $this->incidentService->process($id);
        return new IncidentFormDetailResource($incident);
    }

    public function complete(Request $request, $id)
    {
        $request->validate([
            'incident_resolution' => 'required|string',
            'incident_root_cause' => 'nullable|string',
            'action_plan' => 'nullable|string',
        ]);

        $incident = $this->incidentService->complete($id, $request->all());
        return new IncidentFormDetailResource($incident);
    }
}
