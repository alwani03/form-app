<?php

namespace App\Http\Controllers;

use App\Models\RoleMenu;
use App\Models\LogActivity;
use App\Enums\ActivityType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class RoleMenuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = RoleMenu::query()->with(['role', 'menu']);
        $remark = ActivityType::LIST->generateRemark('Role Menus');

        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('role', function ($q) use ($search) {
                $q->where('role', 'like', '%' . $search . '%');
            })->orWhereHas('menu', function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%');
            });
            $remark = ActivityType::SEARCH->generateRemark('Role Menu', $search);
        }

        if ($request->has('role_id')) {
            $query->where('role_id', $request->role_id);
        }

        // Log Activity
        LogActivity::create([
            'user_id' => Auth::id(),
            'remark' => $remark,
        ]);

        $roleMenus = $query->paginate(10);

        return response()->json([
            'message' => 'Role Menus retrieved successfully',
            'data' => $roleMenus
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id',
            'menu_id' => 'required|exists:menus,id',
            'is_active' => 'boolean',
        ]);

        // Check if combination already exists
        $exists = RoleMenu::where('role_id', $request->role_id)
            ->where('menu_id', $request->menu_id)
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'This menu is already assigned to the role'], 422);
        }

        $roleMenu = RoleMenu::create([
            'role_id' => $request->role_id,
            'menu_id' => $request->menu_id,
            'is_active' => $request->is_active ?? 1,
            'created_by' => Auth::id(),
        ]);

        // Log Activity
        // We load relations to get names for the log
        $roleMenu->load(['role', 'menu']);
        $details = "Role: {$roleMenu->role->role}, Menu: {$roleMenu->menu->name}";
        
        LogActivity::create([
            'user_id' => Auth::id(),
            'remark' => ActivityType::CREATE->generateRemark('Role Menu', $details),
        ]);

        return response()->json([
            'message' => 'Role Menu created successfully',
            'data' => $roleMenu
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $roleMenu = RoleMenu::with(['role', 'menu'])->find($id);

        if (!$roleMenu) {
            return response()->json(['message' => 'Role Menu not found'], 404);
        }

        // Log Activity
        $details = "Role: {$roleMenu->role->role}, Menu: {$roleMenu->menu->name}";
        LogActivity::create([
            'user_id' => Auth::id(),
            'remark' => ActivityType::READ->generateRemark('Role Menu', $details),
        ]);

        return response()->json([
            'message' => 'Role Menu details',
            'data' => $roleMenu
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $roleMenu = RoleMenu::find($id);

        if (!$roleMenu) {
            return response()->json(['message' => 'Role Menu not found'], 404);
        }

        $request->validate([
            'role_id' => 'required|exists:roles,id',
            'menu_id' => 'required|exists:menus,id',
            'is_active' => 'boolean',
        ]);

        // Check for duplicates if role_id or menu_id changed
        if ($request->role_id != $roleMenu->role_id || $request->menu_id != $roleMenu->menu_id) {
            $exists = RoleMenu::where('role_id', $request->role_id)
                ->where('menu_id', $request->menu_id)
                ->where('id', '!=', $id)
                ->exists();
            
            if ($exists) {
                return response()->json(['message' => 'This menu is already assigned to the role'], 422);
            }
        }

        $roleMenu->update([
            'role_id' => $request->role_id,
            'menu_id' => $request->menu_id,
            'is_active' => $request->is_active ?? $roleMenu->is_active,
            'updated_by' => Auth::id(),
        ]);

        // Log Activity
        $roleMenu->load(['role', 'menu']);
        $details = "Role: {$roleMenu->role->role}, Menu: {$roleMenu->menu->name}";
        
        LogActivity::create([
            'user_id' => Auth::id(),
            'remark' => ActivityType::UPDATE->generateRemark('Role Menu', $details),
        ]);

        return response()->json([
            'message' => 'Role Menu updated successfully',
            'data' => $roleMenu
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $roleMenu = RoleMenu::with(['role', 'menu'])->find($id);

        if (!$roleMenu) {
            return response()->json(['message' => 'Role Menu not found'], 404);
        }

        $details = "Role: {$roleMenu->role->role}, Menu: {$roleMenu->menu->name}";
        
        // Update deleted_by before deleting
        $roleMenu->update(['deleted_by' => Auth::id()]);
        $roleMenu->delete();

        // Log Activity
        LogActivity::create([
            'user_id' => Auth::id(),
            'remark' => ActivityType::DELETE->generateRemark('Role Menu', $details),
        ]);

        return response()->json([
            'message' => 'Role Menu deleted successfully'
        ]);
    }
}
