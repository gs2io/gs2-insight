<?php

namespace App\Domain\Aggregate\Metrics;

use App\Http\Controllers\Metrics\Common\AbstractMetricsController;
use App\Models\AccessLog;
use App\Models\IssueStampSheetLog;
use App\Models\Metrics;
use App\Models\Timeline;
use DateInterval;
use DatePeriod;
use DateTime;
use Google\Cloud\BigQuery\BigQueryClient;
use Illuminate\Support\Carbon;

class RetentionUsers extends AbstractMetricsController
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

    public function daily(
        DateTime $start,
        DateInterval $interval,
        DateTime $end,
    ) {
        $targetStartYmd = $start->format('Y-m-d');
        $targetEndYmd = $start->add($interval)->format('Y-m-d');
        $endDateYmd = $end->format('Y-m-d');

        $keyFile = json_decode($this->credentials, true);
        $projectId = $keyFile["project_id"];

        $bigQuery = new BigQueryClient([
            'keyFile' => $keyFile,
        ]);
        $query = $bigQuery->query("
        SELECT
            COUNT(*) as value
        FROM
        (
            SELECT
                userId as userId,
                COUNT(userId) as loginDays
            FROM
            (
              SELECT
                  userId,
                  FORMAT_DATETIME('%Y-%m-%d', timestamp) as key
              FROM
                    `{$this->table('Invoke')}`
              WHERE
                  DATE(timestamp) BETWEEN '$targetStartYmd' AND '$targetEndYmd'
              GROUP BY
                  userId, key
            )
            GROUP BY
                userId
            HAVING
                loginDays = DATE_DIFF('$targetEndYmd', '$targetStartYmd', day) + 1
        ) as a,
        (
            SELECT
                DISTINCT userId
            FROM
                `{$this->table()}`
            WHERE
                DATE(timestamp) = '$endDateYmd'
        ) as b
        WHERE
            a.userId = b.userId AND
            a.userId NOT IN (
                SELECT
                    DISTINCT userId

                FROM
                    `{$this->table()}`
                WHERE
                    userId IS NOT NULL AND
                    DATE(timestamp) BETWEEN DATE_ADD(DATE '$targetEndYmd', INTERVAL 1 DAY) AND DATE_SUB(DATE '$endDateYmd', INTERVAL 1 DAY)
            )
        ");
        $query->allowLargeResults(true);
        $items = $bigQuery->runQuery($query, [
            'maxResults' => 1000,
        ]);

        foreach ($items as $item) {
            \Amp\call(function () use ($endDateYmd, $targetEndYmd, $targetStartYmd, $item): void {
                Metrics::query()->updateOrCreate(
                    ["metricsId" => 'retention_users:daily:'. $targetStartYmd. ':'. $targetEndYmd. ':'. $endDateYmd],
                    [
                        "category" => 'retention_users:daily:'. $targetStartYmd,
                        "key" => $targetEndYmd. ':'. $endDateYmd,
                        "value" => $item["value"],
                        "timestamp" => DateTime::createFromFormat('Y-m-d', $targetEndYmd),
                    ],
                );
            });
        }
    }

    public function load() {
        $this->daily(
            clone $this->baseAt,
            DateInterval::createFromDateString("0 days"),
            (clone $this->baseAt)->add(DateInterval::createFromDateString("0 days")),
        );

        for ($j=1; $j<8; $j++) {
            for ($i = 0; $i < 9-$j; $i++) {
                $this->daily(
                    clone $this->baseAt,
                    DateInterval::createFromDateString($i . " days"),
                    (clone $this->baseAt)->add(DateInterval::createFromDateString(($i + $j) . " days")),
                );
            }
        }
    }
}
