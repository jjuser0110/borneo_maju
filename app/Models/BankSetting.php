<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BankSetting extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'bank_id',
        'account_no',
        'owner_name',
        'is_active',
        'capital',
        'amount',
    ];

    public function bank()
    {
        return $this->belongsTo(Bank::class, 'bank_id');
    }

    public function bank_logs()
    {
        return $this->morphMany('App\Models\BankLog', 'content')->orderBy('created_at', 'desc');
    }

    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }

    public function getStockAmountAttribute()
    {
        return $this->hasMany(Stock::class)->sum('idr_balance');
    }
}
