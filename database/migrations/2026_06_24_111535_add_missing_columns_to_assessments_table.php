<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assessments', function (Blueprint $table) {

            if (!Schema::hasColumn('assessments', 'student_id')) {
                $table->foreignId('student_id')
                    ->after('group_id')
                    ->constrained()
                    ->cascadeOnDelete();
            }

            if (!Schema::hasColumn('assessments', 'final_score')) {
                $table->decimal('final_score', 5, 2)
                    ->nullable()
                    ->after('assessment_date');
            }

            if (!Schema::hasColumn('assessments', 'predicate')) {
                $table->string('predicate')
                    ->nullable()
                    ->after('final_score');
            }
        });
    }

    public function down(): void
    {
        Schema::table('assessments', function (Blueprint $table) {

            if (Schema::hasColumn('assessments', 'predicate')) {
                $table->dropColumn('predicate');
            }

            if (Schema::hasColumn('assessments', 'final_score')) {
                $table->dropColumn('final_score');
            }

            if (Schema::hasColumn('assessments', 'student_id')) {
                $table->dropForeign(['student_id']);
                $table->dropColumn('student_id');
            }
        });
    }
};