<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use Illuminate\Http\JsonResponse;

class ShiftController extends Controller
{
    /**
     * Get all shifts.
     */
    public function index(): JsonResponse
    {
        $shifts = Shift::all();

        return response()->json([
            'status' => 'success',
            'data' => $shifts,
        ]);
    }
}
