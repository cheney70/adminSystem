<?php

use Illuminate\Support\Facades\Route;
use Cheney\AdminSystem\Controllers\AuthController;
use Cheney\AdminSystem\Controllers\UserController;
use Cheney\AdminSystem\Controllers\RoleController;
use Cheney\AdminSystem\Controllers\PermissionController;
use Cheney\AdminSystem\Controllers\MenuController;
use Cheney\AdminSystem\Controllers\OperationLogController;
use Cheney\AdminSystem\Controllers\UploadController;
use Cheney\AdminSystem\Controllers\ArticleController;
use Cheney\AdminSystem\Controllers\ArticleCategoryController;
use Cheney\AdminSystem\Controllers\ArticleTagController;

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

        Route::get('menus/user', [MenuController::class, 'userMenus']);
        Route::get('menus/tree', [MenuController::class, 'tree']);
        Route::apiResource('menus', MenuController::class);

        Route::apiResource('operation-logs', OperationLogController::class)->only(['index', 'show', 'destroy']);
        Route::post('operation-logs/batch', [OperationLogController::class, 'batchDestroy']);
        Route::delete('operation-logs/clear', [OperationLogController::class, 'clear']);
        Route::get('operation-logs/export', [OperationLogController::class, 'export']);

        Route::post('upload', [UploadController::class, 'upload']);

        // 文章管理
        Route::prefix('articles')->group(function () {
            Route::post('{id}/publish', [ArticleController::class, 'publish']);
            Route::post('{id}/unpublish', [ArticleController::class, 'unpublish']);
        });
        Route::apiResource('articles', ArticleController::class);

        // 文章分类管理
        Route::prefix('article-categories')->group(function () {
            Route::get('tree', [ArticleCategoryController::class, 'tree']);
        });
        Route::apiResource('article-categories', ArticleCategoryController::class);

        // 文章标签管理
        Route::apiResource('article-tags', ArticleTagController::class);
    });
});
