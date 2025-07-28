<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $roles = Role::pluck('id')->toArray();

        User::create([
            'name' => 'Admin',
            'email' => 'admin@mail.com',
            'password' => Hash::make('admin123'),
            'is_admin' => true,
        ]);
        User::create([
            'name' => 'User 1',
            'email' => 'user1@mail.com',
            'password' => Hash::make('user123'),
            'is_admin' => false,
        ]);
        User::create([
            'name' => 'User 2',
            'email' => 'user2@mail.com',
            'password' => Hash::make('user123'),
            'is_admin' => false,
        ]);
        User::create([
            'name' => 'User 3',
            'email' => 'user3@mail.com',
            'password' => Hash::make('user123'),
            'is_admin' => false,
        ]);
    }
}
