<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $fillable = ['branch_id', 'name', 'description'];

    public function branch() { return $this->belongsTo(Branch::class); }
    public function students() { return $this->hasMany(Student::class); }
    public function teachers() { return $this->belongsToMany(Teacher::class, 'teacher_groups')->withTimestamps(); }
    public function schedules() { return $this->hasMany(Schedule::class); }
}
