<?php

namespace App\Domain\Gs2Ranking;

use App\Domain\BaseDomain;
use App\Models\Grn;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use JetBrains\PhpStorm\Pure;

class CategoryModelDomain extends BaseDomain {

    public NamespaceDomain $namespace;
    public string $categoryModelName;

    public function __construct(
        NamespaceDomain $namespace,
        string $categoryModelName,
    ) {
        $this->namespace = $namespace;
        $this->categoryModelName = $categoryModelName;
    }

    public function infoView(
        string $view,
    ): View
    {
        return view($view)
            ->with("categoryModel", $this);
    }


    public function putScoreMetricsView(
        string $timeSpan,
    ): View
    {
        $service = 'ranking';
        $method = 'putScore';
        $category = 'count';

        return $this->metrics(
            $service,
            $method,
            $category,
            $timeSpan,
            [
                'namespaceName' => $this->namespace->namespaceName,
                'categoryName' => $this->categoryModelName,
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
                    $this->putScoreMetricsView(
                        "hourly",
                    ),
                ],
                "daily" => [
                    $this->putScoreMetricsView(
                        "daily",
                    ),
                ],
                "weekly" => [
                    $this->putScoreMetricsView(
                        "weekly",
                    ),
                ],
                "monthly" => [
                    $this->putScoreMetricsView(
                        "monthly",
                    ),
                ],
            ]);
    }
}
