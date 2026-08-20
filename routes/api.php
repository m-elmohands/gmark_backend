<?php

use App\Http\Controllers\Api\V1\AreaController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\SiteSettingController;
use App\Http\Controllers\Api\V1\UnitController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware('locale')->group(function (): void {
    Route::prefix('auth')->group(function (): void {
        Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:6,1');
        Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:6,1');
        Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->middleware('throttle:3,1');
        Route::post('/reset-password', [AuthController::class, 'resetPassword'])->middleware('throttle:6,1');

        Route::middleware('auth:sanctum')->group(function (): void {
            Route::get('/me', [AuthController::class, 'me']);
            Route::put('/profile', [AuthController::class, 'updateProfile']);
            Route::put('/password', [AuthController::class, 'changePassword'])->middleware('throttle:6,1');
            Route::post('/logout', [AuthController::class, 'logout']);
        });
    });

    Route::get('/areas', [AreaController::class, 'index']);
    Route::get('/settings', [SiteSettingController::class, 'show']);
    Route::get('/areas/{slug}', [AreaController::class, 'show']);
    Route::get('/areas/{slug}/units', [AreaController::class, 'units']);
    Route::get('/units/{slug}', [UnitController::class, 'show']);
});
