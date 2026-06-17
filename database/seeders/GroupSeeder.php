<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Group;
use Illuminate\Database\Seeder;

class GroupSeeder extends Seeder
{
    public function run(): void
    {
        $bojonegoro = Branch::where('name', 'Sahadat-Qu Bojonegoro Branch')->firstOrFail();

        collect(['Pra Al-Qur\'an', 'Tilawah', 'Hafalan', 'Kelas B Tahfidz'])
            ->each(fn (string $name) => Group::create([
                'branch_id' => $bojonegoro->id,
                'name' => $name,
            ]));
    }
}
