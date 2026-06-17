<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LessonAssessment extends Model
{
    protected $fillable = ['assessment_id', 'subject_id', 'score'];

    public function assessment() { return $this->belongsTo(Assessment::class); }
    public function subject() { return $this->belongsTo(Subject::class); }
}
