<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\LogActivity;
use App\Enums\ActivityType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class MenuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Menu::query();
        $remark = ActivityType::LIST->generateRemark('Menus');

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('url', 'like', '%' . $request->search . '%');
            $remark = ActivityType::SEARCH->generateRemark('Menu', $request->search);
        }

        // Log Activity
        LogActivity::create([
            'user_id' => Auth::id(),
            'remark' => $remark,
        ]);

        $menus = $query->paginate(10);

        return response()->json([
            'message' => 'Menus retrieved successfully',
            'data' => $menus
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:menus,name',
            'url' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $menu = Menu::create([
            'name' => $request->name,
            'url' => $request->url,
            'description' => $request->description,
            'is_active' => $request->is_active ?? 1,
            'created_by' => Auth::id(),
        ]);

        // Log Activity
        LogActivity::create([
            'user_id' => Auth::id(),
            'remark' => ActivityType::CREATE->generateRemark('Menu', $menu->name),
        ]);

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
        $menu = Menu::find($id);

        if (!$menu) {
            return response()->json(['message' => 'Menu not found'], 404);
        }

        // Log Activity
        LogActivity::create([
            'user_id' => Auth::id(),
            'remark' => ActivityType::READ->generateRemark('Menu', $menu->name),
        ]);

        return response()->json([
            'message' => 'Menu details',
            'data' => $menu
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $menu = Menu::find($id);

        if (!$menu) {
            return response()->json(['message' => 'Menu not found'], 404);
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('menus')->ignore($id)],
            'url' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $menu->update([
            'name' => $request->name,
            'url' => $request->url,
            'description' => $request->description,
            'is_active' => $request->is_active ?? $menu->is_active,
            'updated_by' => Auth::id(),
        ]);

        // Log Activity
        LogActivity::create([
            'user_id' => Auth::id(),
            'remark' => ActivityType::UPDATE->generateRemark('Menu', $menu->name),
        ]);

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
        $menu = Menu::find($id);

        if (!$menu) {
            return response()->json(['message' => 'Menu not found'], 404);
        }

        $menuName = $menu->name;
        
        // Update deleted_by before deleting
        $menu->update(['deleted_by' => Auth::id()]);
        $menu->delete();

        // Log Activity
        LogActivity::create([
            'user_id' => Auth::id(),
            'remark' => ActivityType::DELETE->generateRemark('Menu', $menuName),
        ]);

        return response()->json([
            'message' => 'Menu deleted successfully'
        ]);
    }
}
