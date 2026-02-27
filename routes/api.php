<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientAppController;
use App\Http\Controllers\RuleController;
use App\Http\Controllers\EventIngestController;
use App\Http\Controllers\CaseController;
use App\Http\Controllers\AlertController;
use App\Http\Controllers\BlocklistController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\DashboardController;

Route::prefix('v1')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);

        Route::middleware('auth:sanctum')->group(function () {
            Route::post('/logout', [AuthController::class, 'logout']);
        });
    });
    Route::post('/events', [EventIngestController::class, 'store'])
    ->middleware(['client.app', 'throttle:events']);
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', [AuthController::class, 'me']);

        // Client Apps
        Route::apiResource('apps', ClientAppController::class);

        // Rules
        Route::get('/apps/{app}/rules', [RuleController::class, 'index']);
        Route::post('/apps/{app}/rules', [RuleController::class, 'store']);
        Route::patch('/rules/{rule}', [RuleController::class, 'update']);
        Route::delete('/rules/{rule}', [RuleController::class, 'destroy']);


        // Cases + Alerts
        Route::get('/cases', [CaseController::class, 'index']);
        Route::get('/cases/{case}', [CaseController::class, 'show']);
        Route::patch('/cases/{case}/status', [CaseController::class, 'updateStatus']);

        Route::get('/alerts', [AlertController::class, 'index']);
        Route::patch('/alerts/{alert}/ack', [AlertController::class, 'ack']);
        Route::patch('/alerts/{alert}/resolve', [AlertController::class, 'resolve']);


        Route::get('/apps/{app}/blocklist', [BlocklistController::class, 'index']);
        Route::post('/apps/{app}/blocklist', [BlocklistController::class, 'store']);
        Route::delete('/blocklist/{block}', [BlocklistController::class, 'destroy']);

        Route::get('/events', [EventController::class, 'index']);
        Route::get('/dashboard/overview', [DashboardController::class, 'overview']);
    });
});