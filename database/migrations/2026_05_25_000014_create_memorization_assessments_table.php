<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('memorization_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_id')->constrained()->cascadeOnDelete();
            $table->string('memorization_type');
            $table->string('surah');
            $table->unsignedInteger('from_ayah');
            $table->unsignedInteger('to_ayah');
            $table->decimal('movement_score', 5, 2);
            $table->decimal('fluency_score', 5, 2);
            $table->decimal('tajwid_score', 5, 2);
            $table->decimal('makhraj_score', 5, 2);
            $table->decimal('total_score', 5, 2);
            $table->string('result_status')->nullable();
            $table->string('examiner_1')->nullable();
            $table->string('examiner_2')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('memorization_assessments');
    }
};
