<?php

namespace App\Domain\Gs2Schedule;

use App\Domain\BaseDomain;
use App\Models\Grn;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use JetBrains\PhpStorm\Pure;

class TriggerModelDomain extends BaseDomain {

    public NamespaceDomain $namespace;
    public string $triggerModelName;

    public function __construct(
        NamespaceDomain $namespace,
        string $triggerModelName,
    ) {
        $this->namespace = $namespace;
        $this->triggerModelName = $triggerModelName;
    }

    public function infoView(
        string $view,
    ): View
    {
        return view($view)
            ->with("triggerModel", $this);
    }


    public function triggerMetricsView(
        string $timeSpan,
    ): View
    {
        $service = 'schedule';
        $method = 'trigger';
        $trigger = 'count';

        return $this->metrics(
            $service,
            $method,
            $trigger,
            $timeSpan,
            [
                'namespaceName' => $this->namespace->namespaceName,
                'triggerName' => $this->triggerModelName,
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
                    $this->triggerMetricsView(
                        "hourly",
                    ),
                ],
                "daily" => [
                    $this->triggerMetricsView(
                        "daily",
                    ),
                ],
                "weekly" => [
                    $this->triggerMetricsView(
                        "weekly",
                    ),
                ],
                "monthly" => [
                    $this->triggerMetricsView(
                        "monthly",
                    ),
                ],
            ]);
    }
}
