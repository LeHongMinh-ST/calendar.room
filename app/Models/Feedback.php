<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Feedback extends Model
{
    protected $table = 'feedbacks';
    use SoftDeletes;

    public function User()
    {
        return $this->belongsTo(User::class);
    }


}
