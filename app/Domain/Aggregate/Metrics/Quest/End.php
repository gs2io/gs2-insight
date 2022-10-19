<?php

namespace App\Domain\Aggregate\Metrics\Quest;

use App\Domain\Aggregate\Metrics\AbstractMetrics;
use App\Domain\Aggregate\Metrics\Common\Result\LoadResult;
use App\Models\Metrics;
use DatePeriod;
use DateTime;
use JetBrains\PhpStorm\Pure;

class End extends AbstractMetrics
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

        $service = 'quest';
        $method = 'end';
        $methods = [
            "{$method}",
            "{$method}ByUserId",
            "{$method}ByStampSheet",
            "{$method}ByStampTask",
        ];
        $groupByFieldNames = [
            'namespaceName',
            'questGroupName',
            'questName',
        ];

        $client = $this->createClient();
        $sql = "
            SELECT
                CONCAT(FORMAT_DATETIME('%Y-%m-%d %H', timestamp), ':00:00') AS date,
        " . implode(
                "",
                array_map(
                    function (string $groupByFieldName) {
                        if ($groupByFieldName == "questGroupName") {
                            return "
                                IFNULL(SPLIT(JSON_EXTRACT_SCALAR(result, '$.item.questModelId'), ':')[OFFSET(7)], '') as $groupByFieldName,
                            ";
                        }
                        if ($groupByFieldName == "questName") {
                            return "
                                IFNULL(SPLIT(JSON_EXTRACT_SCALAR(result, '$.item.questModelId'), ':')[OFFSET(9)], '') as $groupByFieldName,
                            ";
                        }
                        return "
                            CONCAT(
                                IFNULL(JSON_EXTRACT_SCALAR(request, '$.$groupByFieldName'), ''),
                                IFNULL(JSON_EXTRACT_SCALAR(JSON_EXTRACT_SCALAR(JSON_EXTRACT_SCALAR(JSON_EXTRACT_SCALAR(request, '$.stampSheet'), '$.body'), '$.args'), '$.$groupByFieldName'), ''),
                                IFNULL(JSON_EXTRACT_SCALAR(JSON_EXTRACT_SCALAR(JSON_EXTRACT_SCALAR(JSON_EXTRACT_SCALAR(request, '$.stampTask'), '$.body'), '$.args'), '$.$groupByFieldName'), '')
                            ) as $groupByFieldName,
                        ";
                    },
                    $groupByFieldNames,
                )
            ) . "
                COUNT(*) AS value,
            FROM
                `{$this->table()}`
            WHERE
                {$this->timeRange()} AND
                service = '{$service}' AND
                method IN (
        " . implode(
                ",",
                array_map(
                    function (string $methodName) {
                        return "'$methodName'";
                    },
                    $methods,
                )
            ) . ")
            GROUP BY
                date,
        " . implode(
                ",",
                array_map(
                    function (string $groupByFieldName) {
                        return "$groupByFieldName";
                    },
                    $groupByFieldNames,
                )
            ) . "
        ";
        $query = $client->query($sql);
        $query->allowLargeResults(true);
            $items = $client->runQuery($query, [
                'maxResults' => 1000,
            ]);

        $totalBytesProcessed = $items->info()['totalBytesProcessed'];

        $metrics = [];
        foreach ($items as $item) {
            $keys = implode(
                ":",
                array_map(
                    function ($key) use ($item) {
                        return $key . ':' . $item[$key];
                    },
                    $groupByFieldNames,
                ),
            );
            $metrics[] = Metrics::query()->updateOrCreate(
                ["metricsId" => "$service:$method:count:$keys:{$item["date"]}"],
                [
                    "key" => "$service:$method:count:$keys",
                    "value" => $item["value"],
                    "timestamp" => DateTime::createFromFormat('Y-m-d H:i:s', $item["date"]),
                ],
            );
        }
        return new LoadResult(
            $totalBytesProcessed,
        );
    }
}
