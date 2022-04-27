<?php

namespace App\Domain\Aggregate\Metrics\Common;

use App\Domain\Aggregate\Metrics\Common\Result\LoadMetricsResult;
use App\Models\Metrics;
use DateTime;
use Google\Cloud\BigQuery\BigQueryClient;

class MetricsLoader {

    public static function buildCountQuery(
        string $service,
        string $tableName,
        string $timeRange,
        array $methodNames,
        array $groupByFieldNames,
        array $unnestFieldNames,
    ): string {
        return "
            SELECT
                CONCAT(FORMAT_DATETIME('%Y-%m-%d %H', timestamp), ':00:00') AS date,
        " . implode(
                "",
                array_map(
                    function (string $groupByFieldName) use ($methodNames, $service, $unnestFieldNames) {
                        if (in_array($groupByFieldName, $unnestFieldNames)) {
                            return "$groupByFieldName,";
                        } else {
                            if ($service == 'script' && $methodNames[0] == 'invoke' && $groupByFieldName == 'namespaceName') {
                                return "
                                    SPLIT(JSON_EXTRACT_SCALAR(request, '$.scriptId'), ':')[offset(5)] as $groupByFieldName,
                                ";
                            } else if ($service == 'script' && $methodNames[0] == 'invoke' && $groupByFieldName == 'scriptName') {
                                return "
                                    SPLIT(JSON_EXTRACT_SCALAR(request, '$.scriptId'), ':')[offset(7)] as $groupByFieldName,
                                ";
                            } else {
                                return "
                                    CONCAT(
                                        IFNULL(JSON_EXTRACT_SCALAR(request, '$.$groupByFieldName'), ''),
                                        IFNULL(JSON_EXTRACT_SCALAR(JSON_EXTRACT_SCALAR(JSON_EXTRACT_SCALAR(JSON_EXTRACT_SCALAR(request, '$.stampSheet'), '$.body'), '$.args'), '$.$groupByFieldName'), ''),
                                        IFNULL(JSON_EXTRACT_SCALAR(JSON_EXTRACT_SCALAR(JSON_EXTRACT_SCALAR(JSON_EXTRACT_SCALAR(request, '$.stampTask'), '$.body'), '$.args'), '$.$groupByFieldName'), '')
                                    ) as $groupByFieldName,
                                ";
                            }
                        }
                    },
                    $groupByFieldNames,
                )
            ) . "
                COUNT(*) AS value,
            FROM
                `{$tableName}`
        " . implode(
                "",
                array_map(
                    function (string $unnestFieldName) {
                        return ", UNNEST(
                            ARRAY_CONCAT(
                                IFNULL(JSON_EXTRACT_STRING_ARRAY(request, '$.$unnestFieldName'), []),
                                IFNULL(JSON_EXTRACT_STRING_ARRAY(JSON_EXTRACT_SCALAR(JSON_EXTRACT_SCALAR(JSON_EXTRACT_SCALAR(request, '$.stampSheet'), '$.body'), '$.args'), '$.$unnestFieldName'), []),
                                IFNULL(JSON_EXTRACT_STRING_ARRAY(JSON_EXTRACT_SCALAR(JSON_EXTRACT_SCALAR(JSON_EXTRACT_SCALAR(request, '$.stampTask'), '$.body'), '$.args'), '$.$unnestFieldName'), [])
                            )
                        ) as $unnestFieldName";
                    },
                    $unnestFieldNames,
                )
            ) . "
            WHERE
                {$timeRange} AND
                service = '{$service}' AND
                method IN (
        " . implode(
                ",",
                array_map(
                    function (string $methodName) {
                        return "'$methodName'";
                    },
                    $methodNames,
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
    }

    public static function buildSumQuery(
        string $service,
        string $tableName,
        string $timeRange,
        string $valueFieldName,
        array $methodNames,
        array $groupByFieldNames,
    ): string {
        return "
            SELECT
                CONCAT(FORMAT_DATETIME('%Y-%m-%d %H', timestamp), ':00:00') AS date,
        " . implode(
                "",
                array_map(
                    function (string $groupByFieldName) {
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
                IFNULL(SUM(CAST(JSON_EXTRACT_SCALAR(request, '$.{$valueFieldName}') AS INT64)), 0) +
                IFNULL(SUM(CAST(JSON_EXTRACT_SCALAR(JSON_EXTRACT_SCALAR(JSON_EXTRACT_SCALAR(JSON_EXTRACT_SCALAR(request, '$.stampSheet'), '$.body'), '$.args'), '$.{$valueFieldName}') AS INT64)), 0) +
                IFNULL(SUM(CAST(JSON_EXTRACT_SCALAR(JSON_EXTRACT_SCALAR(JSON_EXTRACT_SCALAR(JSON_EXTRACT_SCALAR(request, '$.stampTask'), '$.body'), '$.args'), '$.{$valueFieldName}') AS INT64)), 0) AS value,
            FROM
                `{$tableName}`
            WHERE
                {$timeRange} AND
                service = '{$service}' AND
                method IN (
        " . implode(
                ",",
                array_map(
                    function (string $methodName) {
                        return "'$methodName'";
                    },
                    $methodNames,
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
    }

    public static function buildValueMapQuery(
        string $service,
        string $tableName,
        string $timeRange,
        string $valueFieldName,
        array $groupByFieldNames,
        array $methodNames,
    ): string {
        return "
            SELECT
                date,
                CAST(AVG(value) AS INT64) as value,
        " . implode(
                "",
                array_map(
                    function (string $groupByFieldName) {
                        return "$groupByFieldName,";
                    },
                    $groupByFieldNames,
                )
            ) . "
                COUNT(value) as count,
            FROM
            (
                SELECT
                    CONCAT(FORMAT_DATETIME('%Y-%m-%d %H', timestamp), ':00:00') AS date,
        " . implode(
                "",
                array_map(
                    function (string $groupByFieldName) {
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
                    userId,
                    IFNULL(AVG(CAST(JSON_EXTRACT_SCALAR(result, '$.{$valueFieldName}') AS INT64)), 0) as value
                FROM
                    `{$tableName}`
                WHERE
                    {$timeRange} AND
                    service = '{$service}' AND
                    method IN (
        " . implode(
                ",",
                array_map(
                    function (string $methodName) {
                        return "'$methodName'";
                    },
                    $methodNames,
                )
            ) . ")
                GROUP BY
                    date,
                    userId,
        " . implode(
                ",",
                array_map(
                    function (string $groupByFieldName) {
                        return "$groupByFieldName";
                    },
                    $groupByFieldNames,
                )
            ) . "
            )
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
    }

    public static function loadCount(
        BigQueryClient $client,
        string $service,
        string $tableName,
        string $timeRange,
        string $method,
        array $groupByFieldNames = ['namespaceName'],
        array $unnestFieldNames = [],
        array $methods = [],
    ): LoadMetricsResult
    {
        $category = 'count';
        if (count($methods) == 0) {
            $methods = [
                "{$method}",
                "{$method}ByUserId",
                "{$method}ByStampSheet",
                "{$method}ByStampTask",
            ];
        }

        $sql = MetricsLoader::buildCountQuery(
            $service,
            $tableName,
            $timeRange,
            $methods,
            $groupByFieldNames,
            $unnestFieldNames,
        );
        $query = $client->query($sql);
        $query->allowLargeResults(true);
        $items = $client->runQuery($query);

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
                ["metricsId" => "$service:$method:$category:$keys:{$item["date"]}"],
                [
                    "key" => "$service:$method:$category:$keys",
                    "value" => $item["value"],
                    "timestamp" => DateTime::createFromFormat('Y-m-d H:i:s', $item["date"]),
                ],
            );
        }
        return new LoadMetricsResult(
            $metrics,
            $totalBytesProcessed,
        );
    }

    public static function loadSum(
        BigQueryClient $client,
        string $service,
        string $tableName,
        string $timeRange,
        string $valueFieldName,
        string $method,
        array $groupByFieldNames,
        array $methods = [],
    ): LoadMetricsResult
    {
        $category = 'sum';
        if (count($methods) == 0) {
            $methods = [
                "{$method}",
                "{$method}ByUserId",
                "{$method}ByStampSheet",
                "{$method}ByStampTask",
            ];
        }

        $sql = MetricsLoader::buildSumQuery(
            $service,
            $tableName,
            $timeRange,
            $valueFieldName,
            $methods,
            $groupByFieldNames,
        );
        $query = $client->query($sql);
        $query->allowLargeResults(true);
        $items = $client->runQuery($query);

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
                ["metricsId" => "$service:$method:$category:$valueFieldName:$keys:{$item["date"]}"],
                [
                    "key" => "$service:$method:$category:$valueFieldName:$keys",
                    "value" => $item["value"],
                    "timestamp" => DateTime::createFromFormat('Y-m-d H:i:s', $item["date"]),
                ],
            );
        }
        return new LoadMetricsResult(
            $metrics,
            $totalBytesProcessed,
        );
    }

    public static function loadBalance(
        BigQueryClient $client,
        string $service,
        string $tableName,
        string $timeRange,
        string $method,
        array $groupByFieldNames,
        string $acquireValueFieldName,
        array $acquireMethods,
        string $consumeValueFieldName,
        array $consumeMethods,
    ): LoadMetricsResult
    {
        $category = 'balance';

        $acquireSql = MetricsLoader::buildSumQuery(
            $service,
            $tableName,
            $timeRange,
            $acquireValueFieldName,
            $acquireMethods,
            $groupByFieldNames,
        );
        $consumeSql = MetricsLoader::buildSumQuery(
            $service,
            $tableName,
            $timeRange,
            $consumeValueFieldName,
            $consumeMethods,
            $groupByFieldNames,
        );
        $query = $client->query("
            SELECT
                IFNULL(a.date, b.date) as date,
        " . implode(
                "",
                array_map(
                    function (string $groupByFieldName) {
                        return "
                        IFNULL(a.$groupByFieldName, b.$groupByFieldName) as $groupByFieldName,
                        ";
                    },
                    $groupByFieldNames,
                )
            ) . "
                IFNULL(a.value, 0) - IFNULL(b.value, 0) as value
            FROM
            (
                $acquireSql
            ) as a
            FULL OUTER JOIN
            (
                $consumeSql
            ) as b
            ON
            a.date = b.date
        " . implode(
                "",
                array_map(
                    function (string $groupByFieldName) {
                        return "
                        AND a.$groupByFieldName = b.$groupByFieldName
                        ";
                    },
                    $groupByFieldNames,
                )
            ) . "
        ");
        $query->allowLargeResults(true);
        $items = $client->runQuery($query);

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
                ["metricsId" => "$service:$method:$category:$keys:{$item["date"]}"],
                [
                    "key" => "$service:$method:$category:$keys",
                    "value" => $item["value"],
                    "timestamp" => DateTime::createFromFormat('Y-m-d H:i:s', $item["date"]),
                ],
            );
        }
        return new LoadMetricsResult(
            $metrics,
            $totalBytesProcessed,
        );
    }

    public static function loadValueMap(
        BigQueryClient $client,
        string $service,
        string $tableName,
        string $timeRange,
        string $valueFieldName,
        string $method,
        array $groupByFieldNames = ['namespaceName'],
        array $methods = [],
    ): LoadMetricsResult
    {
        $category = 'map';
        if (count($methods) == 0) {
            $methods = [
                "{$method}",
                "{$method}ByUserId",
                "{$method}ByStampSheet",
                "{$method}ByStampTask",
            ];
        }

        $sql = MetricsLoader::buildValueMapQuery(
            $service,
            $tableName,
            $timeRange,
            $valueFieldName,
            $groupByFieldNames,
            $methods,
        );
        $query = $client->query($sql);
        $query->allowLargeResults(true);
        $items = $client->runQuery($query);

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
                ["metricsId" => "$service:$method:$category:$valueFieldName:$keys:{$item["value"]}:{$item["date"]}"],
                [
                    "key" => "$service:$method:$category:$valueFieldName:$keys:{$item["value"]}",
                    "value" => $item["count"],
                    "timestamp" => DateTime::createFromFormat('Y-m-d H:i:s', $item["date"]),
                ],
            );
        }
        return new LoadMetricsResult(
            $metrics,
            $totalBytesProcessed,
        );
    }
}
