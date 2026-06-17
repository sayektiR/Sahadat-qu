<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $fillable = ['branch_id', 'name', 'description'];

    public function branch() { return $this->belongsTo(Branch::class); }
    public function lessonAssessments() { return $this->hasMany(LessonAssessment::class); }
}
