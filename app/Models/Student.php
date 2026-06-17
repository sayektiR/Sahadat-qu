<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = ['branch_id', 'group_id', 'guardian_id', 'nis', 'nik', 'name', 'birth_place', 'birth_date', 'gender', 'address', 'photo', 'status'];

    protected $casts = ['birth_date' => 'date'];

    public function branch() { return $this->belongsTo(Branch::class); }
    public function group() { return $this->belongsTo(Group::class); }
    public function guardian() { return $this->belongsTo(Guardian::class); }
    public function attendanceDetails() { return $this->hasMany(AttendanceDetail::class); }
    public function assessments() { return $this->hasMany(Assessment::class); }
    public function reports() { return $this->hasMany(Report::class); }
}
