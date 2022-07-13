<?php

namespace App\Domain\Aggregate;

use App\Domain\Aggregate\Metrics\Common\Result\LoadResult;
use App\Models\AccessLog;
use App\Models\ExecuteStampSheetLog;
use App\Models\ExecuteStampTaskLog;
use App\Models\Gcp;
use App\Models\IssueStampSheetLog;
use App\Models\IssueStampSheetLogJoinTask;
use App\Models\Timeline;
use DatePeriod;
use Google\Cloud\BigQuery\BigQueryClient;
use Illuminate\Support\Carbon;
use JetBrains\PhpStorm\Pure;

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

        $totalBytesProcessed = 0;

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
        $items = $bigQuery->runQuery($query);

        $totalBytesProcessed += $items->info()['totalBytesProcessed'];

        foreach ($items as $item) {
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
        $items = $bigQuery->runQuery($query);

        $totalBytesProcessed += $items->info()['totalBytesProcessed'];

        foreach ($items as $item) {
            if (isset($item["transactionId"])) {
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
        $items = $bigQuery->runQuery($query);

        $totalBytesProcessed += $items->info()['totalBytesProcessed'];

        foreach ($items as $item) {
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

        return new LoadResult(
            $totalBytesProcessed,
        );
    }

    public function loadDetail(
        string $userId,
    ): LoadResult {
        $totalBytesProcessed = 0;

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
        $result = $bigQuery->runQuery($query);

        $totalBytesProcessed += $result->info()['totalBytesProcessed'];

        foreach ($result as $item) {
            $request = json_decode($item["request"], true);
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

        return new LoadResult($totalBytesProcessed);
    }
}
