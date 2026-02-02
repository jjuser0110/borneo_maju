<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BankLog extends Model
{
    use HasFactory;
    use SoftDeletes;
    
    protected $fillable = [
        'content_id',
        'content_type',
        'bank_setting_id',
        'type',
        'remarks',
        'prev_amount',
        'amount',
        'after_amount',
    ];

    public function bank_setting()
    {
        return $this->belongsTo(BankSetting::class);
    }

    public function content()
    {
        return $this->morphTo();
    }
}
