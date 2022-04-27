<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class IssueStampSheetLog extends Model
{
    protected $table = 'issueStampSheet';

    protected $primaryKey = 'transactionId';

    protected $fillable = [
        'transactionId',
        'service',
        'method',
        'action',
        'userId',
        'args',
        'tasks',
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
