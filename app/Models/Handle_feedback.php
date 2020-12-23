<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Handle_feedback extends Model
{
    protected $table = 'handle_feedbacks';
    use SoftDeletes;
}
