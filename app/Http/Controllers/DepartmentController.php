<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\LogActivity;
use App\Enums\ActivityType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Department::query();
        $remark = ActivityType::LIST->generateRemark('Departments');

        if ($request->has('search')) {
            $query->where('department_name', 'like', '%' . $request->search . '%');
            $remark = ActivityType::SEARCH->generateRemark('Department', $request->search);
        }

        // Log Activity
        LogActivity::create([
            'user_id' => Auth::id(),
            'remark' => $remark,
        ]);

        $departments = $query->paginate(10);

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

        $department = Department::create([
            'department_name' => $request->department_name,
            'created_by' => Auth::id(),
        ]);

        // Log Activity
        LogActivity::create([
            'user_id' => Auth::id(),
            'remark' => ActivityType::CREATE->generateRemark('Department', $department->department_name),
        ]);

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
        $department = Department::find($id);

        if (!$department) {
            return response()->json(['message' => 'Department not found'], 404);
        }

        // Log Activity
        LogActivity::create([
            'user_id' => Auth::id(),
            'remark' => ActivityType::READ->generateRemark('Department', $department->department_name),
        ]);

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
        $department = Department::find($id);

        if (!$department) {
            return response()->json(['message' => 'Department not found'], 404);
        }

        $request->validate([
            'department_name' => 'required|string|max:255|unique:departments,department_name,' . $id,
        ]);

        $department->update([
            'department_name' => $request->department_name,
        ]);

        // Log Activity
        LogActivity::create([
            'user_id' => Auth::id(),
            'remark' => ActivityType::UPDATE->generateRemark('Department', $department->department_name),
        ]);

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
        $department = Department::find($id);

        if (!$department) {
            return response()->json(['message' => 'Department not found'], 404);
        }

        $department_name = $department->department_name;
        $department->delete();

        // Log Activity
        LogActivity::create([
            'user_id' => Auth::id(),
            'remark' => ActivityType::DELETE->generateRemark('Department', $department_name),
        ]);

        return response()->json([
            'message' => 'Department deleted successfully'
        ]);
    }
}
