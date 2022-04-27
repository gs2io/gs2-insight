<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Grn extends Model
{
    protected $table = 'grn';

    public $incrementing = false;

    protected $primaryKey = 'grn';

    protected $dates = [];

    protected $fillable = [
        'grn',
        'parent',
        'category',
        'key',
    ];

    const CREATED_AT = null;
    const UPDATED_AT = null;
}
