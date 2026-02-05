<?php

namespace App\Http\Controllers;

use App\Services\MenuService;
use App\Http\Requests\StoreMenuRequest;
use App\Http\Requests\UpdateMenuRequest;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function __construct(
        protected MenuService $menuService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $menus = $this->menuService->paginate($request->search ?? null, 10);

        return response()->json([
            'message' => 'Menus retrieved successfully',
            'data' => $menus
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMenuRequest $request)
    {
        $menu = $this->menuService->create($request->validated());

        return response()->json([
            'message' => 'Menu created successfully',
            'data' => $menu
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $menu = $this->menuService->find($id);

        if (!$menu) {
            return response()->json(['message' => 'Menu not found'], 404);
        }

        return response()->json([
            'message' => 'Menu details',
            'data' => $menu
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMenuRequest $request, $id)
    {
        $menu = $this->menuService->update($id, $request->validated());

        if (!$menu) {
            return response()->json(['message' => 'Menu not found'], 404);
        }

        return response()->json([
            'message' => 'Menu updated successfully',
            'data' => $menu
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $deleted = $this->menuService->delete($id);

        if (!$deleted) {
            return response()->json(['message' => 'Menu not found'], 404);
        }

        return response()->json([
            'message' => 'Menu deleted successfully'
        ]);
    }
}
