<?php

use App\Http\Controllers\TenantFileUploadController;
use App\Http\Controllers\WebSocket\TenantConnectionController;
use Illuminate\Support\Facades\Route;

// Route::middleware('auth:sanctum')->post('/websocket/connections', TenantConnectionController::class)
//     ->name('websocket.connections.store');

Route::post('/websocket/connections', TenantConnectionController::class)
    ->name('websocket.connections.store');

Route::post('/tenants/{tenant}/upload', TenantFileUploadController::class)
    ->name('api.tenants.upload');
