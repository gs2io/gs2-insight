<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IssueStampSheetLogJoinTask extends Model
{
    protected $table = 'issueStampSheetJoinTask';

    protected $primaryKey = 'transactionId';

    protected $fillable = [
        'transactionId',
        'taskId',
        'action',
    ];

    public function getTransactionIdAttribute(string | null $value): string | null
    {
        return $value;
    }

    public function getTaskIdAttribute(string | null $value): string | null
    {
        return $value;
    }

    const CREATED_AT = null;
    const UPDATED_AT = null;
}
