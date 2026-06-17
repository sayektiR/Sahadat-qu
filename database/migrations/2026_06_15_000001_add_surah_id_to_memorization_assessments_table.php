<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('memorization_assessments', function (Blueprint $table) {
            $table->foreignId('surah_id')
                ->nullable()
                ->after('memorization_type')
                ->constrained('surahs')
                ->nullOnDelete();
        });

        DB::table('memorization_assessments')
            ->join('surahs', 'memorization_assessments.surah', '=', 'surahs.name')
            ->whereNull('memorization_assessments.surah_id')
            ->update(['memorization_assessments.surah_id' => DB::raw('surahs.id')]);
    }

    public function down(): void
    {
        Schema::table('memorization_assessments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('surah_id');
        });
    }
};
