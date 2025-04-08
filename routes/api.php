<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ContractController;
use App\Http\Controllers\Api\MaintenanceRequestController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\RoomController;
use App\Http\Controllers\Api\TenantController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    // Room routes
    Route::apiResource('rooms', RoomController::class);
    Route::get('rooms/statistics', [RoomController::class, 'statistics']);

    // Tenant routes
    Route::apiResource('tenants', TenantController::class);
    Route::get('tenants/search', [TenantController::class, 'search']);

    // Contract routes
    Route::apiResource('contracts', ContractController::class);
    Route::get('contracts/upcoming-expirations', [ContractController::class, 'upcomingExpirations']);
    Route::post('contracts/{contract}/terminate', [ContractController::class, 'terminate']);

    // Payment routes
    Route::apiResource('payments', PaymentController::class);
    Route::get('contracts/{contract}/payments', [PaymentController::class, 'byContract']);
    Route::get('payments/statistics', [PaymentController::class, 'statistics']);

    // Maintenance Request routes
    Route::apiResource('maintenance-requests', MaintenanceRequestController::class);
    Route::get('rooms/{room}/maintenance-requests', [MaintenanceRequestController::class, 'byRoom']);
    Route::get('maintenance-requests/statistics', [MaintenanceRequestController::class, 'statistics']);
}); 