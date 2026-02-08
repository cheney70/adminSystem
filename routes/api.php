<?php

use Illuminate\Support\Facades\Route;
use Cheney\AdminSystem\Controllers\AuthController;
use Cheney\AdminSystem\Controllers\UserController;
use Cheney\AdminSystem\Controllers\RoleController;
use Cheney\AdminSystem\Controllers\PermissionController;
use Cheney\AdminSystem\Controllers\MenuController;
use Cheney\AdminSystem\Controllers\OperationLogController;

Route::prefix('api/system')->middleware('cors')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('login', [AuthController::class, 'login']);
        Route::post('logout', [AuthController::class, 'logout'])->middleware('jwt');
        Route::post('refresh', [AuthController::class, 'refresh'])->middleware('jwt');
        Route::get('me', [AuthController::class, 'me'])->middleware('jwt');
        Route::put('profile', [AuthController::class, 'updateProfile'])->middleware('jwt');
        Route::post('change-password', [AuthController::class, 'changePassword'])->middleware('jwt');
    });

    Route::middleware('jwt')->group(function () {
        Route::apiResource('admins', UserController::class);
        Route::post('admins/{id}/roles', [UserController::class, 'assignRoles']);
        Route::post('admins/{id}/reset-password', [UserController::class, 'resetPassword']);
        Route::post('admins/{id}/change-status', [UserController::class, 'changeStatus']);

        Route::apiResource('roles', RoleController::class);
        Route::post('roles/{id}/permissions', [RoleController::class, 'assignPermissions']);
        Route::post('roles/{id}/admins', [RoleController::class, 'assignAdmins']);

        Route::apiResource('permissions', PermissionController::class);

        Route::apiResource('menus', MenuController::class);
        Route::get('menus/tree', [MenuController::class, 'tree']);
        Route::get('menus/user', [MenuController::class, 'userMenus']);

        Route::apiResource('operation-logs', OperationLogController::class)->only(['index', 'show', 'destroy']);
        Route::post('operation-logs/batch', [OperationLogController::class, 'batchDestroy']);
        Route::delete('operation-logs/clear', [OperationLogController::class, 'clear']);
        Route::get('operation-logs/export', [OperationLogController::class, 'export']);
    });
});
