<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\ShiftRequestController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::get('/roles', [RoleController::class, 'index']);
Route::get('/users', [UserController::class, 'index']);

Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // Shift
    Route::get('shifts', [ShiftController::class, 'index']);
    Route::post('shift', [ShiftController::class, 'store']);
    
    Route::get('shifts/assigned', [ShiftController::class, 'assigned']);
    Route::get('shifts/unassigned', [ShiftController::class, 'unassigned']);
    Route::post('shifts/request', [ShiftRequestController::class, 'requestShift']);
    Route::get('shifts/request/status', [ShiftRequestController::class, 'status']);
    
    Route::get('shifts/{id}', [ShiftController::class, 'show']);
    Route::delete('shifts/{id}', [ShiftController::class, 'destroy']);
    Route::put('shifts/edit/{id}', [ShiftController::class, 'update']);
    
    // Admin Approve/Reject
    Route::post('admin/approve', [AdminController::class, 'approve']);
    Route::post('admin/reject', [AdminController::class, 'reject']);

    // View Assignments
    Route::get('admin/assignments', [AdminController::class, 'assignments']);
    Route::post('/admin/assign', [AdminController::class, 'manualAssign']);
});