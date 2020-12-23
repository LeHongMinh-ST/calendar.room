<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Faculty extends Model
{
    use SoftDeletes;
    protected $fillable = ['id','faculty_id','name','user_create_id','user_update_id','is_active','updated_at','created_at','deleted_at'];
    public function Department()
    {
        return $this->hasMany(Department::class);
    }
}
