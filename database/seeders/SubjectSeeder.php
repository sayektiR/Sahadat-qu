<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Subject;
use Illuminate\Database\Seeder;

class SubjectSeeder extends Seeder
{
    public function run(): void
    {
        $bojonegoro = Branch::where('name', 'Sahadat-Qu Bojonegoro Branch')->firstOrFail();

        collect(['Tajwid', 'Hadist', 'Aqidah', 'Kaligrafi', 'Al-Qur\'an'])
            ->each(fn (string $name) => Subject::create([
                'branch_id' => $bojonegoro->id,
                'name' => $name,
            ]));
    }
}
