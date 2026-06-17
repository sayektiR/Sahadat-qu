<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $bojonegoro = Branch::where('name', 'Sahadat-Qu Bojonegoro Branch')->firstOrFail();

        User::create([
            'name' => 'Leader Sahadat-Qu',
            'email' => 'leader@sahadatqu.com',
            'password' => Hash::make('password123'),
            'role' => 'leader',
        ]);

        User::create([
            'branch_id' => $bojonegoro->id,
            'name' => 'Admin Bojonegoro',
            'email' => 'admin.bojonegoro@sahadatqu.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        User::create([
            'branch_id' => $bojonegoro->id,
            'name' => 'Teacher Bojonegoro',
            'email' => 'teacher.bojonegoro@sahadatqu.com',
            'password' => Hash::make('password123'),
            'role' => 'teacher',
        ]);

        User::create([
            'branch_id' => $bojonegoro->id,
            'name' => 'Guardian Bojonegoro',
            'email' => 'guardian.bojonegoro@sahadatqu.com',
            'password' => Hash::make('password123'),
            'role' => 'guardian',
        ]);
    }
}
