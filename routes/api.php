<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\RoleMenuController;

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
});
