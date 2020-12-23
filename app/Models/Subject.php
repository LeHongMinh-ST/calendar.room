<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subject extends Model
{
    use SoftDeletes;
    protected $fillable = ['id','subject_id','department_id','name','user_create_id','user_update_id','created_at','deleted_at'];
    public function Department()
    {
        return $this->belongsTo(Department::class);
    }

    public function Schedule()
    {
        return $this->hasMany(Schedule::class);
    }
}
