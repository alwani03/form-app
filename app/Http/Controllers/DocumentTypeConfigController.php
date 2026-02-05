<?php

namespace App\Http\Controllers;

use App\Services\DocumentTypeConfigService;
use App\Http\Requests\StoreDocumentTypeConfigRequest;
use App\Http\Requests\UpdateDocumentTypeConfigRequest;
use Illuminate\Http\Request;

class DocumentTypeConfigController extends Controller
{
    public function __construct(
        protected DocumentTypeConfigService $service
    ) {}

    public function index(Request $request)
    {
        $configs = $this->service->paginate($request->search ?? null, 10);

        return response()->json([
            'message' => 'Document Type Configs retrieved successfully',
            'data' => $configs
        ]);
    }

    public function store(StoreDocumentTypeConfigRequest $request)
    {
        $config = $this->service->create($request->validated());

        return response()->json([
            'message' => 'Document Type Config created successfully',
            'data' => $config
        ], 201);
    }

    public function show($id)
    {
        $config = $this->service->find($id);

        if (!$config) {
            return response()->json(['message' => 'Document Type Config not found'], 404);
        }

        return response()->json([
            'message' => 'Document Type Config details',
            'data' => $config
        ]);
    }

    public function update(UpdateDocumentTypeConfigRequest $request, $id)
    {
        $config = $this->service->update($id, $request->validated());

        if (!$config) {
            return response()->json(['message' => 'Document Type Config not found'], 404);
        }

        return response()->json([
            'message' => 'Document Type Config updated successfully',
            'data' => $config
        ]);
    }

    public function destroy($id)
    {
        $deleted = $this->service->delete($id);

        if (!$deleted) {
            return response()->json(['message' => 'Document Type Config not found'], 404);
        }

        return response()->json([
            'message' => 'Document Type Config deleted successfully'
        ]);
    }
}
