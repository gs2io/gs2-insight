<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoadStatus extends Model
{
    protected $table = 'loadStatus';

    public $incrementing = false;

    protected $primaryKey = 'scope';

    protected $dates = [];

    protected $fillable = [
        'scope',
        'working',
        'progress',
        'totalBytesProcessed',
    ];

    const CREATED_AT = null;
    const UPDATED_AT = null;
}
