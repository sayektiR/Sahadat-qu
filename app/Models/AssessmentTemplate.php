<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssessmentTemplate extends Model
{
    protected $fillable = [
        'branch_id',    
        'name',
    ];

    public function attributes()
    {
        return $this->hasMany(
            AssessmentAttribute::class,
        );
    }

    public function aspects()
    {
        return $this->hasMany(
            AssessmentAspect::class,
        );
    }
}
