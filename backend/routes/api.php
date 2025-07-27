<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\ShiftRequestController;
use App\Http\Controllers\AdminController;

Route::post('/register', [AuthController::class, 'register']);
Route::get('/shifts', [ShiftController::class, 'index']);

Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
    
    Route::post('/shifts', [ShiftController::class, 'store']);
    Route::post('/shifts/request', [ShiftRequestController::class, 'request']);
    Route::get('/shifts/request/status', [ShiftRequestController::class, 'status']);

    // Admin
    Route::post('/admin/approve', [AdminController::class, 'approve']);
    Route::post('/admin/reject', [AdminController::class, 'reject']);
    Route::get('/admin/assignments', [AdminController::class, 'assignments']);
});
