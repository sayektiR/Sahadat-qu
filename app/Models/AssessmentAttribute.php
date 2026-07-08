<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssessmentAttribute extends Model
{
    protected $fillable = [
        'assessment_template_id',
        'attribute_name',
        'attribute_type',
    ];

    public function template()
    {
        return $this->belongsTo(
            AssessmentTemplate::class,
            'assessment_template_id'
        );
    }
}