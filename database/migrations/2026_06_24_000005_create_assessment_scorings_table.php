<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('assessment_scorings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_scoring_template_id')
              ->constrained()
              ->cascadeOnDelete();
            $table->foreignId('student_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->text('value');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessment_scorings');
    }
};
