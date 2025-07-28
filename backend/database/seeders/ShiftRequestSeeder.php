<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ShiftRequest;
use App\Models\Shift;
use App\Models\User;

class ShiftRequestSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('is_admin', false)->first(); // Ambil 1 user pekerja
        $shifts = Shift::take(5)->get(); // Ambil 5 shift pertama (tidak overlap & tidak assigned)

        foreach ($shifts as $shift) {
            ShiftRequest::create([
                'user_id' => $user->id,
                'shift_id' => $shift->id,
                'status' => 'pending', // Sesuai permintaan
            ]);
        }
    }
}

