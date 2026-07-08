<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssessmentAttributeValue extends Model
{
    protected $fillable = [
        'assessment_id',
        'assessment_attribute_id',
        'value',
    ];

    public function assessment()
    {
        return $this->belongsTo(Assessment::class);
    }

    public function attribute()
    {
        return $this->belongsTo(
            AssessmentAttribute::class,
            'assessment_attribute_id'
        );
    }
}