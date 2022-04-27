<?php

namespace App\Domain\Log\BigQuery;

use App\Http\Controllers\Metrics\Common\AbstractMetricsController;
use App\Models\AccessLog;
use DatePeriod;
use Google\Cloud\BigQuery\BigQueryClient;

class AccessLogs extends AbstractMetricsController
{
    private DatePeriod $period;
    private string $datasetName;
    private string $credentials;

    public function __construct(
        DatePeriod $period,
        string $datasetName,
        string $credentials,
    )
    {
        $this->period = $period;
        $this->datasetName = $datasetName;
        $this->credentials = $credentials;
    }

    public function load() {
        $startYmd = $this->period->getStartDate()->format('Y-m-d');
        $endYmd = $this->period->getEndDate()->format('Y-m-d');

        $keyFile = json_decode($this->credentials, true);
        $projectId = $keyFile["project_id"];

        $bigQuery = new BigQueryClient([
            'keyFile' => $keyFile,
        ]);
        $query = $bigQuery->query("
        SELECT
            *
        FROM
            `{$this->table('Invoke')}`
        WHERE
            {$this->timeRange()} AND
            userId IS NOT NULL AND userId != '' AND
            method NOT IN ('get', 'describe', 'getItemSet')
        ");
        $query->allowLargeResults(true);
        $queryResults = $bigQuery->runQuery($query);

        foreach ($queryResults as $row) {
            $row['timestamp'] = $row['timestamp']->get()->format("Y-m-d H:i:s");
            AccessLog::query()->firstOrCreate(
                ["requestId" => $row["requestId"]],
                $row,
            );
        }
    }
}
