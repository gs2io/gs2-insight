<?php

namespace App\Domain\Aggregate\Metrics\Common;

use App\Domain\Aggregate\Metrics\Common\Result\LoadGrnKeyResult;
use App\Models\Grn;
use App\Models\GrnKey;
use Google\Cloud\BigQuery\BigQueryClient;

class GrnKeyLoader {

    public static function buildQuery(
        string $service,
        array $keyNames,
        string $tableName,
        string $userId,
        string $timeRange,
        array $filter,
        array $methods = [],
    ): string {
        $field = 'result';
        if (count($keyNames) > 2) {
            $field = $keyNames[2];
        }
        return "
            SELECT
                key,
                requestId,
            FROM
            (
                SELECT
                    CONCAT('$keyNames[0]:', JSON_EXTRACT_SCALAR({$field}, '$.{$keyNames[1]}')) as key,
                    requestId,
                FROM
                    `{$tableName}`
                WHERE
                    {$timeRange} AND
                    service = '{$service}' AND
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
                    userId = '{$userId}'
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
                UNION DISTINCT
                SELECT
                    CONCAT('$keyNames[0]:', JSON_EXTRACT_SCALAR(result, '$.{$keyNames[1]}')) as key,
                    requestId,
                FROM
                    `{$tableName}`
                WHERE
                    {$timeRange} AND
                    service = '{$service}' AND
                    method not like 'get%' AND method not like 'describe%' AND
                    JSON_EXTRACT_SCALAR(JSON_EXTRACT_SCALAR(JSON_EXTRACT_SCALAR(JSON_EXTRACT_SCALAR(request, '$.stampSheet'), '$.body'), '$.args'), '$.userId') = '{$userId}'
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
                UNION DISTINCT
                SELECT
                    CONCAT('$keyNames[0]:', JSON_EXTRACT_SCALAR(result, '$.{$keyNames[1]}')) as key,
                    requestId,
                FROM
                    `{$tableName}`
                WHERE
                    {$timeRange} AND
                    service = '{$service}' AND
                    method not like 'get%' AND method not like 'describe%' AND
                    JSON_EXTRACT_SCALAR(JSON_EXTRACT_SCALAR(JSON_EXTRACT_SCALAR(JSON_EXTRACT_SCALAR(request, '$.stampTask'), '$.body'), '$.args'), '$.userId') = '{$userId}'
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
            )
            WHERE
                key IS NOT NULL AND
                key <> 'null'
        ";
    }

    public static function buildArrayQuery(
        string $service,
        array $keyNames,
        string $tableName,
        string $userId,
        string $timeRange,
        array $filter,
    ): string {
        return "
            SELECT".
                (function() use ($service, $keyNames) {
                    if ($service == "jobQueue" && $keyNames[1] == "items.name") {
                        return "
                            CONCAT('$keyNames[0]:', JSON_EXTRACT_SCALAR(key, '$.name')) as key,
                        ";
                    } else {
                        return "
                            CONCAT('$keyNames[0]:', key) as key,
                        ";
                    }
                })()
            ."
                requestId,
            FROM
            (
                SELECT ".
                    (function() use ($service, $keyNames) {
                        if ($service == "jobQueue" && $keyNames[1] == "items.name") {
                            return "
                                JSON_EXTRACT_ARRAY(result, '$.items') as key,
                            ";
                        } else {
                            return "
                                JSON_EXTRACT_STRING_ARRAY(request, '$.{$keyNames[1]}') as key,
                            ";
                        }
                    })()
                ."
                    requestId
                FROM
                    `{$tableName}`
                WHERE
                    {$timeRange} AND
                    service = '{$service}' AND
                    method not like 'get%' AND method not like 'describe%' AND
                    userId = '{$userId}'
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
            UNION DISTINCT
            SELECT
                key,
                requestId,
            FROM
            (
                SELECT
                    JSON_EXTRACT_STRING_ARRAY(JSON_EXTRACT_SCALAR(JSON_EXTRACT_SCALAR(JSON_EXTRACT_SCALAR(request, '$.stampSheet'), '$.body'), '$.args'), '$.{$keyNames[1]}') as key,
                    requestId
                FROM
                    `{$tableName}`
                WHERE
                    {$timeRange} AND
                    service = '{$service}' AND
                    method not like 'get%' AND method not like 'describe%' AND
                    JSON_EXTRACT_SCALAR(JSON_EXTRACT_SCALAR(JSON_EXTRACT_SCALAR(JSON_EXTRACT_SCALAR(request, '$.stampSheet'), '$.body'), '$.args'), '$.userId') = '{$userId}'
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
            UNION DISTINCT
            SELECT
                key,
                requestId,
            FROM
            (
                SELECT
                    JSON_EXTRACT_STRING_ARRAY(JSON_EXTRACT_SCALAR(JSON_EXTRACT_SCALAR(JSON_EXTRACT_SCALAR(request, '$.stampTask'), '$.body'), '$.args'), '$.{$keyNames[1]}') as key,
                    requestId
                FROM
                    `{$tableName}`
                WHERE
                    {$timeRange} AND
                    service = '{$service}' AND
                    method not like 'get%' AND method not like 'describe%' AND
                    JSON_EXTRACT_SCALAR(JSON_EXTRACT_SCALAR(JSON_EXTRACT_SCALAR(JSON_EXTRACT_SCALAR(request, '$.stampTask'), '$.body'), '$.args'), '$.userId') = '{$userId}'
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
        ";
    }

    public static function buildUserQuery(
        string $service,
        string $tableName,
        string $userId,
        array $methods,
        string $timeRange,
        array $filter,
    ): string {
        return "
            SELECT
                CONCAT('user', ':', '$userId') as key,
                requestId,
            FROM
            (
                SELECT
                    requestId,
                FROM
                    `{$tableName}`
                WHERE
                    {$timeRange} AND
                    service = '{$service}' AND
                    method not like 'get%' AND method not like 'describe%' AND
                    method IN (
        " . implode(
                ",",
                array_map(
                    function (string $method) {
                        return "'$method'";
                    },
                    $methods,
                )
            ) . ") AND
                    userId = '{$userId}'
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
            )
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
    ): LoadGrnKeyResult
    {
        $query = $client->query($sql);
        $query->allowLargeResults(true);
        $items = $client->runQuery($query);

        $totalBytesProcessed = $items->info()['totalBytesProcessed'];

        $grnKeys = [];
        foreach ($items as $item) {
            if (isset($item["key"])) {
                $grnKeys[] = GrnKey::query()->firstOrCreate([
                    'keyId' => "{$parent->grn}:{$item["key"]}:{$modelName}:requestId:{$item['requestId']}",
                    'grn' => "{$parent->grn}:{$item["key"]}",
                    'category' => "{$modelName}",
                    'requestId' => $item['requestId'],
                ]);
            }
        }
        return new LoadGrnKeyResult(
            $grnKeys,
            $totalBytesProcessed,
        );
    }
}
