<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $fillable = [
        'branch_id',
        'group_id',
        'all_groups',
        'period_id',
        'start_date',
        'end_date',
        'start_time',
        'end_time',
        'total_meetings',
    ];

    protected $casts = ['start_date' => 'date', 'end_date' => 'date'];

    public function branch() { return $this->belongsTo(Branch::class); }
    public function group() { return $this->belongsTo(Group::class); }
    public function period() { return $this->belongsTo(Period::class); }
    public function details() { return $this->hasMany(ScheduleDetail::class); }
}
