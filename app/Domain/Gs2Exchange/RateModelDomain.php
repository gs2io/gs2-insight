<?php

namespace App\Domain\Gs2Exchange;

use App\Domain\BaseDomain;
use Illuminate\Contracts\View\View;

class RateModelDomain extends BaseDomain {

    public NamespaceDomain $namespace;
    public string $rateModelName;

    public function __construct(
        NamespaceDomain $namespace,
        string $rateModelName,
    ) {
        $this->namespace = $namespace;
        $this->rateModelName = $rateModelName;
    }

    public function infoView(
        string $view,
    ): View
    {
        return view($view)
            ->with("rateModel", $this);
    }
    public function acquireMetricsView(
        string $timeSpan,
    ): View
    {
        $service = 'exchange';
        $method = 'acquire';
        $category = 'count';

        return $this->metrics(
            $service,
            $method,
            $category,
            $timeSpan,
            [
                'namespaceName' => $this->namespace->namespaceName,
                'rateName' => $this->rateModelName,
            ]
        );
    }

    public function exchangeMetricsView(
        string $timeSpan,
    ): View
    {
        $service = 'exchange';
        $method = 'exchange';
        $category = 'count';

        return $this->metrics(
            $service,
            $method,
            $category,
            $timeSpan,
            [
                'namespaceName' => $this->namespace->namespaceName,
                'rateName' => $this->rateModelName,
            ]
        );
    }

    public function skipMetricsView(
        string $timeSpan,
    ): View
    {
        $service = 'exchange';
        $method = 'skip';
        $category = 'count';

        return $this->metrics(
            $service,
            $method,
            $category,
            $timeSpan,
            [
                'namespaceName' => $this->namespace->namespaceName,
                'rateName' => $this->rateModelName,
            ]
        );
    }

    public function metricsView(
        string $view,
    ): View
    {
        return view($view)
            ->with("rateModel", $this)
            ->with('metrics', [
                "hourly" => [
                    $this->acquireMetricsView(
                        "hourly",
                    ),
                    $this->exchangeMetricsView(
                        "hourly",
                    ),
                    $this->skipMetricsView(
                        "hourly",
                    ),
                ],
                "daily" => [
                    $this->acquireMetricsView(
                        "daily",
                    ),
                    $this->exchangeMetricsView(
                        "daily",
                    ),
                    $this->skipMetricsView(
                        "daily",
                    ),
                ],
                "weekly" => [
                    $this->acquireMetricsView(
                        "weekly",
                    ),
                    $this->exchangeMetricsView(
                        "weekly",
                    ),
                    $this->skipMetricsView(
                        "weekly",
                    ),
                ],
                "monthly" => [
                    $this->acquireMetricsView(
                        "monthly",
                    ),
                    $this->exchangeMetricsView(
                        "monthly",
                    ),
                    $this->skipMetricsView(
                        "monthly",
                    ),
                ],
            ]);
    }
}
