<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Week extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    public function Semester()
    {
        return $this->belongsTo(Semester::class);
    }
}
