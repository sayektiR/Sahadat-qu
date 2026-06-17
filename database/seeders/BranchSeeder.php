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
    }
}
