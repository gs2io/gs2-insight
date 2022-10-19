<?php

namespace App\Domain\Aggregate\Metrics\Common;

use App\Domain\Aggregate\Metrics\Common\Result\LoadGrnResult;
use App\Models\Grn;
use Google\Cloud\BigQuery\BigQueryClient;

class GrnLoader {

    public static function buildQuery(
        string $service,
        string $keyName,
        string $tableName,
        string $timeRange,
        array $filter,
        array $methods = [],
        string $userId = null,
    ): string {
        return "
            SELECT
                key
            FROM
            (
                SELECT
                    ". ((function() use ($service, $keyName) {
                        if ($service == 'script' && $keyName == 'scriptName') {
                            return "SPLIT(JSON_EXTRACT_SCALAR(request, '$.scriptId'), ':')[offset(7)] as key";
                        } else if ($service == 'jobQueue' && $keyName == 'jobName') {
                            return "JSON_EXTRACT_SCALAR(result, '$.item.name') as key";
                        } else {
                            return "JSON_EXTRACT_SCALAR(request, '$.{$keyName}') as key";
                        }
                    })()). "
                FROM
                    `{$tableName}`
                WHERE
                    {$timeRange} AND
                    method not like 'get%' AND method not like 'describe%' AND
        " . implode(
                ",",
                array_map(
                    function (string $method) use ($methods) {
                        if (count($methods) == 1) {
                            return "method IN ('$method') AND ";
                        } else if (array_search($method, $methods) == 0) {
                            return "method IN ('$method'";
                        } else if (array_search($method, $methods) == count($methods)-1) {
                            return "'$method') AND ";
                        }
                        return "'$method'";
                    },
                    $methods,
                )
            ).
            ((function() use ($userId) {
                if (is_null($userId)) {
                    return "";
                } else {
                    return "userId = '$userId' AND ";
                }
            })())
            ."
                    service = '{$service}'
        " . implode(
                "",
                array_map(
                    function (string $filterName, string $filterValue) use ($keyName, $service) {
                        if ($service == 'script' && $keyName == 'scriptName') {
                            return "
                                AND SPLIT(JSON_EXTRACT_SCALAR(request, '$.scriptId'), ':')[offset(5)] = '{$filterValue}'
                            ";
                        } else {
                            return "
                                AND JSON_EXTRACT_SCALAR(request, '$.{$filterName}') = '{$filterValue}'
                            ";
                        }
                    },
                    array_keys($filter), array_values($filter),
                )
            ) . "
                GROUP BY
                    key
                UNION DISTINCT
                SELECT
                    JSON_EXTRACT_SCALAR(JSON_EXTRACT_SCALAR(JSON_EXTRACT_SCALAR(JSON_EXTRACT_SCALAR(request, '$.stampSheet'), '$.body'), '$.args'), '$.{$keyName}') as key
                FROM
                    `{$tableName}`
                WHERE
                    {$timeRange} AND
                    method not like 'get%' AND method not like 'describe%' AND
        " . implode(
                ",",
                array_map(
                    function (string $method) use ($methods) {
                        if (count($methods) == 1) {
                            return "method IN ('$method') AND ";
                        } else if (array_search($method, $methods) == 0) {
                            return "method IN ('$method'";
                        } else if (array_search($method, $methods) == count($methods)-1) {
                            return "'$method') AND ";
                        }
                        return "'$method'";
                    },
                    $methods,
                )
            ) . "
                    service = '{$service}'
        " . implode(
                "",
                array_map(
                    function (string $filterName, string $filterValue) {
                        return "
                            AND JSON_EXTRACT_SCALAR(JSON_EXTRACT_SCALAR(JSON_EXTRACT_SCALAR(JSON_EXTRACT_SCALAR(request, '$.stampSheet'), '$.body'), '$.args'), '$.{$filterName}') = '{$filterValue}'
                        ";
                    },
                    array_keys($filter), array_values($filter),
                )
            ) . "
                GROUP BY
                    key
                UNION DISTINCT
                SELECT
                    JSON_EXTRACT_SCALAR(JSON_EXTRACT_SCALAR(JSON_EXTRACT_SCALAR(JSON_EXTRACT_SCALAR(request, '$.stampTask'), '$.body'), '$.args'), '$.{$keyName}') as key
                FROM
                    `{$tableName}`
                WHERE
                    {$timeRange} AND
                    method not like 'get%' AND method not like 'describe%' AND
        " . implode(
                ",",
                array_map(
                    function (string $method) use ($methods) {
                        if (count($methods) == 1) {
                            return "method IN ('$method') AND ";
                        } else if (array_search($method, $methods) == 0) {
                            return "method IN ('$method'";
                        } else if (array_search($method, $methods) == count($methods)-1) {
                            return "'$method') AND ";
                        }
                        return "'$method'";
                    },
                    $methods,
                )
            ) . "
                    service = '{$service}'
        " . implode(
                "",
                array_map(
                    function (string $filterName, string $filterValue) {
                        return "
                            AND JSON_EXTRACT_SCALAR(JSON_EXTRACT_SCALAR(JSON_EXTRACT_SCALAR(JSON_EXTRACT_SCALAR(request, '$.stampTask'), '$.body'), '$.args'), '$.{$filterName}') = '{$filterValue}'
                        ";
                    },
                    array_keys($filter), array_values($filter),
                )
            ) . "
                GROUP BY
                    key
            )
            WHERE
                key IS NOT NULL AND
                key <> 'null'
        ";
    }

    public static function buildArrayQuery(
        string $service,
        string $keyName,
        string $tableName,
        string $timeRange,
        array $filter,
        array $methods = [],
    ): string {
        return "
            SELECT
                key
            FROM
            (
                SELECT
                    JSON_EXTRACT_STRING_ARRAY(request, '$.{$keyName}') as key
                FROM
                    `{$tableName}`
                WHERE
                    {$timeRange} AND
                    service = '{$service}'
        " . implode(
                "",
                array_map(
                    function (string $filterName, string $filterValue) {
                        return "
                            AND JSON_EXTRACT_SCALAR(request, '$.{$filterName}') = '{$filterValue}'
                        ";
                    },
                    array_keys($filter), array_values($filter),
                )
            ) . "
            ) as a,
            UNNEST(key) AS key
            GROUP BY
                key
            UNION DISTINCT
            SELECT
                key
            FROM
            (
                SELECT
                    JSON_EXTRACT_STRING_ARRAY(JSON_EXTRACT_SCALAR(JSON_EXTRACT_SCALAR(JSON_EXTRACT_SCALAR(request, '$.stampSheet'), '$.body'), '$.args'), '$.{$keyName}') as key
                FROM
                    `{$tableName}`
                WHERE
                    {$timeRange} AND
                    method not like 'get%' AND method not like 'describe%' AND
        " . implode(
                ",",
                array_map(
                    function (string $method) use ($methods) {
                        if (count($methods) == 1) {
                            return "method IN ('$method') AND ";
                        } else if (array_search($method, $methods) == 0) {
                            return "method IN ('$method'";
                        } else if (array_search($method, $methods) == count($methods)-1) {
                            return "'$method') AND ";
                        }
                        return "'$method'";
                    },
                    $methods,
                )
            ) . "
                    service = '{$service}'
        " . implode(
                "",
                array_map(
                    function (string $filterName, string $filterValue) {
                        return "
                            AND JSON_EXTRACT_SCALAR(JSON_EXTRACT_SCALAR(JSON_EXTRACT_SCALAR(JSON_EXTRACT_SCALAR(request, '$.stampSheet'), '$.body'), '$.args'), '$.{$filterName}') = '{$filterValue}'
                        ";
                    },
                    array_keys($filter), array_values($filter),
                )
            ) . "
            ) as a,
            UNNEST(key) AS key
            GROUP BY
                key
            UNION DISTINCT
            SELECT
                key
            FROM
            (
                SELECT
                    JSON_EXTRACT_STRING_ARRAY(JSON_EXTRACT_SCALAR(JSON_EXTRACT_SCALAR(JSON_EXTRACT_SCALAR(request, '$.stampTask'), '$.body'), '$.args'), '$.{$keyName}') as key
                FROM
                    `{$tableName}`
                WHERE
                    {$timeRange} AND
                    service = '{$service}'
        " . implode(
                "",
                array_map(
                    function (string $filterName, string $filterValue) {
                        return "
                            AND JSON_EXTRACT_SCALAR(JSON_EXTRACT_SCALAR(JSON_EXTRACT_SCALAR(JSON_EXTRACT_SCALAR(request, '$.stampTask'), '$.body'), '$.args'), '$.{$filterName}') = '{$filterValue}'
                        ";
                    },
                    array_keys($filter), array_values($filter),
                )
            ) . "
            ) as a,
            UNNEST(key) AS key
            WHERE
                key IS NOT NULL AND
                key <> 'null'
            GROUP BY
                key
        ";
    }

    public static function rootGrn(
        string $service,
    ): Grn {
        return new Grn([
            "grn" => "grn:{$service}",
        ]);
    }

    public static function load(
        BigQueryClient $client,
        Grn $parent,
        string $sql,
        string $modelName,
    ): LoadGrnResult
    {
        $query = $client->query($sql);
        $query->allowLargeResults(true);
            $items = $client->runQuery($query, [
                'maxResults' => 1000,
            ]);

        $totalBytesProcessed = $items->info()['totalBytesProcessed'];

        $grns = [];
        foreach ($items as $item) {
            if (isset($item["key"])) {
                \Amp\call(function () use ($modelName, $parent, $item, &$grns): void {
                    $grns[] = Grn::query()->updateOrCreate(
                        ["grn" => "{$parent->grn}:{$modelName}:{$item["key"]}"],
                        [
                            "parent" => $parent->grn,
                            "category" => $modelName,
                            "key" => $item["key"],
                        ],
                    );
                });
            }
        }
        return new LoadGrnResult(
            $grns,
            $totalBytesProcessed,
        );
    }
}
