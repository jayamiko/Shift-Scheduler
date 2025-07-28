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

        if ($shift->assigned_to) {
            return response()->json(['error' => 'Shift already assigned.'], 409);
        }

        $overlap = Shift::where('date', $shift->date)
            ->whereHas('requests', function ($q) use ($user) {
                $q->where('user_id', $user->id)->where('status', 'approved');
            })
            ->where(function ($q) use ($shift) {
                $q->whereBetween('start_time', [$shift->start_time, $shift->end_time])
                  ->orWhereBetween('end_time', [$shift->start_time, $shift->end_time]);
            })->exists();

        if ($overlap) {
            return response()->json(['error' => 'Overlapping shift detected.'], 409);
        }

        $dayCount = ShiftRequest::where('user_id', $user->id)
            ->whereHas('shift', fn ($q) => $q->whereDate('date', $shift->date))
            ->where('status', 'approved')
            ->count();

        if ($dayCount >= 1) {
            return response()->json(['error' => 'You already have a shift on that day.'], 409);
        }

        $startOfWeek = Carbon::parse($shift->date)->startOfWeek();
        $endOfWeek = Carbon::parse($shift->date)->endOfWeek();

        $weekCount = ShiftRequest::where('user_id', $user->id)
            ->whereHas('shift', fn ($q) => $q->whereBetween('date', [$startOfWeek, $endOfWeek]))
            ->where('status', 'approved')
            ->count();

        if ($weekCount >= 5) {
            return response()->json(['error' => 'You exceeded weekly shift limit.'], 409);
        }

        $request = ShiftRequest::create([
            'user_id' => $user->id,
            'shift_id' => $shift->id,
            'status' => 'pending'
        ]);

        return response()->json($request);
    }

    public function status()
    {
        $user = Auth::user();
        return ShiftRequest::with('shift')->where('user_id', $user->id)->get();
    }
}
