<?php

namespace App\Models;

use App\Domain\PlayerDomain;
use DateTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Player extends Model
{
    protected $table = 'player';

    protected $primaryKey = 'userId';

    protected $dates = [
        'lastAccessAt',
    ];

    protected $fillable = [
        'userId',
        'fetchedBeginAt',
        'fetchedEndAt',
        'lastAccessAt',
    ];

    const CREATED_AT = null;
    const UPDATED_AT = null;

    public function toDomainModel(): PlayerDomain
    {
        return new PlayerDomain($this->userId);
    }

    public function getUserIdAttribute(string $value): string
    {
        return $value;
    }

    public function isNeedFetch(
        DateTime $startAt,
        DateTime $endAt,
    ): bool
    {
        if (is_null($this->fetchedBeginAt) || $this->fetchedBeginAt->getTimestamp() > $startAt->getTimestamp()) {
            return true;
        }
        if (is_null($this->fetchedEndAt) || $this->fetchedEndAt->getTimestamp() < $endAt->getTimestamp()) {
            return true;
        }
        return false;
    }

    public function fetched(
        DateTime $startAt,
        DateTime $endAt,
    ): bool
    {
        $this->update(
            [
                "fetchedBeginAt" => $startAt->getTimestamp(),
                "fetchedEndAt" => $endAt->getTimestamp(),
            ]
        );
        return false;
    }

    public function getFetchedBeginAtAttribute($value): Carbon | null
    {
        if (is_null($value)) return null;
        return Carbon::createFromTimestampUTC($value)
            ->timezone(date_default_timezone_get());
    }

    public function getFetchedEndAtAttribute($value): Carbon | null
    {
        if (is_null($value)) return null;
        return Carbon::createFromTimestampUTC($value)
            ->timezone(date_default_timezone_get());
    }

    public function getLastAccessAtAttribute($value): string
    {
        return Carbon::createFromFormat('Y-m-d H:i:s', $value, "UTC")
            ->timezone(date_default_timezone_get())
            ->format('Y-m-d H:i:s');
    }
}
