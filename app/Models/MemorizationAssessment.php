<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MemorizationAssessment extends Model
{
    protected $fillable = ['assessment_id', 'memorization_type', 'surah_id', 'surah', 'from_ayah', 'to_ayah', 'movement_score', 'fluency_score', 'tajwid_score', 'makhraj_score', 'total_score', 'result_status', 'examiner_1', 'examiner_2'];

    public function assessment() { return $this->belongsTo(Assessment::class); }

    public function surahRecord() { return $this->belongsTo(Surah::class, 'surah_id'); }
}
