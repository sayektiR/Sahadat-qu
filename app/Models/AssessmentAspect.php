<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssessmentAspect extends Model
{
    protected $fillable = [
        'assessment_template_id',
        'aspect_name',
        'weight',
    ];

    public function template()
    {
        return $this->belongsTo(
            AssessmentTemplate::class,
            'assessment_template_id'
        );
    }
}