<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\LogActivity;
use App\Enums\ActivityType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Role::query();
        $remark = ActivityType::LIST->generateRemark('Roles');

        if ($request->has('search')) {
            $query->where('role', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            $remark = ActivityType::SEARCH->generateRemark('Role', $request->search);
        }

        // Log Activity
        LogActivity::create([
            'user_id' => Auth::id(),
            'remark' => $remark,
        ]);

        $roles = $query->paginate(10);

        return response()->json([
            'message' => 'Roles retrieved successfully',
            'data' => $roles
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'role' => 'required|string|max:255|unique:roles,role',
            'description' => 'nullable|string',
            'is_active' => 'integer|in:0,1',
        ]);

        $role = Role::create([
            'role' => $request->role,
            'description' => $request->description,
            'is_active' => $request->is_active ?? 1,
            'created_by' => Auth::id(),
        ]);

        // Log Activity
        LogActivity::create([
            'user_id' => Auth::id(),
            'remark' => ActivityType::CREATE->generateRemark('Role', $role->role),
        ]);

        return response()->json([
            'message' => 'Role created successfully',
            'data' => $role
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $role = Role::find($id);

        if (!$role) {
            return response()->json(['message' => 'Role not found'], 404);
        }

        // Log Activity
        LogActivity::create([
            'user_id' => Auth::id(),
            'remark' => ActivityType::READ->generateRemark('Role', $role->role),
        ]);

        return response()->json([
            'message' => 'Role details',
            'data' => $role
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $role = Role::find($id);

        if (!$role) {
            return response()->json(['message' => 'Role not found'], 404);
        }

        $request->validate([
            'role' => 'required|string|max:255|unique:roles,role,' . $id,
            'description' => 'nullable|string',
            'is_active' => 'integer|in:0,1',
        ]);

        $role->update([
            'role' => $request->role,
            'description' => $request->description,
            'is_active' => $request->has('is_active') ? $request->is_active : $role->is_active,
            'updated_by' => Auth::id(),
        ]);

        // Log Activity
        LogActivity::create([
            'user_id' => Auth::id(),
            'remark' => ActivityType::UPDATE->generateRemark('Role', $role->role),
        ]);

        return response()->json([
            'message' => 'Role updated successfully',
            'data' => $role
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $role = Role::find($id);

        if (!$role) {
            return response()->json(['message' => 'Role not found'], 404);
        }

        $role_name = $role->role;

        // Optional: Set deleted_by before deleting
        $role->update(['deleted_by' => Auth::id()]);
        $role->delete();

        // Log Activity
        LogActivity::create([
            'user_id' => Auth::id(),
            'remark' => ActivityType::DELETE->generateRemark('Role', $role_name),
        ]);

        return response()->json([
            'message' => 'Role deleted successfully'
        ]);
    }
}
