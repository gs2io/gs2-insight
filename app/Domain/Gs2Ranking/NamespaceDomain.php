<?php

namespace App\Domain\Gs2Ranking;

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

    #[Pure] public function categoryModel(
        string $categoryModelName,
    ): CategoryModelDomain {
        return new CategoryModelDomain(
            $this,
            $categoryModelName
        );
    }

    public function categoryModels(
        string $categoryModelName = null,
    ): Builder {
        $categoryModels = Grn::query()
            ->where("parent", "grn:ranking:namespace:{$this->namespaceName}")
            ->where("category", "categoryModel");
        if (!is_null($categoryModelName)) {
            $categoryModels->where('key', 'like', "$categoryModelName%");
        }
        return $categoryModels;
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

    public function categoryModelsView(
        string $view,
        string $categoryModelName = null,
    ): View
    {
        return view($view)
            ->with("namespace", $this)
            ->with("categoryModels", (
            tap(
                $this->categoryModels(
                    $categoryModelName,
                )
                    ->simplePaginate(10, ['*'], 'namespace_categoryModels')
            )->transform(
                function ($grn) {
                    return new CategoryModelDomain(
                        $this,
                        $grn->key,
                    );
                }
            )
            ));
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
