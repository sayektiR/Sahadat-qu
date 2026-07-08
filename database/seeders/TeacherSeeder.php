<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Group;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Seeder;

class TeacherSeeder extends Seeder
{
    public function run(): void
    {
        $bojonegoro = Branch::where('name', 'Sahadat-Qu Bojonegoro Branch')->firstOrFail();

        $teacherUser = User::where('email', 'teacher.bojonegoro@sahadatqu.com')->firstOrFail();

        $groups = Group::where('branch_id', $bojonegoro->id)
            ->orderBy('id')
            ->get();

        $teacherNames = [
            'Fatimah',
            'Siti Aisah',
            'Dudung',
            'Somat',
            'Susan',
        ];

        foreach ($teacherNames as $index => $name) {

            Teacher::create([
                'user_id'   => $index === 0 ? $teacherUser->id : null,
                'branch_id' => $bojonegoro->id,
                'group_id'  => $groups[$index % $groups->count()]->id,
                'name'      => $name,
                'status'    => 'active',
            ]);
        }
    }
}
