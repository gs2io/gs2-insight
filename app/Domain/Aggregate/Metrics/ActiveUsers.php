<?php

namespace App\Domain\Aggregate\Metrics;

use App\Domain\Aggregate\Metrics\Common\MetricsLoader;
use App\Domain\Aggregate\Metrics\Common\Result\LoadMetricsResult;
use App\Domain\Aggregate\Metrics\Common\Result\LoadResult;
use App\Models\AccessLog;
use App\Models\IssueStampSheetLog;
use App\Models\Metrics;
use App\Models\Timeline;
use DatePeriod;
use DateTime;
use Google\Cloud\BigQuery\BigQueryClient;
use Illuminate\Support\Carbon;
use JetBrains\PhpStorm\Pure;

class ActiveUsers extends AbstractMetrics
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

    public function hourly(): LoadResult {

        $client = $this->createClient();
        $sql = "
            SELECT
                CONCAT(FORMAT_DATETIME('%Y-%m-%d %H', timestamp), ':00:00') AS date,
                COUNT(DISTINCT(userId)) AS value,
            FROM
                `{$this->table()}`
            WHERE
                {$this->timeRange()}
            GROUP BY
                date
        ";
        $query = $client->query($sql);
        $query->allowLargeResults(true);
        $items = $client->runQuery($query);

        $totalBytesProcessed = $items->info()['totalBytesProcessed'];

        $metrics = [];
        foreach ($items as $item) {
            $metrics[] = Metrics::query()->updateOrCreate(
                ["metricsId" => "general:uniquePlayer:count:hourly:{$item["date"]}"],
                [
                    "key" => "general:uniquePlayer:count:hourly",
                    "value" => $item["value"],
                    "timestamp" => DateTime::createFromFormat('Y-m-d H:i:s', $item["date"]),
                ],
            );
        }
        return new LoadResult(
            $totalBytesProcessed,
        );
    }

    public function daily(): LoadResult {

        $client = $this->createClient();
        $sql = "
            SELECT
                CONCAT(FORMAT_DATETIME('%Y-%m-%d', timestamp), '00:00:00') AS date,
                COUNT(DISTINCT(userId)) AS value,
            FROM
                `{$this->table()}`
            WHERE
                {$this->timeRange()}
            GROUP BY
                date
        ";
        $query = $client->query($sql);
        $query->allowLargeResults(true);
        $items = $client->runQuery($query);

        $totalBytesProcessed = $items->info()['totalBytesProcessed'];

        $metrics = [];
        foreach ($items as $item) {
            $metrics[] = Metrics::query()->updateOrCreate(
                ["metricsId" => "general:uniquePlayer:count:daily:{$item["date"]}"],
                [
                    "key" => "general:uniquePlayer:count:daily",
                    "value" => $item["value"],
                    "timestamp" => DateTime::createFromFormat('Y-m-d H:i:s', $item["date"]),
                ],
            );
        }
        return new LoadResult(
            $totalBytesProcessed,
        );
    }

    public function monthly(): LoadResult {

        $client = $this->createClient();
        $sql = "
            SELECT
                CONCAT(FORMAT_DATETIME('%Y-%m', timestamp), '-01 00:00:00') AS date,
                COUNT(DISTINCT(userId)) AS value,
            FROM
                `{$this->table()}`
            WHERE
                {$this->timeRange()}
            GROUP BY
                date
        ";
        $query = $client->query($sql);
        $query->allowLargeResults(true);
        $items = $client->runQuery($query);

        $totalBytesProcessed = $items->info()['totalBytesProcessed'];

        $metrics = [];
        foreach ($items as $item) {
            $metrics[] = Metrics::query()->updateOrCreate(
                ["metricsId" => "general:uniquePlayer:count:monthly:{$item["date"]}"],
                [
                    "key" => "general:uniquePlayer:count:monthly",
                    "value" => $item["value"],
                    "timestamp" => DateTime::createFromFormat('Y-m-d H:i:s', $item["date"]),
                ],
            );
        }
        return new LoadResult(
            $totalBytesProcessed,
        );
    }

    public function load(): LoadResult {
        $totalBytesProcessed = 0;

        $result = $this->hourly();
        $totalBytesProcessed += $result->totalBytesProcessed;

        $result = $this->daily();
        $totalBytesProcessed += $result->totalBytesProcessed;

        $result = $this->monthly();
        $totalBytesProcessed += $result->totalBytesProcessed;

        return new LoadResult(
            $totalBytesProcessed,
        );
    }
}
