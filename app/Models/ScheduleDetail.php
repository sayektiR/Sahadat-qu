<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScheduleDetail extends Model
{
    protected $fillable = ['schedule_id', 'day', 'subject_id', 'material_name', 'order_number'];

    public function schedule() { return $this->belongsTo(Schedule::class); }
    public function subject() { return $this->belongsTo(Subject::class); }
}
