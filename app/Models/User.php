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
        'limit',
        'idr_limit',
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

    public function orders()
    {
        return $this->hasMany('App\Models\Order');
    }

    public function uplineUser()
    {
        return $this->belongsTo(User::class, 'upline');
    }

    public function isAdmin(): bool
    {
        return (int) $this->role_id === 1;
    }

    public function isAgent(): bool
    {
        return (int) $this->role_id === 3;
    }

    /**
     * True when $this sits anywhere below $user in the upline chain.
     */
    public function isDownlineOf(User $user): bool
    {
        $current = $this->uplineUser;
        $seen = [];

        while ($current && !isset($seen[$current->id])) {
            if ((int) $current->id === (int) $user->id) {
                return true;
            }
            $seen[$current->id] = true; // guard against loops in bad data
            $current = $current->uplineUser;
        }

        return false;
    }

    /**
     * Can $this (the logged-in user) see the given agent's pages?
     * Admins see everyone; agents only see their own downline tree.
     */
    public function canView(User $agent): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        return $this->isAgent() && $agent->isDownlineOf($this);
    }

    /**
     * Can $this change the given agent (edit, move points)?
     * Admins: any agent. Agents: only their direct downline.
     */
    public function canManage(User $agent): bool
    {
        if (!$agent->isAgent()) {
            return false;
        }

        if ($this->isAdmin()) {
            return true;
        }

        return $this->isAgent() && (int) $agent->upline === (int) $this->id;
    }
}
