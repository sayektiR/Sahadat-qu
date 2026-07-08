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
        Schema::table('assessment_attribute_values', function (Blueprint $table) {
            $table->foreignId('assessment_id')
                ->after('id')
                ->constrained('assessments')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assessment_attribute_values', function (Blueprint $table) {
            $table->dropForeign(['assessment_id']);
            $table->dropColumn('assessment_id');
        });
    }
};
