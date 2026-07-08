<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssessmentScoring extends Model
{
    protected $fillable = [
        
        'assessment_aspect_id',
        'assessment_id',
        'value',
    ];

        public function assessment()
    {
        return $this->belongsTo(Assessment::class, 'assessment_id');
    }

    public function aspect()
    {
        return $this->belongsTo(
            AssessmentAspect::class,
            'assessment_aspect_id'
        );
    }

}