<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Assessment extends Model
{
    protected $fillable = ['branch_id', 'group_id', 'student_id', 'teacher_id', 'period_id', 'assessment_type', 'assessment_date', 'final_score', 'predicate', 'note'];

    protected $casts = ['assessment_date' => 'date'];

    public function branch() { return $this->belongsTo(Branch::class); }
    public function group() { return $this->belongsTo(Group::class); }
    public function student() { return $this->belongsTo(Student::class); }
    public function teacher() { return $this->belongsTo(Teacher::class); }
    public function period() { return $this->belongsTo(Period::class); }
    public function lessonAssessment() { return $this->hasOne(LessonAssessment::class); }
    public function memorizationAssessment() { return $this->hasOne(MemorizationAssessment::class); }

    public static function predicateFor(float $score): string
    {
        return match (true) {
            $score >= 90 => 'Mumtaz',
            $score >= 80 => 'Jayyid Jiddan',
            $score >= 60 => 'Jayyid',
            default => 'Perlu Mengulang',
        };
    }
}
