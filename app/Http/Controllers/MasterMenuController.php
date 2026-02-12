<?php

namespace App\Http\Controllers;

use App\Services\MasterMenuService;
use App\Http\Requests\StoreMasterMenuRequest;
use App\Http\Requests\UpdateMasterMenuRequest;
use Illuminate\Http\Request;

class MasterMenuController extends Controller
{
    public function __construct(
        protected MasterMenuService $service
    ) {}

    public function index(Request $request)
    {
        $masterMenus = $this->service->paginate($request->search ?? null, 10, filter_var($request->header('X-Skip-Log'), FILTER_VALIDATE_BOOLEAN));

        return response()->json([
            'message' => 'Master Menus retrieved successfully',
            'data' => $masterMenus
        ]);
    }

    public function store(StoreMasterMenuRequest $request)
    {
        $masterMenu = $this->service->create($request->validated());

        return response()->json([
            'message' => 'Master Menu created successfully',
            'data' => $masterMenu
        ], 201);
    }

    public function show(Request $request, $id)
    {
        $masterMenu = $this->service->find($id, filter_var($request->header('X-Skip-Log'), FILTER_VALIDATE_BOOLEAN));

        if (!$masterMenu) {
            return response()->json(['message' => 'Master Menu not found'], 404);
        }

        return response()->json([
            'message' => 'Master Menu details',
            'data' => $masterMenu
        ]);
    }

    public function update(UpdateMasterMenuRequest $request, $id)
    {
        $masterMenu = $this->service->update($id, $request->validated());

        if (!$masterMenu) {
            return response()->json(['message' => 'Master Menu not found'], 404);
        }

        return response()->json([
            'message' => 'Master Menu updated successfully',
            'data' => $masterMenu
        ]);
    }

    public function destroy($id)
    {
        $deleted = $this->service->delete($id);

        if (!$deleted) {
            return response()->json(['message' => 'Master Menu not found'], 404);
        }

        return response()->json(['message' => 'Master Menu deleted successfully']);
    }
}
