<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use App\Models\ShiftRequest;
use Illuminate\Http\Request;

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

        $conflict = Shift::where('assigned_to', $userId)
            ->whereDate('date', $shift->date)
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
            $query->whereDate('date', $date);
        }
    
        if ($userName) {
            $query->whereHas('user', function ($q) use ($userName) {
                $q->where('name', 'like', '%' . $userName . '%');
            });
        }
    
        return $query->get();
    }    
}
