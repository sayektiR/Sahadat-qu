<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Period extends Model
{
    protected $fillable = ['branch_id', 'name', 'academic_year', 'semester', 'start_date', 'end_date', 'is_active'];

    protected $casts = ['start_date' => 'date', 'end_date' => 'date', 'is_active' => 'boolean'];

    public function branch() { return $this->belongsTo(Branch::class); }
    public function schedules() { return $this->hasMany(Schedule::class); }
    public function assessments() { return $this->hasMany(Assessment::class); }
    public function reports() { return $this->hasMany(Report::class); }
}
