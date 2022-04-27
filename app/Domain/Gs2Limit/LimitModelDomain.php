<?php

namespace App\Domain\Gs2Limit;

use App\Domain\BaseDomain;
use App\Models\Grn;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use JetBrains\PhpStorm\Pure;

class LimitModelDomain extends BaseDomain {

    public NamespaceDomain $namespace;
    public string $limitModelName;

    public function __construct(
        NamespaceDomain $namespace,
        string $limitModelName,
    ) {
        $this->namespace = $namespace;
        $this->limitModelName = $limitModelName;
    }

    public function infoView(
        string $view,
    ): View
    {
        return view($view)
            ->with("limitModel", $this);
    }

    public function countUpMetricsView(
        string $timeSpan,
    ): View
    {
        $service = 'limit';
        $method = 'countUp';
        $category = 'count';

        return $this->metrics(
            $service,
            $method,
            $category,
            $timeSpan,
            [
                'namespaceName' => $this->namespace->namespaceName,
                'limitName' => $this->limitModelName,
            ]
        );
    }

    public function countUpSumMetricsView(
        string $timeSpan,
    ): View
    {
        $service = 'limit';
        $method = 'countUp';
        $category = 'sum:countUpValue';

        return $this->metrics(
            $service,
            $method,
            $category,
            $timeSpan,
            [
                'namespaceName' => $this->namespace->namespaceName,
                'limitName' => $this->limitModelName,
            ]
        );
    }

    public function metricsView(
        string $view,
    ): View
    {
        return view($view)
            ->with("limitModel", $this)
            ->with('metrics', [
                "hourly" => [
                    $this->countUpMetricsView(
                        "hourly",
                    ),
                    $this->countUpSumMetricsView(
                        "hourly",
                    ),
                ],
                "daily" => [
                    $this->countUpMetricsView(
                        "daily",
                    ),
                    $this->countUpSumMetricsView(
                        "daily",
                    ),
                ],
                "weekly" => [
                    $this->countUpMetricsView(
                        "weekly",
                    ),
                    $this->countUpSumMetricsView(
                        "weekly",
                    ),
                ],
                "monthly" => [
                    $this->countUpMetricsView(
                        "monthly",
                    ),
                    $this->countUpSumMetricsView(
                        "weekly",
                    ),
                ],
            ]);
    }
}
