<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SaveHistory extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'content_type',
        'content_id',
        'field_name',
        'old_value',
        'new_value',
    ];

    public function content()
    {
        return $this->morphTo();
    }

}
