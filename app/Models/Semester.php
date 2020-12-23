<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Semester extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    public function Weeks()
    {
        return $this->hasMany(Week::class);
    }

    public function Assignments()
    {
        return $this->hasMany(Assignment::class);
    }
}
