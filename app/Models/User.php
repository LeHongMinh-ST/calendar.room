<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class User extends Model
{
    use Notifiable;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','full_name','phone',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $dates = ['deleted_at'];

    public function Role()
    {
        return $this->belongsTo(Role::class);
    }

    public function Department()
    {
        return $this->belongsTo(Department::class);
    }

    public function Registers()
    {
        return $this->hasMany(Register::class);
    }

    public function Feedbacks()
    {
        return $this->hasMany(Feedback::class);
    }

    public function Handle_feedbacks()
    {
        return $this->hasMany(Handle_feedback::class);
    }
}
