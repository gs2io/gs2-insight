<?php

namespace App\Domain\Gs2Matchmaking;

use App\Domain\BaseDomain;
use Illuminate\Contracts\View\View;

class NamespaceDomain extends BaseDomain {

    public string $namespaceName;

    public function __construct(
        string $namespaceName,
    ) {
        $this->namespaceName = $namespaceName;
    }

    public function infoView(
        string $view,
    ): View
    {
        return view($view)
            ->with("namespace", $this);
    }

    public function createGatheringMetricsView(
        string $timeSpan,
    ): View
    {
        $service = 'matchmaking';
        $method = 'createGathering';
        $category = 'count';

        return $this->metrics(
            $service,
            $method,
            $category,
            $timeSpan,
            [
                'namespaceName' => $this->namespaceName,
            ]
        );
    }

    public function doMatchmakingMetricsView(
        string $timeSpan,
    ): View
    {
        $service = 'matchmaking';
        $method = 'doMatchmaking';
        $category = 'count';

        return $this->metrics(
            $service,
            $method,
            $category,
            $timeSpan,
            [
                'namespaceName' => $this->namespaceName,
            ]
        );
    }

    public function cancelMatchmakingMetricsView(
        string $timeSpan,
    ): View
    {
        $service = 'matchmaking';
        $method = 'cancelMatchmaking';
        $category = 'count';

        return $this->metrics(
            $service,
            $method,
            $category,
            $timeSpan,
            [
                'namespaceName' => $this->namespaceName,
            ]
        );
    }

    public function metricsView(
        string $view,
    ): View
    {
        return view($view)
            ->with("namespace", $this)
            ->with('metrics', [
                "hourly" => [
                    $this->createGatheringMetricsView(
                        "hourly",
                    ),
                    $this->doMatchmakingMetricsView(
                        "hourly",
                    ),
                    $this->cancelMatchmakingMetricsView(
                        "hourly",
                    ),
                ],
                "daily" => [
                    $this->createGatheringMetricsView(
                        "daily",
                    ),
                    $this->doMatchmakingMetricsView(
                        "daily",
                    ),
                    $this->cancelMatchmakingMetricsView(
                        "daily",
                    ),
                ],
                "weekly" => [
                    $this->createGatheringMetricsView(
                        "weekly",
                    ),
                    $this->doMatchmakingMetricsView(
                        "weekly",
                    ),
                    $this->cancelMatchmakingMetricsView(
                        "weekly",
                    ),
                ],
                "monthly" => [
                    $this->createGatheringMetricsView(
                        "monthly",
                    ),
                    $this->doMatchmakingMetricsView(
                        "monthly",
                    ),
                    $this->cancelMatchmakingMetricsView(
                        "monthly",
                    ),
                ],
            ]);
    }
}
