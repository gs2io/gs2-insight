<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Gcp extends Model
{
    protected $table = 'gcp';

    protected $primaryKey = 'datasetName';

    protected $dates = [];

    protected $fillable = [
        'datasetName',
        'beginAt',
        'endAt',
        'credentials',
    ];

    const CREATED_AT = null;
    const UPDATED_AT = null;

    public function getDatasetNameAttribute($value): string
    {
        return $value;
    }

    public function getBeginAtAttribute($value): Carbon | null
    {
        if (is_null($value)) return null;
        return Carbon::createFromTimestampUTC($value)
            ->timezone(date_default_timezone_get());
    }

    public function getEndAtAttribute($value): Carbon | null
    {
        if (is_null($value)) return null;
        return Carbon::createFromTimestampUTC($value)
            ->timezone(date_default_timezone_get());
    }

    public function getBeginAtString(): string
    {
        return $this->beginAt->format('Y-m-d H:i:s');
    }

    public function getEndAtString(): string
    {
        return $this->endAt->format('Y-m-d H:i:s');
    }
}
