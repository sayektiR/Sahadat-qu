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
        Schema::create('assessment_attribute_values', function (Blueprint $table) {
            $table->id();

            $table->foreignId('assessment_attribute_id')
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
        Schema::dropIfExists('assessment_attribute_values');
    }
};
