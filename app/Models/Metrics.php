<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Metrics extends Model
{
    protected $table = 'metrics';

    protected $primaryKey = 'metricsId';

    protected $dates = ['timestamp'];

    protected $fillable = [
        'metricsId',
        'key',
        'value',
        'timestamp',
    ];

    const CREATED_AT = null;
    const UPDATED_AT = null;

    public function getTimestampAttribute($value): string
    {
        return Carbon::createFromFormat('Y-m-d H:i:s', $value, "UTC")
            ->timezone(date_default_timezone_get())
            ->format('Y-m-d H:i:s');
    }
}
