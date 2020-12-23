<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Assignment extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    public function Room()
    {
        return $this->belongsTo(Room::class);
    }

    public function Semester()
    {
        return $this->belongsTo(Semester::class);
    }
}
