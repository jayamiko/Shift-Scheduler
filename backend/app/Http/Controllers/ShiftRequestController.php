<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use App\Models\ShiftRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ShiftRequestController extends Controller
{
    public function requestShift(Request $request)
    {
        $request->validate([
            'shift_id' => 'required|exists:shifts,id',
        ]);
    
        $user = Auth::user();
        $shift = Shift::findOrFail($request->shift_id);

        if ($shift->assigned_to && $shift->assigned_to !== $user->id) {
            return response()->json([
                'error' => 'Shift already assigned to another user.'
            ], 409);
        }

        $existingRequest = ShiftRequest::where('shift_id', $shift->id)
            ->where('status', '==', 'approved')
            ->first();

        if ($existingRequest && $existingRequest->user_id === $user->id) {
            return response()->json([
                'error' => 'You have already requested this shift.'
            ], 409);
        }

        $overlap = ShiftRequest::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'approved'])
            ->whereHas('shift', function ($q) use ($shift) {
                $q->where('date', $shift->date)
                    ->where(function ($query) use ($shift) {
                        $query->whereBetween('start_time', [$shift->start_time, $shift->end_time])
                            ->orWhereBetween('end_time', [$shift->start_time, $shift->end_time])
                            ->orWhere(function ($sub) use ($shift) {
                                $sub->where('start_time', '<=', $shift->start_time)
                                    ->where('end_time', '>=', $shift->end_time);
                            });
                    });
            })
            ->exists();
        
        if ($overlap) {
            return response()->json(['error' => 'You already have an overlapping shift request.'], 409);
        }
    
        $dayCount = ShiftRequest::where('user_id', $user->id)
            ->whereHas('shift', fn($q) => $q->whereDate('date', $shift->date))
            ->where('status', 'approved')
            ->count();
    
        if ($dayCount >= 1) {
            return response()->json([
                'error' => 'You already have a shift on this day.'
            ], 409);
        }
    
        $startOfWeek = \Carbon\Carbon::parse($shift->date)->startOfWeek();
        $endOfWeek = \Carbon\Carbon::parse($shift->date)->endOfWeek();
    
        $weekCount = ShiftRequest::where('user_id', $user->id)
            ->whereHas('shift', fn($q) => $q->whereBetween('date', [$startOfWeek, $endOfWeek]))
            ->where('status', 'approved')
            ->count();
    
        if ($weekCount >= 5) {
            return response()->json([
                'error' => 'You have exceeded the maximum weekly shift limit (5).'
            ], 409);
        }
    
        $request = ShiftRequest::create([
            'user_id' => $user->id,
            'shift_id' => $shift->id,
            'status' => 'pending'
        ]);
    
        return response()->json([
            'message' => 'Shift requested successfully.',
            'data' => $request
        ]);
    }

    public function status(Request $request)
    {
        $status = $request->query('status');
        $allowedStatuses = ['pending', 'approved', 'rejected'];

        if ($status && !in_array($status, $allowedStatuses)) {
            return response()->json(['message' => 'Status invalid'], 400);
        }

        $query = ShiftRequest::with(['shift', 'user']);

        if ($status) {
            $query->where('status', $status);
        }

        return $query->get();
    }
}
