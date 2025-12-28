<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ExternalApiController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// External API demonstration endpoint (for HTTP faking tests)
Route::get('/ip-info', [ExternalApiController::class, 'ipInfo']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
});
