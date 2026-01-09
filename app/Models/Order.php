<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'order_no',
        'order_datetime',
        'bank_id',
        'account_no',
        'fullname',
        'idr_rate',
        'myr_amount',
        'idr_amount',
        'processing_fees',
        'total_amount',
        'status',
        'status_at',
        'status_by_id',
        'duration',
        'remarks',
    ];

    public function bank()
    {
        return $this->belongsTo('App\Models\Bank', 'bank_id');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    public function status_by()
    {
        return $this->belongsTo('App\Models\User', 'status_by_id');
    }

    public function file_attachments()
    {
        return $this->morphMany('App\Models\FileAttachment', 'content');
    }

    public function details()
    {
        return $this->hasMany('App\Models\OrderDetail', 'order_id');
    }

    public function last_image()
    {
        return $this->morphOne('App\Models\FileAttachment', 'content')->latest();
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class)
            ->whereColumn('order_details.user_id', 'orders.user_id');
    }



    
}

            