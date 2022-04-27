<?php

namespace App\Domain\Gs2Mission;

use App\Domain\BaseDomain;
use App\Models\Grn;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use JetBrains\PhpStorm\Pure;

class CounterModelDomain extends BaseDomain {

    public NamespaceDomain $namespace;
    public string $counterModelName;

    public function __construct(
        NamespaceDomain $namespace,
        string $counterModelName,
    ) {
        $this->namespace = $namespace;
        $this->counterModelName = $counterModelName;
    }

    public function infoView(
        string $view,
    ): View
    {
        return view($view)
            ->with("counterModel", $this);
    }

    public function increaseCounterMetricsView(
        string $timeSpan,
    ): View
    {
        $service = 'mission';
        $method = 'increaseCounter';
        $category = 'count';

        return $this->metrics(
            $service,
            $method,
            $category,
            $timeSpan,
            [
                'namespaceName' => $this->namespace->namespaceName,
                'counterName' => $this->counterModelName,
            ]
        );
    }

    public function increaseCounterSumMetricsView(
        string $timeSpan,
    ): View
    {
        $service = 'mission';
        $method = 'increaseCounter';
        $category = 'sum:value';

        return $this->metrics(
            $service,
            $method,
            $category,
            $timeSpan,
            [
                'namespaceName' => $this->namespace->namespaceName,
                'counterName' => $this->counterModelName,
            ]
        );
    }

    public function metricsView(
        string $view,
    ): View
    {
        return view($view)
            ->with("counterModel", $this)
            ->with('metrics', [
                "hourly" => [
                    $this->increaseCounterMetricsView(
                        "hourly",
                    ),
                    $this->increaseCounterSumMetricsView(
                        "hourly",
                    ),
                ],
                "daily" => [
                    $this->increaseCounterMetricsView(
                        "daily",
                    ),
                    $this->increaseCounterSumMetricsView(
                        "daily",
                    ),
                ],
                "weekly" => [
                    $this->increaseCounterMetricsView(
                        "weekly",
                    ),
                    $this->increaseCounterSumMetricsView(
                        "weekly",
                    ),
                ],
                "monthly" => [
                    $this->increaseCounterMetricsView(
                        "monthly",
                    ),
                    $this->increaseCounterSumMetricsView(
                        "monthly",
                    ),
                ],
            ]);
    }
}
