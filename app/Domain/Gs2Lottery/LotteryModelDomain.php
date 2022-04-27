<?php

namespace App\Domain\Gs2Lottery;

use App\Domain\BaseDomain;
use App\Models\Grn;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use JetBrains\PhpStorm\Pure;

class LotteryModelDomain extends BaseDomain {

    public NamespaceDomain $namespace;
    public string $lotteryModelName;

    public function __construct(
        NamespaceDomain $namespace,
        string $lotteryModelName,
    ) {
        $this->namespace = $namespace;
        $this->lotteryModelName = $lotteryModelName;
    }

    public function infoView(
        string $view,
    ): View
    {
        return view($view)
            ->with("lotteryModel", $this);
    }

    public function drawMetricsView(
        string $timeSpan,
    ): View
    {
        $service = 'lottery';
        $method = 'draw';
        $category = 'count';

        return $this->metrics(
            $service,
            $method,
            $category,
            $timeSpan,
            [
                'namespaceName' => $this->namespace->namespaceName,
                'lotteryName' => $this->lotteryModelName,
            ]
        );
    }

    public function drawSumMetricsView(
        string $timeSpan,
    ): View
    {
        $service = 'lottery';
        $method = 'draw';
        $category = 'sum:count';

        return $this->metrics(
            $service,
            $method,
            $category,
            $timeSpan,
            [
                'namespaceName' => $this->namespace->namespaceName,
                'lotteryName' => $this->lotteryModelName,
            ]
        );
    }

    public function metricsView(
        string $view,
    ): View
    {
        return view($view)
            ->with("lotteryModel", $this)
            ->with('metrics', [
                "hourly" => [
                    $this->drawMetricsView(
                        "hourly",
                    ),
                    $this->drawSumMetricsView(
                        "hourly",
                    ),
                ],
                "daily" => [
                    $this->drawMetricsView(
                        "daily",
                    ),
                    $this->drawSumMetricsView(
                        "daily",
                    ),
                ],
                "weekly" => [
                    $this->drawMetricsView(
                        "weekly",
                    ),
                    $this->drawSumMetricsView(
                        "weekly",
                    ),
                ],
                "monthly" => [
                    $this->drawMetricsView(
                        "monthly",
                    ),
                    $this->drawSumMetricsView(
                        "weekly",
                    ),
                ],
            ]);
    }
}
