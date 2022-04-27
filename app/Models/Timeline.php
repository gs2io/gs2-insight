<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Timeline extends Model
{
    protected $table = 'timeline';

    protected $primaryKey = 'transactionId';

    protected $dates = ['timestamp'];

    protected $fillable = [
        'transactionId',
        'type',
        'userId',
        'action',
        'args',
        'rewardAction',
        'rewardArgs',
        'result',
        'timestamp',
    ];

    const CREATED_AT = null;
    const UPDATED_AT = null;

    public function isAccessLog(): bool
    {
        return $this->type == 'access';
    }

    public function isIssueStampSheetLog(): bool
    {
        return $this->type == 'issueStampSheet';
    }

    /** @noinspection PhpIncompatibleReturnTypeInspection */
    public function accessLog(): AccessLog | null
    {
        return AccessLog::query()
            ->where("requestId", $this->transactionId)
            ->first();
    }

    /** @noinspection PhpIncompatibleReturnTypeInspection */
    public function issueStampSheetLog(): IssueStampSheetLog | null
    {
        return IssueStampSheetLog::query()
            ->where("transactionId", $this->transactionId)
            ->first();
    }

    /** @noinspection PhpIncompatibleReturnTypeInspection */
    public function executeStampSheetLog(): ExecuteStampSheetLog | null
    {
        return ExecuteStampSheetLog::query()
            ->where("transactionId", $this->transactionId)
            ->first();
    }

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
}
