<?php

namespace App\Domain\Aggregate;

use App\Domain\Aggregate\Metrics\Common\Result\LoadResult;
use App\Models\LoadStatus;
use App\Models\Player;
use App\Models\Timeline;
use DatePeriod;
use Illuminate\Support\Facades\DB;
use JetBrains\PhpStorm\Pure;

class Players extends AbstractAggregate
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

    public function load(): LoadResult
    {
        $totalSteps = 1;
        $step = 0;
        $totalBytesProcessed = 0;

        $workingStatus = LoadStatus::query()->find("global:player");
        if ($workingStatus == null) {
            $workingStatus = LoadStatus::query()->create([
                "scope" => "global:player",
                "working" => "userId",
                "progress" => 0.0,
                "totalBytesProcessed" => 0,
            ]);
        }

        $items = Timeline::query()
            ->select(DB::raw('userId, MAX(timestamp) As timestamp'))
            ->whereBetween("timestamp", [$this->period->getStartDate(), $this->period->getEndDate()])
            ->groupBy("userId")
            ->orderBy("timestamp", "desc")
            ->get();
        foreach ($items as $item) {
            Player::query()->firstOrCreate(
                ["userId" => $item["userId"]],
                ["lastAccessAt" => $item["timestamp"]],
            );
        }

        $bigQuery = $this->createClient();
        $query = $bigQuery->query("
            SELECT
                userId,
                MAX(timestamp) as lastAccessAt,
            FROM
                `{$this->table('Invoke')}`
            WHERE
                {$this->timeRange()}
            GROUP BY
                userId
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

            Player::query()->firstOrCreate(
                ["userId" => $item["userId"]],
                ["lastAccessAt" => $item["lastAccessAt"]],
            );
        }

        $totalBytesProcessed += $items->info()['totalBytesProcessed'];
        $step++;
        $workingStatus->update([
            "progress" => $step / $totalSteps,
        ]);

        return new LoadResult($totalBytesProcessed);
    }
}
