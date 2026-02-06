<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Stock extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'bank_setting_id',
        'idr_rate',
        'myr_amount',
        'idr_amount',
        'idr_balance',
    ];

    public function bankSetting()
    {
        return $this->belongsTo(BankSetting::class);
    }

    public function stock_logs()
    {
        return $this->hasMany(StockLog::class);
    }

    public function profits()
    {
        return $this->hasMany(Profit::class);
    }
}
