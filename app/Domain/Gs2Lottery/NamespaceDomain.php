<?php

namespace App\Domain\Gs2Lottery;

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

    #[Pure] public function lotteryModel(
        string $lotteryModelName,
    ): LotteryModelDomain {
        return new LotteryModelDomain(
            $this,
            $lotteryModelName
        );
    }

    public function lotteryModels(
        string $lotteryModelName = null,
    ): Builder {
        $lotteryModels = Grn::query()
            ->where("parent", "grn:lottery:namespace:{$this->namespaceName}")
            ->where("category", "lotteryModel");
        if (!is_null($lotteryModelName)) {
            $lotteryModels->where('key', 'like', "$lotteryModelName%");
        }
        return $lotteryModels;
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

    public function lotteryModelsView(
        string $view,
        string $lotteryModelName = null,
    ): View
    {
        return view($view)
            ->with("namespace", $this)
            ->with("lotteryModels", (
            tap(
                $this->lotteryModels(
                    $lotteryModelName,
                )
                    ->simplePaginate(10, ['*'], 'namespace_lotteryModels')
            )->transform(
                function ($grn) {
                    return new LotteryModelDomain(
                        $this,
                        $grn->key,
                    );
                }
            )
            ));
    }

    public function drawMetricsView(
        string $timeSpan,
    ): View
    {
        $service = 'lottery';
        $method = 'draw';
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

    public function drawSumMetricsView(
        string $timeSpan,
    ): View
    {
        $service = 'lottery';
        $method = 'draw';
        $category = 'sum:count';

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
                    $this->drawMetricsView(
                        "hourly",
                    ),
                    $this->drawSumMetricsView(
                        "hourly",
                    ),
                ],
                "daily" => [
                    $this->drawMetricsView(
                        "daily",
                    ),
                    $this->drawSumMetricsView(
                        "daily",
                    ),
                ],
                "weekly" => [
                    $this->drawMetricsView(
                        "weekly",
                    ),
                    $this->drawSumMetricsView(
                        "weekly",
                    ),
                ],
                "monthly" => [
                    $this->drawMetricsView(
                        "monthly",
                    ),
                    $this->drawSumMetricsView(
                        "monthly",
                    ),
                ],
            ]);
    }
}
