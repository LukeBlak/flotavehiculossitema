<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\FuelLogController;
use App\Http\Controllers\IncidentController;
use App\Http\Controllers\MaintenanceLogController;
use App\Http\Controllers\TripController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\VehicleTypeController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::post('auth/login', [AuthController::class, 'login'])->middleware('throttle:10,1');

    Route::middleware('auth:sanctum')->group(function (): void {
        Route::post('auth/logout', [AuthController::class, 'logout']);
        Route::get('auth/me', [AuthController::class, 'me']);

        Route::apiResource('vehicle-types', VehicleTypeController::class);
        Route::apiResource('vehicles', VehicleController::class);
        Route::apiResource('trips', TripController::class);
        Route::apiResource('fuel-logs', FuelLogController::class);
        Route::apiResource('maintenance-logs', MaintenanceLogController::class);
        Route::apiResource('incidents', IncidentController::class);
    });
});
