<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Department extends Model
{
    use SoftDeletes;
    protected $fillable = ['id','department_id','name','user_create_id','user_update_id','created_at','deleted_at'];

    public function Subjects()
    {
        return $this->hasMany(Subject::class);
    }

    public function Users()
    {
        return $this->hasMany(User::class);
    }
}
