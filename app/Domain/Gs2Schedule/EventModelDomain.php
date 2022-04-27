<?php

namespace App\Domain\Gs2Schedule;

use App\Domain\BaseDomain;
use App\Models\Grn;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use JetBrains\PhpStorm\Pure;

class EventModelDomain extends BaseDomain {

    public NamespaceDomain $namespace;
    public string $eventModelName;

    public function __construct(
        NamespaceDomain $namespace,
        string $eventModelName,
    ) {
        $this->namespace = $namespace;
        $this->eventModelName = $eventModelName;
    }

    public function infoView(
        string $view,
    ): View
    {
        return view($view)
            ->with("eventModel", $this);
    }


    public function triggerMetricsView(
        string $timeSpan,
    ): View
    {
        $service = 'schedule';
        $method = 'trigger';
        $event = 'count';

        return $this->metrics(
            $service,
            $method,
            $event,
            $timeSpan,
            [
                'namespaceName' => $this->namespace->namespaceName,
                'eventName' => $this->eventModelName,
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
