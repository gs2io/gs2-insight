<?php

namespace App\Domain\Gs2Showcase;

use App\Domain\BaseDomain;
use App\Models\Grn;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use JetBrains\PhpStorm\Pure;

class ShowcaseModelDomain extends BaseDomain {

    public NamespaceDomain $namespace;
    public string $showcaseModelName;

    public function __construct(
        NamespaceDomain $namespace,
        string $showcaseModelName,
    ) {
        $this->namespace = $namespace;
        $this->showcaseModelName = $showcaseModelName;
    }

    #[Pure] public function displayItemModel(
        string $displayItemId,
    ): DisplayItemModelDomain {
        return new DisplayItemModelDomain(
            $this,
            $displayItemId
        );
    }

    public function displayItemModels(
        string $displayItemId = null,
    ): Builder {
        $displayItemModels = Grn::query()
            ->where("parent", "grn:showcase:namespace:{$this->namespace->namespaceName}:showcaseModel:{$this->showcaseModelName}")
            ->where("category", "displayItemModel");
        if (!is_null($displayItemId)) {
            $displayItemModels->where('key', 'like', "$displayItemId%");
        }
        return $displayItemModels;
    }

    public function displayItemModelsView(
        string $view,
        string $displayItemId = null,
    ): View
    {
        return view($view)
            ->with("showcaseModel", $this)
            ->with("displayItemModels", (
            tap(
                $this->displayItemModels(
                    $displayItemId,
                )
                    ->simplePaginate(10, ['*'], 'namespace_displayItemModels')
            )->transform(
                function ($grn) {
                    return new DisplayItemModelDomain(
                        $this,
                        $grn->key,
                    );
                }
            )
            ));
    }

    public function infoView(
        string $view,
    ): View
    {
        return view($view)
            ->with("showcaseModel", $this);
    }

    public function buyMetricsView(
        string $timeSpan,
    ): View
    {
        $service = 'showcase';
        $method = 'buy';
        $category = 'count';

        return $this->metrics(
            $service,
            $method,
            $category,
            $timeSpan,
            [
                'namespaceName' => $this->namespace->namespaceName,
                'showcaseName' => $this->showcaseModelName,
            ]
        );
    }

    public function metricsView(
        string $view,
    ): View
    {
        return view($view)
            ->with("showcaseModel", $this)
            ->with('metrics', [
                "hourly" => [
                    $this->buyMetricsView(
                        "hourly",
                    ),
                ],
                "daily" => [
                    $this->buyMetricsView(
                        "daily",
                    ),
                ],
                "weekly" => [
                    $this->buyMetricsView(
                        "weekly",
                    ),
                ],
                "monthly" => [
                    $this->buyMetricsView(
                        "monthly",
                    ),
                ],
            ]);
    }
}
