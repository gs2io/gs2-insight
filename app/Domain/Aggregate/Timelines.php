<?php

namespace App\Domain\Aggregate;

use App\Domain\Aggregate\Metrics\Common\Result\LoadResult;
use App\Models\AccessLog;
use App\Models\ExecuteStampSheetLog;
use App\Models\ExecuteStampTaskLog;
use App\Models\Gcp;
use App\Models\IssueStampSheetLog;
use App\Models\IssueStampSheetLogJoinTask;
use App\Models\LoadStatus;
use App\Models\Timeline;
use DatePeriod;
use Google\Cloud\BigQuery\BigQueryClient;
use Google\Cloud\Core\Exception\NotFoundException;
use Illuminate\Support\Carbon;
use JetBrains\PhpStorm\Pure;
use function Amp\ParallelFunctions\parallelMap;
use function Amp\Promise\wait;

class Timelines extends AbstractAggregate
{
    #[Pure] public function __construct(
        DatePeriod $period,
        string $datasetName,
        string $credentials,
    )
    {
        parent::__construct(
            $period,
            $datasetName,
            $credentials,
        );
    }

    public function load(): LoadResult {

        $totalSteps = 3;
        $step = 0;
        $totalBytesProcessed = 0;

        $workingStatus = LoadStatus::query()->find("global:timeline");
        if ($workingStatus == null) {
            $workingStatus = LoadStatus::query()->create([
                "scope" => "global:timeline",
                "working" => "issueStampSheet",
                "progress" => 0.0,
                "totalBytesProcessed" => 0,
            ]);
        }

        $bigQuery = $this->createClient();
        $query = $bigQuery->query("
            SELECT
                transactionId, service, method, action, args, tasks, userId, timestamp
            FROM
                `{$this->table('IssueStampSheet')}`
            WHERE
                {$this->timeRange()}
        ");
        $query->allowLargeResults(true);
        $items = $bigQuery->runQuery($query, [
            'maxResults' => 1000,
        ]);

        $rows = 0;
        $totalRows = $items->info()["totalRows"];

        foreach ($items as $item) {
            $rows++;
            if ($rows % 1000 == 0) {
                $workingStatus->update([
                    "progress" => $step / $totalSteps + (1 / $totalSteps) * ($rows / $totalRows),
                ]);
            }

            Timeline::query()->firstOrCreate(
                ["transactionId" => $item["transactionId"]],
                [
                    "type" => 'issueStampSheet',
                    "userId" => array_key_exists('userId', $item) ? $item['userId'] : json_decode($item["args"], true)['userId'],
                    "action" => 'Gs2' . ucwords($item["service"]) . ':' . ucwords($item["method"]),
                    "rewardAction" => $item["action"],
                    "rewardArgs" => $item["args"],
                    "timestamp" => $item["timestamp"],
                ],
            );

            IssueStampSheetLog::query()->firstOrCreate(
                ["transactionId" => $item["transactionId"]],
                [
                    "type" => 'issueStampSheet',
                    "userId" => array_key_exists('userId', $item) ? $item['userId'] : json_decode($item["args"], true)['userId'],
                    "service" => $item["service"],
                    "method" => $item["method"],
                    "action" => $item["action"],
                    "args" => $item["args"],
                    "tasks" => $item["tasks"],
                    "timestamp" => Carbon::createFromInterface($item["timestamp"]->get()),
                ],
            );

            $tasks = json_decode($item["tasks"], true);
            foreach ($tasks as $task) {
                $task = json_decode($task, true);
                IssueStampSheetLogJoinTask::query()->firstOrCreate(
                    ["transactionId" => $item["transactionId"]],
                    [
                        "taskId" => $task["taskId"],
                        "action" => $task["action"],
                    ],
                );
            }
        }

        $totalBytesProcessed += $items->info()['totalBytesProcessed'];
        $step++;
        $workingStatus->update([
            "progress" => $step / $totalSteps,
            "totalBytesProcessed" => $totalBytesProcessed,
        ]);

        $query = $bigQuery->query("
        SELECT
            a.taskId,
            c.transactionId,
            b.service,
            b.method,
            a.action,
            a.userId,
            a.args,
            b.result,
            a.timestamp
        FROM
            (
                SELECT
                    taskId,
                    action,
                    args,
                    userId,
                    timestamp
                FROM
                    `{$this->table('ExecuteStampTask')}`
                WHERE
                    {$this->timeRange()}
            ) as a
            LEFT JOIN
            (
                SELECT
                    JSON_EXTRACT_SCALAR(JSON_EXTRACT_SCALAR(task, '$.body'), '$.taskId') as taskId,
                    service,
                    method,
                    result
               FROM
               (
                    SELECT
                        JSON_EXTRACT_STRING_ARRAY(JSON_EXTRACT_SCALAR(JSON_EXTRACT_SCALAR(result, '$.stampSheet'), '$.body'), '$.tasks') as tasks,
                        service,
                        method,
                        result
                    FROM
                        `{$this->table('Invoke')}`
                    WHERE
                        {$this->timeRange()} AND
                        JSON_EXTRACT_STRING_ARRAY(JSON_EXTRACT_SCALAR(JSON_EXTRACT_SCALAR(result, '$.stampSheet'), '$.body'), '$.tasks') IS NOT NULL
               ),
               UNNEST(tasks) as task
               WHERE
                    JSON_EXTRACT_SCALAR(JSON_EXTRACT_SCALAR(task, '$.body'), '$.taskId') IS NOT NULL
            ) as b
            ON
                a.taskId = b.taskId
            LEFT JOIN
            (
                SELECT
                    transactionId,
                    JSON_EXTRACT_SCALAR(task, '$.taskId') as taskId
                FROM
                (
                    SELECT
                        transactionId,
                        JSON_EXTRACT_STRING_ARRAY(tasks, '$.') as tasks,
                    FROM
                        `{$this->table('IssueStampSheet')}`
                    WHERE
                        {$this->timeRange()}
                ),
                UNNEST(tasks) as task
            ) as c
            ON
                a.taskId = c.taskId
        ");
        $query->allowLargeResults(true);
        $items = $bigQuery->runQuery($query, [
            'maxResults' => 1000,
        ]);

        $rows = 0;
        $totalRows = $items->info()["totalRows"];

        foreach ($items as $item) {
            $rows++;
            if ($rows % 1000 == 0) {
                $workingStatus->update([
                    "progress" => $step / $totalSteps + (1 / $totalSteps) * ($rows / $totalRows),
                ]);
            }
            if (isset($item["transactionId"]) && isset($item["service"])) {
                ExecuteStampTaskLog::query()->firstOrCreate(
                    ["taskId" => $item["taskId"]],
                    [
                        "userId" => array_key_exists('userId', $item) ? $item['userId'] : json_decode($item["args"], true)['userId'],
                        "transactionId" => $item["transactionId"],
                        "service" => $item["service"],
                        "method" => $item["method"],
                        "action" => $item["action"],
                        "args" => $item["args"],
                        "result" => $item["result"],
                        "timestamp" => Carbon::createFromInterface($item["timestamp"]->get()),
                    ],
                );
            }
        }

        $totalBytesProcessed += $items->info()['totalBytesProcessed'];
        $step++;
        $workingStatus->update([
            "progress" => $step / $totalSteps,
            "totalBytesProcessed" => $totalBytesProcessed,
        ]);

        $query = $bigQuery->query("
            SELECT
                a.transactionId,
                b.service,
                b.method,
                a.action,
                a.userId,
                a.args,
                b.result,
                a.timestamp
            FROM
                (
                    SELECT
                        transactionId,
                        action,
                        args,
                        userId,
                        timestamp
                    FROM
                        `{$this->table('ExecuteStampSheet')}`
                    WHERE
                        {$this->timeRange()}
                ) as a
                LEFT JOIN
                (
                    SELECT
                        JSON_EXTRACT_SCALAR(JSON_EXTRACT_SCALAR(JSON_EXTRACT_SCALAR(request, '$.stampSheet'), '$.body'), '$.transactionId') as transactionId,
                        service,
                        method,
                        result
                    FROM
                        `{$this->table('Invoke')}`
                    WHERE
                        {$this->timeRange()} AND
                        JSON_EXTRACT_SCALAR(JSON_EXTRACT_SCALAR(JSON_EXTRACT_SCALAR(request, '$.stampSheet'), '$.body'), '$.transactionId') IS NOT NULL
                ) as b
                ON
                    a.transactionId = b.transactionId
        ");
        $query->allowLargeResults(true);
        $items = $bigQuery->runQuery($query, [
            'maxResults' => 1000,
        ]);

        $rows = 0;
        $totalRows = $items->info()["totalRows"];

        foreach ($items as $item) {
            $rows++;
            if ($rows % 1000 == 0) {
                $workingStatus->update([
                    "progress" => $step / $totalSteps + (1 / $totalSteps) * ($rows / $totalRows),
                ]);
            }
            if (isset($item["service"])) {
                $result = json_decode($item["result"], true);
                if (in_array('result', array_keys($result))) {
                    $item["result"] = $result['result'];
                }
                ExecuteStampSheetLog::query()->firstOrCreate(
                    ["transactionId" => $item["transactionId"]],
                    [
                        "userId" => array_key_exists('userId', $item) ? $item['userId'] : json_decode($item["args"], true)['userId'],
                        "service" => $item["service"],
                        "method" => $item["method"],
                        "action" => $item["action"],
                        "args" => $item["args"],
                        "result" => $item["result"],
                        "timestamp" => Carbon::createFromInterface($item["timestamp"]->get()),
                    ],
                );
            }
        }

        $totalBytesProcessed += $items->info()['totalBytesProcessed'];
        $step++;
        $workingStatus->update([
            "progress" => $step / $totalSteps,
            "totalBytesProcessed" => $totalBytesProcessed,
        ]);

        return new LoadResult(
            $totalBytesProcessed,
        );
    }

    public function loadDetail(
        string $userId,
    ): LoadResult {

        $totalSteps = 1;
        $step = 0;
        $totalBytesProcessed = 0;

        $workingStatus = LoadStatus::query()->find("$userId:timeline");
        if ($workingStatus == null) {
            $workingStatus = LoadStatus::query()->create([
                "scope" => "$userId:timeline",
                "working" => "invoke",
                "progress" => 0.0,
                "totalBytesProcessed" => 0,
            ]);
        }

        $bigQuery = $this->createClient();
        $query = $bigQuery->query("
            SELECT
                requestId, service, method, userId, request, result, timestamp
            FROM
                `{$this->table('Invoke')}`
            WHERE
                {$this->timeRange()} AND
                method not like 'get%' AND method not like 'describe%' AND
                userId = @userId
        ")->parameters([
            'userId' => $userId,
        ]);
        $query->allowLargeResults(true);
        $items = $bigQuery->runQuery($query, [
            'maxResults' => 1000,
        ]);

        $totalBytesProcessed += $items->info()['totalBytesProcessed'];

        $rows = 0;
        $totalRows = $items->info()["totalRows"];

        foreach ($items as $item) {
            $rows++;
            if ($rows % 1000 == 0) {
                $workingStatus->update([
                    "progress" => $step / $totalSteps + (1 / $totalSteps) * ($rows / $totalRows),
                ]);
            }
            $request = json_decode($item["request"], true);
            if ($request === null) continue;
            if (array_key_exists('contextStack', $request)) {
                unset($request['contextStack']);
            }
            if (array_key_exists('accessToken', $request)) {
                unset($request['accessToken']);
            }
            Timeline::query()->firstOrCreate(
                ["transactionId" => $item["requestId"]],
                [
                    "type" => 'access',
                    "userId" => $item['userId'],
                    "action" => 'Gs2' . ucwords($item["service"]) . ':' . ucwords($item["method"]),
                    "args" => json_encode($request),
                    "timestamp" => $item["timestamp"],
                ],
            );
            $result = json_decode($item["result"], true);
            if (array_key_exists('stampSheet', $result)) {
                unset($result['stampSheet']);
            }
            if (array_key_exists('stampSheetEncryptionKeyId', $result)) {
                unset($result['stampSheetEncryptionKeyId']);
            }
            AccessLog::query()->firstOrCreate(
                ["requestId" => $item["requestId"]],
                [
                    "requestId" => $item["requestId"],
                    "service" => $item['service'],
                    "method" => $item["method"],
                    "userId" => $item["userId"],
                    "request" => json_encode($request),
                    "result" => json_encode($result),
                    "timestamp" => Carbon::createFromInterface($item["timestamp"]->get()),
                ],
            );
        }

        $totalBytesProcessed += $items->info()['totalBytesProcessed'];
        $step++;
        $workingStatus->update([
            "progress" => $step / $totalSteps,
            "totalBytesProcessed" => $totalBytesProcessed,
        ]);

        return new LoadResult($totalBytesProcessed);
    }
}
