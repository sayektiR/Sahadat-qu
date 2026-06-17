<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $fillable = ['branch_id', 'student_id', 'period_id', 'homeroom_teacher_id', 'final_note', 'report_date', 'signed_by', 'pdf_path'];

    protected $casts = ['report_date' => 'date'];

    public function branch() { return $this->belongsTo(Branch::class); }
    public function student() { return $this->belongsTo(Student::class); }
    public function period() { return $this->belongsTo(Period::class); }
    public function homeroomTeacher() { return $this->belongsTo(Teacher::class, 'homeroom_teacher_id'); }
}
