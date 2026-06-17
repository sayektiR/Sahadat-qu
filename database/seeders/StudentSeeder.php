<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Group;
use App\Models\Guardian;
use App\Models\Student;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        $bojonegoro = Branch::where('name', 'Sahadat-Qu Bojonegoro Branch')->firstOrFail();
        $guardians = Guardian::orderBy('id')->get();
        $groups = Group::where('branch_id', $bojonegoro->id)->orderBy('id')->get();

        collect(['Mawar', 'Jasmin', 'Melati', 'Lily', 'Susanti', 'Mei-Mei', 'Raju', 'Ekhsan', 'Jarjit'])
            ->each(function (string $name, int $index) use ($bojonegoro, $groups, $guardians) {
                Student::create([
                    'branch_id' => $bojonegoro->id,
                    'group_id' => $groups[$index % $groups->count()]->id,
                    'guardian_id' => $guardians[$index % $guardians->count()]->id,
                    'nis' => 'SQ-' . str_pad((string) ($index + 1), 4, '0', STR_PAD_LEFT),
                    'name' => $name,
                    'gender' => $index % 2 === 0 ? 'female' : 'male',
                    'status' => 'active',
                ]);
            });
    }
}
