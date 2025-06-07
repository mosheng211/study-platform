<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\StudyProgressController;
use App\Http\Controllers\Api\ResourceController;
use App\Http\Controllers\Api\CheckinController;
use App\Http\Controllers\Api\AdminController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// 公开路由（无需认证）
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
});

// 公开资源路由
Route::prefix('resources')->group(function () {
    Route::get('/', [ResourceController::class, 'index']);
    Route::get('featured', [ResourceController::class, 'featured']);
    Route::get('categories', [ResourceController::class, 'categories']);
    Route::get('{id}', [ResourceController::class, 'show']);
    Route::get('{id}/download', [ResourceController::class, 'download']);
});

// 需要认证的路由
Route::middleware('auth:sanctum')->group(function () {
    
    // 认证相关路由
    Route::prefix('auth')->group(function () {
        Route::get('profile', [AuthController::class, 'profile']);
        Route::put('profile', [AuthController::class, 'updateProfile']);
        Route::put('change-password', [AuthController::class, 'changePassword']);
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('logout-all', [AuthController::class, 'logoutAll']);
    });

    // 学习进度路由
    Route::prefix('progress')->group(function () {
        Route::get('/', [StudyProgressController::class, 'index']);
        Route::post('initialize', [StudyProgressController::class, 'initialize']);
        Route::get('stats', [StudyProgressController::class, 'stats']);
        Route::get('{dayNumber}', [StudyProgressController::class, 'show']);
        Route::put('{dayNumber}', [StudyProgressController::class, 'update']);
    });

    // 资源管理路由（需要认证）
    Route::prefix('resources')->group(function () {
        Route::post('/', [ResourceController::class, 'store']);
        Route::put('{id}', [ResourceController::class, 'update']);
        Route::delete('{id}', [ResourceController::class, 'destroy']);
    });

    // 打卡路由
    Route::prefix('checkin')->group(function () {
        Route::get('/', [CheckinController::class, 'index']);
        Route::post('/', [CheckinController::class, 'store']);
        Route::get('today', [CheckinController::class, 'todayStatus']);
        Route::get('history', [CheckinController::class, 'history']);
        Route::get('dates', [CheckinController::class, 'dates']);
        Route::get('stats', [CheckinController::class, 'stats']);
        Route::put('{id}', [CheckinController::class, 'update']);
    });

    // 排行榜路由
    Route::prefix('leaderboard')->group(function () {
        Route::get('/', [CheckinController::class, 'leaderboard']);
        Route::get('achievements', [CheckinController::class, 'achievements']);
        Route::get('personal', [CheckinController::class, 'personalRank']);
    });

    // 管理员路由
    Route::prefix('admin')->group(function () {
        // 系统概览
        Route::get('stats', [AdminController::class, 'getSystemStats']);
        
        // 用户管理
        Route::get('users', [AdminController::class, 'getUsers']);
        Route::get('users/{id}', [AdminController::class, 'getUserDetail']);
        Route::post('users', [AdminController::class, 'createUser']);
        Route::put('users/{id}', [AdminController::class, 'updateUser']);
        Route::patch('users/{id}/status', [AdminController::class, 'updateUserStatus']);
        Route::delete('users/{id}', [AdminController::class, 'deleteUser']);
        Route::get('users/{userId}/progress', [AdminController::class, 'getUserProgress']);
        Route::get('users/{userId}/checkins', [AdminController::class, 'getUserCheckins']);
        
        // 学习数据
        Route::get('study-data', [AdminController::class, 'getStudyData']);
        
        // 资源管理
        Route::get('resources', [AdminController::class, 'getResources']);
        Route::get('resources/{id}', [AdminController::class, 'getResourceDetail']);
        Route::post('resources', [AdminController::class, 'createResource']);
        Route::put('resources/{id}', [AdminController::class, 'updateResource']);
        Route::delete('resources/{id}', [AdminController::class, 'deleteResource']);
        
        // 分类管理
        Route::get('categories', [AdminController::class, 'getCategories']);
        Route::get('categories/active', [AdminController::class, 'getActiveCategories']);
        Route::get('categories/{id}', [AdminController::class, 'getCategoryDetail']);
        Route::post('categories', [AdminController::class, 'createCategory']);
        Route::put('categories/{id}', [AdminController::class, 'updateCategory']);
        Route::delete('categories/{id}', [AdminController::class, 'deleteCategory']);
        
        // 系统设置
        Route::get('settings', [AdminController::class, 'getSystemSettings']);
        Route::put('settings', [AdminController::class, 'updateSystemSettings']);
        
        // 数据导出
        Route::get('export', [AdminController::class, 'exportData']);
    });

    // 用户信息路由（保持向后兼容）
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});

// 健康检查路由
Route::get('health', function () {
    return response()->json([
        'success' => true,
        'message' => 'API is running',
        'timestamp' => now(),
        'version' => '1.0.0'
    ]);
});
