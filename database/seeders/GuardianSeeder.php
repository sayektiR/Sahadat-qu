<?php

namespace Database\Seeders;

use App\Models\Guardian;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class GuardianSeeder extends Seeder
{
    public function run(): void
    {
        $names = ['Ikhsan', 'Kasino', 'Indro', 'Donno', 'Fizi', 'Ros', 'Mail', 'Ijat', 'Devi'];
        $guardianUser = User::where('email', 'guardian.bojonegoro@sahadatqu.com')->firstOrFail();

        foreach ($names as $index => $name) {
            $user = $index === 0
                ? tap($guardianUser)->update(['name' => $name])
                : $user = User::updateOrCreate(
                    [
                        'email' => 'wali.kasino@sahadatqu.com',
                    ],
                    [
                        'branch_id' => $guardianUser->branch_id,
                        'name'      => 'Kasino',
                        'password'  => Hash::make('password'),
                        'role'      => 'guardian',
                    ]
                );

            Guardian::create([
                'user_id' => $user->id,
                'name' => $name,
                'phone' => '08123456700' . ($index + 3),
                'relation' => 'Parent',
            ]);
        }
    }
}
