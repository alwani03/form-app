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
        $incidents = $this->incidentService->index($request->all());
        return IncidentFormDetailResource::collection($incidents);
    }

    public function store(StoreIncidentFormDetailRequest $request)
    {
        $incident = $this->incidentService->create($request->validated());
        return new IncidentFormDetailResource($incident);
    }

    public function show($id)
    {
        $incident = $this->incidentService->find($id);
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
}
