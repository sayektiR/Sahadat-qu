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

        User::firstOrCreate(
            ['email' => 'leader@sahadatqu.com'],
            [
                'name' => 'Leader Sahadat-Qu',
                'password' => Hash::make('password'),
                'role' => 'leader',
            ]
        );

        User::firstOrCreate(
            ['branch_id' => $bojonegoro->id, 'email' => 'admin.bojonegoro@sahadatqu.com'],
            [
                'name' => 'Admin Bojonegoro',
                'password' => Hash::make('password123'),
                'role' => 'admin',
            ]
        );

        User::firstOrCreate (
            ['branch_id' => $bojonegoro->id, 'email' => 'teacher.bojonegoro@sahadatqu.com'],
            [
                'name' => 'Teacher Bojonegoro',
                'password' => Hash::make('password123'),
                'role' => 'teacher',
            ]
        );

        User::firstOrCreate([
            'branch_id' => $bojonegoro->id,
            'email' => 'guardian.bojonegoro@sahadatqu.com',
        ], [
            'name' => 'Guardian Bojonegoro',
            'password' => Hash::make('password123'),
            'role' => 'guardian',
        ]);
    }
}
