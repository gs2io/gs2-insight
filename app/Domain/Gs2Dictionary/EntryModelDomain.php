<?php

namespace App\Domain\Gs2Dictionary;

use App\Domain\BaseDomain;
use Illuminate\Contracts\View\View;

class EntryModelDomain extends BaseDomain {

    public NamespaceDomain $namespace;
    public string $entryModelName;

    public function __construct(
        NamespaceDomain $namespace,
        string $entryModelName,
    ) {
        $this->namespace = $namespace;
        $this->entryModelName = $entryModelName;
    }

    public function infoView(
        string $view,
    ): View
    {
        return view($view)
            ->with("entryModel", $this);
    }

    public function addEntriesMetricsView(
        string $timeSpan,
    ): View
    {
        $service = 'dictionary';
        $method = 'addEntries';
        $category = 'count';

        return $this->metrics(
            $service,
            $method,
            $category,
            $timeSpan,
            [
                'namespaceName' => $this->namespace->namespaceName,
                'entryModelNames' => $this->entryModelName,
            ]
        );
    }

    public function metricsView(
        string $view,
    ): View
    {
        return view($view)
            ->with("entryModel", $this)
            ->with('metrics', [
                "hourly" => [
                    $this->addEntriesMetricsView(
                        "hourly",
                    ),
                ],
                "daily" => [
                    $this->addEntriesMetricsView(
                        "daily",
                    ),
                ],
                "weekly" => [
                    $this->addEntriesMetricsView(
                        "weekly",
                    ),
                ],
                "monthly" => [
                    $this->addEntriesMetricsView(
                        "monthly",
                    ),
                ],
            ]);
    }
}
