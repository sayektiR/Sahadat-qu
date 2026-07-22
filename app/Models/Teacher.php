<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    protected $fillable = ['user_id', 'branch_id', 'group_id', 'name', 'phone', 'address', 'gender', 'photo', 'status'];

    public function user() { return $this->belongsTo(User::class); }
    public function branch() { return $this->belongsTo(Branch::class); }
    public function attendances() { return $this->hasMany(Attendance::class); }
    public function assessments() { return $this->hasMany(Assessment::class); }
    public function group() { return $this->belongsTo(Group::class); }
}
