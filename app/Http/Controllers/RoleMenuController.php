<?php

namespace App\Http\Controllers;

use App\Services\RoleMenuService;
use App\Http\Requests\StoreRoleMenuRequest;
use App\Http\Requests\UpdateRoleMenuRequest;
use Illuminate\Http\Request;

class RoleMenuController extends Controller
{
    public function __construct(
        protected RoleMenuService $roleMenuService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $roleMenus = $this->roleMenuService->paginate(
            $request->search ?? null,
            $request->role_id ?? null,
            10
        );

        return response()->json([
            'message' => 'Role Menus retrieved successfully',
            'data' => $roleMenus
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRoleMenuRequest $request)
    {
        $roleMenu = $this->roleMenuService->create($request->validated());

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
        $roleMenu = $this->roleMenuService->find($id);

        if (!$roleMenu) {
            return response()->json(['message' => 'Role Menu not found'], 404);
        }

        return response()->json([
            'message' => 'Role Menu details',
            'data' => $roleMenu
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRoleMenuRequest $request, $id)
    {
        $roleMenu = $this->roleMenuService->update($id, $request->validated());

        if (!$roleMenu) {
            return response()->json(['message' => 'Role Menu not found'], 404);
        }

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
        $deleted = $this->roleMenuService->delete($id);

        if (!$deleted) {
            return response()->json(['message' => 'Role Menu not found'], 404);
        }

        return response()->json([
            'message' => 'Role Menu deleted successfully'
        ]);
    }
}
