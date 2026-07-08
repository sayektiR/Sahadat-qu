<?php

namespace Database\Seeders;

use App\Models\Branch;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    public function run(): void
    {
        Branch::create([
            'name' => 'Sahadat-Qu Bojonegoro Branch',
            'address' => 'Bojonegoro',
            'phone' => '081234567001',
            'head_name' => 'Ketua Bojonegoro',
        ]);

        Branch::create([
            'name' => 'Sahadat-Qu Nganjuk Branch',
            'address' => 'Nganjuk',
            'phone' => '081234567002',
            'head_name' => 'Ketua Nganjuk',
        ]);

        Branch::create([
            'name' => 'Sahadat-Qu Banyuwangi Branch',
            'address' => 'Banyuwangi',
            'phone' => '081234567003',
            'head_name' => 'Ketua Banyuwangi',
        ]);

        Branch::create([
            'name' => 'Sahadat-Qu Pasuruan Branch',
            'address' => 'Pasuruan',
            'phone' => '081234567004',
            'head_name' => 'Ketua Pasuruan',
        ]);
    }
}
