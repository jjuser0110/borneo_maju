<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Silber\Bouncer\Database\HasRolesAndAbilities;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable,HasRolesAndAbilities,SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'remember_token',
        'username',
        'role_id',
        'upline',
        'is_active',
        'idr_rate',
        'point',
        'processing_fees',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function role()
    {
        return $this->belongsTo('App\Models\Role');
    }

    public function save_history()
    {
        return $this->morphMany('App\Models\SaveHistory', 'content');
    }

    public function point_history()
    {
        return $this->hasMany('App\Models\PointHistory','agent_id')->orderBy('created_at', 'desc');
    }

    public function downlines()
    {
        return $this->hasMany('App\Models\User', 'upline', 'id');
    }

    public function order_details()
    {
        return $this->hasMany('App\Models\OrderDetail');
    }
}
