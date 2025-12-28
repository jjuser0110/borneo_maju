<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FileAttachment extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'content_id',
        'content_type',
        'file_path',
        'file_name',
        'file_type',
        'type',
    ];

    public function content()
    {
        return $this->morphTo();
    }
}
