<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ShiftController extends Controller
{
    public function index()
    {
        return Shift::with('user')->get();
    }

    public function show($id)
    {
        return Shift::with('user')->findOrFail($id);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'role' => 'required|string',
            'location' => 'required|string',
        ]);

        return Shift::create($data);
    }

    public function update(Request $request, $id)
    {
        $shift = Shift::findOrFail($id);

        $data = $request->validate([
            'date' => 'date',
            'start_time' => 'date_format:H:i',
            'end_time' => 'date_format:H:i|after:start_time',
            'role' => 'string',
            'location' => 'string',
        ]);

        $shift->update($data);

        return $shift;
    }

    public function destroy($id)
    {
        Shift::destroy($id);
        return response()->noContent();
    }

    public function assignedToMe(Request $request)
    {
        return Shift::where('assigned_to', $request->user()->id)->get();
    }

    public function unassigned()
    {
        return Shift::whereNull('assigned_to')->get();
    }
}
