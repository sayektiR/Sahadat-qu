<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Surah extends Model
{
    protected $fillable = [
        'number',
        'name',
        'name_arabic',
        'ayah_count',
        'revelation_place',
    ];

    public function memorizationAssessments()
    {
        return $this->hasMany(MemorizationAssessment::class);
    }
}
