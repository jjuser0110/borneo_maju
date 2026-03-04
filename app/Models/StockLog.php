<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockLog extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'bank_setting_id',
        'stock_id',
        'order_id',
        'idr_amount',
        'stock_idr_rate',
        'capital_used',
        'remarks',
    ];

    public function bankSetting()
    {
        return $this->belongsTo(BankSetting::class);
    }

    public function stock()
    {
        return $this->belongsTo(Stock::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
