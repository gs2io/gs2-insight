<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class ExecuteStampSheetLog extends Model
{
    protected $table = 'executeStampSheet';

    protected $primaryKey = 'transactionId';

    protected $fillable = [
        'transactionId',
        'service',
        'method',
        'userId',
        'action',
        'args',
        'result',
        'timestamp',
    ];

    public function getTransactionIdAttribute(string $value): string
    {
        return $value;
    }

    public function getUserIdAttribute(string $value): string
    {
        return $value;
    }

    public function getTimestampAttribute($value): string
    {
        return Carbon::createFromFormat('Y-m-d H:i:s', $value, "UTC")
            ->timezone(date_default_timezone_get())
            ->format('Y-m-d H:i:s');
    }

    const CREATED_AT = null;
    const UPDATED_AT = null;
}
