<?php

namespace App\Http\Controllers;

use App\Enums\ActivityType;
use App\Services\LogActivityService;
use App\Services\DepartmentService;
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
        $departments = $this->departmentService->paginate($request->search ?? null, 10);

        return response()->json([
            'message' => 'Departments retrieved successfully',
            'data' => $departments
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'department_name' => 'required|string|max:255|unique:departments,department_name',
        ]);

        $department = $this->departmentService->create($request->department_name);

        return response()->json([
            'message' => 'Department created successfully',
            'data' => $department
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $department = $this->departmentService->find($id);

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
    public function update(Request $request, $id)
    {
        $request->validate([
            'department_name' => 'required|string|max:255|unique:departments,department_name,' . $id,
        ]);

        $department = $this->departmentService->update($id, [
            'department_name' => $request->department_name
        ]);

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
