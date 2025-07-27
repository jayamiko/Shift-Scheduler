<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = ['HR', 'Lead Programmer', 'Product Manager', 'Frontend Developer', 'Backend Developer', 'Marketing', 'Graphic Design'];

        foreach ($roles as $name) {
            Role::create(['name' => $name]);
        }
    }
}