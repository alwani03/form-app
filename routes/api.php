<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\RoleMenuController;
use App\Http\Controllers\FormRequestController;
use App\Http\Controllers\DocumentTypeConfigController;
use App\Http\Controllers\MasterMenuController;
use App\Http\Controllers\IncidentFormDetailController;
use App\Http\Controllers\LogActivityController;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/logout', [AuthController::class, 'logout']);

    // Department Routes
    Route::apiResource('departments', DepartmentController::class);

    // Role Routes
    Route::apiResource('roles', RoleController::class);

    // User Routes
    Route::apiResource('users', UserController::class);

    // Menu Routes
    Route::apiResource('menus', MenuController::class);

    // Role Menu Routes
    Route::apiResource('role-menus', RoleMenuController::class);

    // Form Request Routes
    Route::apiResource('form-requests', FormRequestController::class);

    // Document Type Config Routes
    Route::apiResource('document-type-configs', DocumentTypeConfigController::class);
    
    // Master Menu Routes
    Route::apiResource('master-menus', MasterMenuController::class);
    

    // Incident Form Detail Routes
    Route::put('incident-form-details/{id}/process', [IncidentFormDetailController::class, 'process']);
    Route::put('incident-form-details/{id}/complete', [IncidentFormDetailController::class, 'complete']);
    Route::apiResource('incident-form-details', IncidentFormDetailController::class);

    // Log Activity Routes
    Route::get('log-activities', [LogActivityController::class, 'index']);
});
