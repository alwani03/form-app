<?php

namespace App\Http\Controllers;

use App\Services\LogActivityService;
use Illuminate\Http\Request;

class LogActivityController extends Controller
{
    public function __construct(
        protected LogActivityService $logActivityService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $logs = $this->logActivityService->paginate(
            $request->search ?? null,
            10,
            filter_var($request->header('X-Skip-Log'), FILTER_VALIDATE_BOOLEAN)
        );

        return response()->json([
            'message' => 'Log Activities retrieved successfully',
            'data' => $logs
        ]);
    }
}
