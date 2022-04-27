<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gs2 extends Model
{
    protected $table = 'gs2';

    public $incrementing = false;

    protected $primaryKey = 'clientId';

    protected $dates = [];

    protected $fillable = [
        'clientId',
        'clientSecret',
        'region',
        'permission',
    ];

    const CREATED_AT = null;
    const UPDATED_AT = null;
}
