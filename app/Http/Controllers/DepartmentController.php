<?php

namespace App\Http\Controllers;

use App\Enums\ActivityType;
use App\Services\LogActivityService;
use App\Services\DepartmentService;
use App\Http\Requests\StoreDepartmentRequest;
use App\Http\Requests\UpdateDepartmentRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DepartmentController extends Controller
{
    public function __construct(
        protected LogActivityService $logActivityService,
        protected DepartmentService $departmentService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $departments = $this->departmentService->paginate($request->search ?? null, 10, filter_var($request->header('X-Skip-Log'), FILTER_VALIDATE_BOOLEAN));

        return response()->json([
            'message' => 'Departments retrieved successfully',
            'data' => $departments
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDepartmentRequest $request)
    {
        $validated = $request->validated();
        $department = $this->departmentService->create(
            $validated['department_name'],
            $validated['department_head_id'] ?? null
        );

        return response()->json([
            'message' => 'Department created successfully',
            'data' => $department
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, $id)
    {
        $department = $this->departmentService->find($id, filter_var($request->header('X-Skip-Log'), FILTER_VALIDATE_BOOLEAN));

        if (!$department) {
            return response()->json(['message' => 'Department not found'], 404);
        }

        return response()->json([
            'message' => 'Department details',
            'data' => $department
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDepartmentRequest $request, $id)
    {
        $validated = $request->validated();
        $department = $this->departmentService->update(
            $id,
            $validated['department_name'],
            $validated['department_head_id'] ?? null
        );

        if (!$department) {
            return response()->json(['message' => 'Department not found'], 404);
        }

        return response()->json([
            'message' => 'Department updated successfully',
            'data' => $department
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $deleted = $this->departmentService->delete($id);

        if (!$deleted) {
            return response()->json(['message' => 'Department not found'], 404);
        }

        return response()->json([
            'message' => 'Department deleted successfully'
        ]);
    }
}
