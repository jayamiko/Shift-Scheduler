<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Shift;
use App\Models\Role;
use Illuminate\Support\Carbon;

class ShiftSeeder extends Seeder
{
    public function run(): void
    {
        $roles = Role::all();
        $startDate = Carbon::today();

        foreach ($roles as $index => $role) {
            $date = $startDate->copy()->addDays($index); 
            Shift::create([
                'date' => $date->toDateString(),
                'start_time' => '09:00',
                'end_time' => '17:00',
                'location' => 'Office A',
                'role_id' => $role->id,
                'assigned_to' => null, 
            ]);
        }
    }
}
