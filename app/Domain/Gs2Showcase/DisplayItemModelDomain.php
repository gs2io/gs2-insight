<?php

namespace App\Domain\Gs2Showcase;

use App\Domain\BaseDomain;
use Illuminate\Contracts\View\View;

class DisplayItemModelDomain extends BaseDomain {

    public ShowcaseModelDomain $showcaseModel;
    public string $displayItemId;

    public function __construct(
        ShowcaseModelDomain $showcaseModel,
        string $displayItemId,
    ) {
        $this->showcaseModel = $showcaseModel;
        $this->displayItemId = $displayItemId;
    }

    public function infoView(
        string $view,
    ): View
    {
        return view($view)
            ->with("displayItemModel", $this);
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
                'namespaceName' => $this->showcaseModel->namespace->namespaceName,
                'showcaseName' => $this->showcaseModel->showcaseModelName,
                'displayItemId' => $this->displayItemId,
            ]
        );
    }

    public function metricsView(
        string $view,
    ): View
    {
        return view($view)
            ->with("displayItemModel", $this)
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
