<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Assessment extends Model
{
    protected $fillable = ['branch_id',
    'group_id',
    'student_id',
    'teacher_id',
    'period_id',
    'assessment_template_id',
    'assessment_date',
    'final_score',
    'predicate',
    'note'
    ];

    protected $casts = ['assessment_date' => 'date'];

    public function branch() { return $this->belongsTo(Branch::class); }
    public function group() { return $this->belongsTo(Group::class); }
    public function student() { return $this->belongsTo(Student::class); }
    public function teacher() { return $this->belongsTo(Teacher::class); }
    public function period() { return $this->belongsTo(Period::class); }
    public function template() {return $this->belongsTo(AssessmentTemplate::class, 'assessment_template_id'); }
    public function scorings(){ return $this->hasMany(AssessmentScoring::class, 'assessment_id'); }
    public function attributeValues() { return $this->hasMany(AssessmentAttributeValue::class, 'assessment_id'); }
    public function subject() {return $this->belongsTo(Subject::class);}

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
