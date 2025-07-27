<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin 1',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('admin123'),
            'is_admin' => true,
        ]);
        
        User::create([
            'name' => 'User 1',
            'email' => 'user@gmail.com',
            'password' => Hash::make('user123'),
            'is_admin' => false,
        ]);
    }
}
