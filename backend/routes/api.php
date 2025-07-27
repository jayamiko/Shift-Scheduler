<?php

use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/shifts', [ShiftController::class, 'index']);
    Route::post('/shifts', [ShiftController::class, 'store']);
    Route::post('/shifts/request', [ShiftRequestController::class, 'request']);
    Route::get('/shifts/request/status', [ShiftRequestController::class, 'status']);

    // Admin
    Route::post('/admin/approve', [AdminController::class, 'approve']);
    Route::post('/admin/reject', [AdminController::class, 'reject']);
    Route::get('/admin/assignments', [AdminController::class, 'assignments']);
});

