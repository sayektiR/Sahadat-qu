<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Period;
use Illuminate\Database\Seeder;

class PeriodSeeder extends Seeder
{
    public function run(): void
    {
        $bojonegoro = Branch::where('name', 'Sahadat-Qu Bojonegoro Branch')->firstOrFail();

        Period::create([
            'branch_id' => $bojonegoro->id,
            'name' => '2026/2027 Ganjil',
            'academic_year' => '2026/2027',
            'semester' => 'Ganjil',
            'start_date' => '2026-07-01',
            'end_date' => '2026-12-31',
            'is_active' => true,
        ]);
    }
}
