<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\ShiftRequestController;
use App\Http\Controllers\AdminController;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // Shift CRUD
    Route::get('shifts', [ShiftController::class, 'index']);
    Route::post('shifts', [ShiftController::class, 'store']);
    Route::get('shifts/{id}', [ShiftController::class, 'show']);
    Route::put('shifts/{id}', [ShiftController::class, 'update']);
    Route::delete('shifts/{id}', [ShiftController::class, 'destroy']);

    // Shift Requests (by workers)
    Route::post('shifts/request', [ShiftRequestController::class, 'requestShift']);
    Route::get('shifts/request/status', [ShiftRequestController::class, 'status']);

    // Admin Approve/Reject
    Route::post('admin/approve', [AdminController::class, 'approve']);
    Route::post('admin/reject', [AdminController::class, 'reject']);

    // View Assignments
    Route::get('admin/assignments', [AdminController::class, 'assignments']);
});
