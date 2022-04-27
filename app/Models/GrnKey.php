<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GrnKey extends Model
{
    protected $table = 'grnKey';

    public $incrementing = false;

    protected $primaryKey = 'keyId';

    protected $dates = [];

    protected $fillable = [
        'keyId',
        'grn',
        'category',
        'requestId',
    ];

    const CREATED_AT = null;
    const UPDATED_AT = null;
}
