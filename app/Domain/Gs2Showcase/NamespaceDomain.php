<?php

namespace App\Domain\Gs2Showcase;

use App\Domain\BaseDomain;
use App\Models\Grn;
use App\Models\Player;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use JetBrains\PhpStorm\Pure;

class NamespaceDomain extends BaseDomain {

    public string $namespaceName;

    public function __construct(
        string $namespaceName,
    ) {
        $this->namespaceName = $namespaceName;
    }

    #[Pure] public function showcaseModel(
        string $showcaseModelName,
    ): ShowcaseModelDomain {
        return new ShowcaseModelDomain(
            $this,
            $showcaseModelName
        );
    }

    public function showcaseModels(
        string $showcaseModelName = null,
    ): Builder {
        $showcaseModels = Grn::query()
            ->where("parent", "grn:showcase:namespace:{$this->namespaceName}")
            ->where("category", "showcaseModel");
        if (!is_null($showcaseModelName)) {
            $showcaseModels->where('key', 'like', "$showcaseModelName%");
        }
        return $showcaseModels;
    }

    #[Pure] public function user(
        string $userId,
    ): UserDomain {
        return new UserDomain(
            $this,
            $userId,
        );
    }

    public function users(
        string $userId = null,
    ): Builder {
        $users = Player::query();
        if (!is_null($userId)) {
            $users->where('userId', 'like', "$userId%");
        }
        return $users;
    }

    public function infoView(
        string $view,
    ): View
    {
        return view($view)
            ->with("namespace", $this);
    }

    public function showcaseModelsView(
        string $view,
        string $showcaseModelName = null,
    ): View
    {
        return view($view)
            ->with("namespace", $this)
            ->with("showcaseModels", (
            tap(
                $this->showcaseModels(
                    $showcaseModelName,
                )
                    ->simplePaginate(10, ['*'], 'namespace_showcaseModels')
            )->transform(
                function ($grn) {
                    return new ShowcaseModelDomain(
                        $this,
                        $grn->key,
                    );
                }
            )
            ));
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
                'namespaceName' => $this->namespaceName,
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
