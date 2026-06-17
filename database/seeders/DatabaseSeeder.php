<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            BranchSeeder::class,
            SurahSeeder::class,
            UserSeeder::class,
            GroupSeeder::class,
            SubjectSeeder::class,
            GuardianSeeder::class,
            TeacherSeeder::class,
            StudentSeeder::class,
            PeriodSeeder::class,
            ScheduleSeeder::class,
            AttendanceSeeder::class,
            ReportSeeder::class,
        ]);
    }
}
