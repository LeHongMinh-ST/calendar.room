<?php

namespace App\Models;

use App\Imports\ScheduleImport;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Maatwebsite\Excel\Facades\Excel;

class Schedule extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'room_id',
        'semester_id',
        'subject_id',
        'subject_group',
        'teacher_id',
        'class',
        'amount_people',
        'day',
        'session',
        'number_session',
        'week',
        'number_week',
        'status',
        'note',
        'user_create_id',
        'user_update_id',
        'week_check',
    ];

    public function Subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function Room()
    {
        return $this->belongsTo(Room::class);
    }

    public function Semester()
    {
        return $this->belongsTo(Semester::class);
    }

}
