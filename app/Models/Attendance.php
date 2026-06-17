<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = ['branch_id', 'group_id', 'teacher_id', 'schedule_id', 'period_id', 'attendance_date', 'meeting_number', 'start_time', 'end_time'];

    protected $casts = ['attendance_date' => 'date'];

    public function branch() { return $this->belongsTo(Branch::class); }
    public function group() { return $this->belongsTo(Group::class); }
    public function teacher() { return $this->belongsTo(Teacher::class); }
    public function schedule() { return $this->belongsTo(Schedule::class); }
    public function period() { return $this->belongsTo(Period::class); }
    public function details() { return $this->hasMany(AttendanceDetail::class); }
}
