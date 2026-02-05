<?php

namespace App\Http\Controllers;

use App\Services\FormRequestService;
use App\Http\Requests\StoreFormRequestRequest;
use App\Http\Requests\UpdateFormRequestRequest;
use Illuminate\Http\Request;

class FormRequestController extends Controller
{
    public function __construct(
        protected FormRequestService $formRequestService
    ) {}

    public function index(Request $request)
    {
        $formRequests = $this->formRequestService->paginate($request->search ?? null, 10);

        return response()->json([
            'message' => 'Form Requests retrieved successfully',
            'data'    => $formRequests
        ]);
    }

    public function store(StoreFormRequestRequest $request)
    {
        $formRequest = $this->formRequestService->create($request->validated());

        return response()->json([
            'message' => 'Form Request created successfully',
            'data' => $formRequest
        ], 201);
    }

    public function show($id)
    {
        $formRequest = $this->formRequestService->find($id);

        if (!$formRequest) {
            return response()->json(['message' => 'Form Request not found'], 404);
        }

        return response()->json([
            'message' => 'Form Request details',
            'data' => $formRequest
        ]);
    }

    public function update(UpdateFormRequestRequest $request, $id)
    {
        $formRequest = $this->formRequestService->update($id, $request->validated());

        if (!$formRequest) {
            return response()->json(['message' => 'Form Request not found'], 404);
        }

        return response()->json([
            'message' => 'Form Request updated successfully',
            'data' => $formRequest
        ]);
    }

    public function destroy($id)
    {
        $deleted = $this->formRequestService->delete($id);

        if (!$deleted) {
            return response()->json(['message' => 'Form Request not found'], 404);
        }

        return response()->json([
            'message' => 'Form Request deleted successfully'
        ]);
    }
}
