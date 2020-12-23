<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Room extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];
    public function Schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    public function Assignments()
    {
        return $this->hasMany(Assignment::class);
    }
}
