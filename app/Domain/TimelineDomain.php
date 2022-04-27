<?php

namespace App\Domain;

use App\Models\AccessLog;
use App\Models\ExecuteStampSheetLog;
use App\Models\ExecuteStampTaskLog;
use App\Models\Gcp;
use App\Models\IssueStampSheetLog;
use App\Models\Timeline;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class TimelineDomain extends BaseDomain {

    public string $userId;
    public string $transactionId;

    public function __construct(
        string $userId,
        string $transactionId,
    ) {
        $this->userId = $userId;
        $this->transactionId = $transactionId;
    }

    /** @noinspection PhpIncompatibleReturnTypeInspection */
    public function model(): Timeline
    {
        return Timeline::query()
            ->where("transactionId", $this->transactionId)
            ->first();
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

    /** @noinspection PhpIncompatibleReturnTypeInspection */
    public function executeStampTaskLogs(): array
    {
        $executeStampTaskLogs = ExecuteStampTaskLog::query()
            ->where("transactionId", $this->transactionId)
            ->get();
        $executeStampTaskLogMap = [];
        foreach ($executeStampTaskLogs as $executeStampTaskLog) {
            $result = json_decode($executeStampTaskLog->result, true);
            if (in_array('stampSheet', array_keys($result))) {
                unset($result['stampSheet']);
            }
            if (in_array('stampSheetEncryptionKeyId', array_keys($result))) {
                unset($result['stampSheetEncryptionKeyId']);
            }
            $executeStampTaskLog->result = json_encode($result);
            $executeStampTaskLogMap[$executeStampTaskLog->taskId] = $executeStampTaskLog;
        }
        return $executeStampTaskLogMap;
    }

    public static function actions(
        string $userId,
    ) {
        $groups = Timeline::query()
            ->select(DB::raw("REPLACE(REPLACE(REPLACE(action, 'ByUserId', ''), 'ByStampTask', ''), 'ByStampSheet', '') as action"))
            ->where("userId", $userId)
            ->where("timeline.action", 'not like', '%StampSheet')
            ->where("timeline.action", 'not like', '%StampTask')
            ->groupBy(DB::raw("REPLACE(REPLACE(REPLACE(action, 'ByUserId', ''), 'ByStampTask', ''), 'ByStampSheet', '')"))
            ->get();
        $actions = [];
        foreach ($groups as $group) {
            $actions[] = $group->action;
        }
        return $actions;
    }

    public static function list(
        string $userId,
        \DateTime|null $beginAt,
        \DateTime|null $endAt,
        array $actions = null,
    ): Builder {
        $gcp = Gcp::query()->first();
        if ($beginAt == null) {
            $beginAt = $gcp->beginAt;
        }
        if ($endAt == null) {
            $endAt = $gcp->endAt;
        }

        $timelines = Timeline::query()
            ->select(
                "timeline.*",
                "issueStampSheetJoinTask.action as taskAction",
            )
            ->leftJoin("issueStampSheetJoinTask", 'timeline.transactionId', 'issueStampSheetJoinTask.transactionId')
            ->whereBetween('timestamp', [$beginAt->setTimezone(new \DateTimeZone('UTC')), $endAt->setTimezone(new \DateTimeZone('UTC'))])
            ->where("userId", $userId)
            ->where("timeline.action", 'not like', '%StampSheet')
            ->where("timeline.action", 'not like', '%StampTask')
            ->orderBy("timestamp", "desc");
        if (isset($actions)) {
            $timelines->where(function($query) use ($actions) {
                $query->whereIn(DB::raw("REPLACE(REPLACE(REPLACE(timeline.action, 'ByUserId', ''), 'ByStampTask', ''), 'ByStampSheet', '')"), $actions);
                $query->orWhereIn(DB::raw("REPLACE(REPLACE(REPLACE(rewardAction, 'ByUserId', ''), 'ByStampTask', ''), 'ByStampSheet', '')"), $actions);
                $query->orWhereIn(DB::raw("REPLACE(REPLACE(REPLACE(issueStampSheetJoinTask.action, 'ByUserId', ''), 'ByStampTask', ''), 'ByStampSheet', '')"), $actions);
            });
        }
        return $timelines;
    }
}
