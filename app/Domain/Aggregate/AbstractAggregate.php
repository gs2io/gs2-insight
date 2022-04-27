<?php

namespace App\Domain\Aggregate;

use DatePeriod;
use Google\Cloud\BigQuery\BigQueryClient;

abstract class AbstractAggregate
{
    protected DatePeriod $period;
    protected string $datasetName;
    protected string $credentials;

    protected function __construct(
        DatePeriod $period,
        string $datasetName,
        string $credentials,
    )
    {
        $this->period = $period;
        $this->datasetName = $datasetName;
        $this->credentials = $credentials;
    }

    protected function table(string $tableName): string
    {
        $keyFile = json_decode($this->credentials, true);
        $projectId = $keyFile["project_id"];
        return "$projectId.$this->datasetName.$tableName";
    }

    protected function createClient(): BigQueryClient
    {
        $keyFile = json_decode($this->credentials, true);
        return new BigQueryClient([
            'keyFile' => $keyFile,
        ]);
    }

    protected function timeRange(): string
    {
        $startYmd = $this->period->getStartDate()->format('Y-m-d');
        $endYmd = $this->period->getEndDate()->format('Y-m-d');
        return "DATE(timestamp) BETWEEN '$startYmd' AND '$endYmd'";
    }
}
