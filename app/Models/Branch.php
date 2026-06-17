<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    protected $fillable = ['name', 'address', 'phone', 'head_name'];

    public function users() { return $this->hasMany(User::class); }
    public function students() { return $this->hasMany(Student::class); }
    public function teachers() { return $this->hasMany(Teacher::class); }
    public function groups() { return $this->hasMany(Group::class); }
    public function periods() { return $this->hasMany(Period::class); }
    public function schedules() { return $this->hasMany(Schedule::class); }
    public function attendances() { return $this->hasMany(Attendance::class); }
    public function assessments() { return $this->hasMany(Assessment::class); }
    public function reports() { return $this->hasMany(Report::class); }
}
