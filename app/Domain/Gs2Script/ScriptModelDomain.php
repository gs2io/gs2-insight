<?php

namespace App\Domain\Gs2Script;

use App\Domain\BaseDomain;
use App\Models\Grn;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use JetBrains\PhpStorm\Pure;

class ScriptModelDomain extends BaseDomain {

    public NamespaceDomain $namespace;
    public string $scriptModelName;

    public function __construct(
        NamespaceDomain $namespace,
        string $scriptModelName,
    ) {
        $this->namespace = $namespace;
        $this->scriptModelName = $scriptModelName;
    }

    public function infoView(
        string $view,
    ): View
    {
        return view($view)
            ->with("scriptModel", $this);
    }


    public function invokeMetricsView(
        string $timeSpan,
    ): View
    {
        $service = 'script';
        $method = 'invoke';
        $script = 'count';

        return $this->metrics(
            $service,
            $method,
            $script,
            $timeSpan,
            [
                'namespaceName' => $this->namespace->namespaceName,
                'scriptName' => $this->scriptModelName,
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
                    $this->invokeMetricsView(
                        "hourly",
                    ),
                ],
                "daily" => [
                    $this->invokeMetricsView(
                        "daily",
                    ),
                ],
                "weekly" => [
                    $this->invokeMetricsView(
                        "weekly",
                    ),
                ],
                "monthly" => [
                    $this->invokeMetricsView(
                        "monthly",
                    ),
                ],
            ]);
    }
}
