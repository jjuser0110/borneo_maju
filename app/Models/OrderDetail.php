<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderDetail extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'order_id',
        'user_id',
        'idr_rate',
        'myr_amount',
        'idr_amount',
        'processing_fees',
        'total_amount',
        'upline',
        'do_up',
        'profit',
        'agent_do_up',
    ];

    public function order()
    {
        return $this->belongsTo('App\Models\Order', 'order_id');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }
}
