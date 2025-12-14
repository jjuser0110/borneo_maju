<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PointHistory extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        'agent_id',
        'point_before',
        'point',
        'point_after',
        'description',
    ];

    public function agent()
    {
        return $this->belongsTo('App\Models\User', 'agent_id');
    }
}
