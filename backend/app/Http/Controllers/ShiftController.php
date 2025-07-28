<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;

class ShiftController extends Controller
{
    public function index()
    {
        return Shift::with(['user', 'role'])->get();
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
            'role_id' => 'required|exists:roles,id',
            'location' => 'required|string',
        ]);
    
        $start = Carbon::createFromFormat('Y-m-d H:i', "{$data['date']} {$data['start_time']}", 'Asia/Jakarta')->setTimezone('UTC');
        $end = Carbon::createFromFormat('Y-m-d H:i', "{$data['date']} {$data['end_time']}", 'Asia/Jakarta')->setTimezone('UTC');
    
        $data['start_time'] = $start->format('H:i');
        $data['end_time'] = $end->format('H:i');
        $data['date'] = $start->format('Y-m-d'); 
    
        return Shift::create($data);
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'location' => 'required|string',
            'role_id' => 'required|integer|exists:roles,id',
        ]);

        $start = Carbon::createFromFormat('Y-m-d H:i', "{$data['date']} {$data['start_time']}", 'Asia/Jakarta')->setTimezone('UTC');
        $end = Carbon::createFromFormat('Y-m-d H:i', "{$data['date']} {$data['end_time']}", 'Asia/Jakarta')->setTimezone('UTC');

        $data['start_time'] = $start->format('H:i');
        $data['end_time'] = $end->format('H:i');
        $data['date'] = $start->format('Y-m-d'); 

        $shift = Shift::findOrFail($id);
        $shift->update($data);

        return response()->json($shift);
    }

    public function destroy($id)
    {
        Shift::destroy($id);
        return response()->noContent();
    }

    public function assigned(Request $request)
    {
        return Shift::whereNotNull('assigned_to')
            ->with('role') 
            ->get();
    }

    public function unassigned()
    {
        return Shift::whereNull('assigned_to')->with('role') ->get();
    }
}
