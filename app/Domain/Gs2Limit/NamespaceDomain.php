<?php

namespace App\Domain\Gs2Limit;

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

    #[Pure] public function limitModel(
        string $limitModelName,
    ): LimitModelDomain {
        return new LimitModelDomain(
            $this,
            $limitModelName
        );
    }

    public function limitModels(
        string $limitModelName = null,
    ): Builder {
        $limitModels = Grn::query()
            ->where("parent", "grn:limit:namespace:{$this->namespaceName}")
            ->where("category", "limitModel");
        if (!is_null($limitModelName)) {
            $limitModels->where('key', 'like', "$limitModelName%");
        }
        return $limitModels;
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

    public function limitModelsView(
        string $view,
        string $limitModelName = null,
    ): View
    {
        return view($view)
            ->with("namespace", $this)
            ->with("limitModels", (
            tap(
                $this->limitModels(
                    $limitModelName,
                )
                    ->simplePaginate(10, ['*'], 'namespace_limitModels')
            )->transform(
                function ($grn) {
                    return new LimitModelDomain(
                        $this,
                        $grn->key,
                    );
                }
            )
            ));
    }

    public function countUpMetricsView(
        string $timeSpan,
    ): View
    {
        $service = 'limit';
        $method = 'countUp';
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

    public function countUpSumMetricsView(
        string $timeSpan,
    ): View
    {
        $service = 'limit';
        $method = 'countUp';
        $category = 'sum:countUpValue';

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
                    $this->countUpMetricsView(
                        "hourly",
                    ),
                    $this->countUpSumMetricsView(
                        "hourly",
                    ),
                ],
                "daily" => [
                    $this->countUpMetricsView(
                        "daily",
                    ),
                    $this->countUpSumMetricsView(
                        "daily",
                    ),
                ],
                "weekly" => [
                    $this->countUpMetricsView(
                        "weekly",
                    ),
                    $this->countUpSumMetricsView(
                        "weekly",
                    ),
                ],
                "monthly" => [
                    $this->countUpMetricsView(
                        "monthly",
                    ),
                    $this->countUpSumMetricsView(
                        "monthly",
                    ),
                ],
            ]);
    }
}
