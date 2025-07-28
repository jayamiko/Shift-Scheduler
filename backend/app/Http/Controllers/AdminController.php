<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use App\Models\ShiftRequest;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function approve(Request $request)
    {
        $request->validate([
            'request_id' => 'required|exists:shift_requests,id',
        ]);

        $shiftRequest = ShiftRequest::with('shift')->findOrFail($request->request_id);
        $shift = $shiftRequest->shift;

        $shift->update(['assigned_to' => $shiftRequest->user_id]);

        ShiftRequest::where('shift_id', $shift->id)
            ->where('id', '!=', $shiftRequest->id)
            ->update(['status' => 'rejected']);

        $shiftRequest->update(['status' => 'approved']);

        return response()->json([
            'message' => 'Shift approved and assigned.',
            'shift_id' => $shift->id,
            'assigned_to' => $shift->assigned_to,
        ]);
    }

    public function reject(Request $request)
    {
        $request->validate([
            'request_id' => 'required|exists:shift_requests,id',
        ]);

        $shiftRequest = ShiftRequest::findOrFail($request->request_id);
        $shiftRequest->update(['status' => 'rejected']);

        return response()->json(['message' => 'Shift request rejected.']);
    }

    public function manualAssign(Request $request)
    {
        $validated = $request->validate([
            'shift_id' => 'required|exists:shifts,id',
            'user_id' => 'required|exists:users,id',
        ]);
    
        $shift = Shift::findOrFail($validated['shift_id']);
        $userId = $validated['user_id'];
    
        $startUtc = \Carbon\Carbon::parse($shift->date . ' 00:00:00', 'Asia/Jakarta')->timezone('UTC');
        $endUtc = \Carbon\Carbon::parse($shift->date . ' 23:59:59', 'Asia/Jakarta')->timezone('UTC');
    
        $conflict = Shift::where('assigned_to', $userId)
            ->whereBetween('date', [$startUtc, $endUtc])
            ->where(function ($query) use ($shift) {
                $query
                    ->whereBetween('start_time', [$shift->start_time, $shift->end_time])
                    ->orWhereBetween('end_time', [$shift->start_time, $shift->end_time])
                    ->orWhere(function ($q) use ($shift) {
                        $q->where('start_time', '<=', $shift->start_time)
                            ->where('end_time', '>=', $shift->end_time);
                    });
            })
            ->exists();
    
        if ($conflict) {
            return response()->json([
                'message' => 'User already has a conflicting shift on this date and time.',
            ], 422);
        }
    
        $shift->assigned_to = $userId;
        $shift->save();
    
        ShiftRequest::where('shift_id', $shift->id)
            ->where('user_id', '!=', $userId)
            ->update(['status' => 'rejected']);
    
        $requestForUser = ShiftRequest::firstOrCreate(
            ['shift_id' => $shift->id, 'user_id' => $userId],
            ['status' => 'approved']
        );
    
        $requestForUser->status = 'approved';
        $requestForUser->save();
    
        return response()->json([
            'message' => 'Shift successfully assigned to user.',
            'shift_id' => $shift->id,
            'assigned_to' => $userId,
        ]);
    }

    public function assignments(Request $request)
    {
        $date = $request->query('date');
        $userName = $request->query('user'); 

        $query = Shift::with(['user', 'role']);

        if ($date) {
            $startUtc = Carbon::parse($date . ' 00:00:00', 'Asia/Jakarta')->timezone('UTC');
            $endUtc = Carbon::parse($date . ' 23:59:59', 'Asia/Jakarta')->timezone('UTC');

            $query->whereBetween('date', [$startUtc, $endUtc]);
        }

        if ($userName) {
            $query->whereHas('user', function ($q) use ($userName) {
                $q->where('name', 'like', '%' . $userName . '%');
            });
        }

        $shifts = $query->get();

        $shifts->transform(function ($shift) {
            $shift->date = Carbon::parse($shift->date)->timezone('Asia/Jakarta')->toDateString();
            $shift->start_time = Carbon::parse($shift->start_time)->timezone('Asia/Jakarta')->format('H:i');
            $shift->end_time = Carbon::parse($shift->end_time)->timezone('Asia/Jakarta')->format('H:i');
            return $shift;
        });

        return $shifts;
    }   
}
