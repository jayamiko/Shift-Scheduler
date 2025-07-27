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

        // Override any existing approval
        $shift->update(['assigned_to' => $shiftRequest->user_id]);

        // Reject all other requests for same shift
        ShiftRequest::where('shift_id', $shift->id)
            ->where('id', '!=', $shiftRequest->id)
            ->update(['status' => 'rejected']);

        $shiftRequest->update(['status' => 'approved']);

        return response()->json(['message' => 'Shift approved and assigned.']);
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

    public function assignments(Request $request)
    {
        $date = $request->query('date');
        $userId = $request->query('user_id');

        $query = Shift::with('user');

        if ($date) {
            $query->whereDate('date', $date);
        }

        if ($userId) {
            $query->where('assigned_to', $userId);
        }

        return $query->get();
    }
}
