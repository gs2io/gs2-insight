<?php

namespace App\Domain\Gs2Exchange;

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

    #[Pure] public function rateModel(
        string $rateModelName,
    ): RateModelDomain {
        return new RateModelDomain(
            $this,
            $rateModelName
        );
    }

    public function rateModels(
        string $rateModelName = null,
    ): Builder {
        $rateModels = Grn::query()
            ->where("parent", "grn:exchange:namespace:{$this->namespaceName}")
            ->where("category", "rateModel");
        if (!is_null($rateModelName)) {
            $rateModels->where('key', 'like', "$rateModelName%");
        }
        return $rateModels;
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

    public function rateModelsView(
        string $view,
        string $rateModelName = null,
    ): View
    {
        return view($view)
            ->with("namespace", $this)
            ->with("rateModels", (
            tap(
                $this->rateModels(
                    $rateModelName,
                )
                    ->simplePaginate(10, ['*'], 'namespace_rateModels')
            )->transform(
                function ($grn) {
                    return new RateModelDomain(
                        $this,
                        $grn->key,
                    );
                }
            )
            ));
    }

    public function acquireMetricsView(
        string $timeSpan,
    ): View
    {
        $service = 'exchange';
        $method = 'acquire';
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

    public function exchangeMetricsView(
        string $timeSpan,
    ): View
    {
        $service = 'exchange';
        $method = 'exchange';
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

    public function skipMetricsView(
        string $timeSpan,
    ): View
    {
        $service = 'exchange';
        $method = 'skip';
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
                    $this->acquireMetricsView(
                        "hourly",
                    ),
                    $this->exchangeMetricsView(
                        "hourly",
                    ),
                    $this->skipMetricsView(
                        "hourly",
                    ),
                ],
                "daily" => [
                    $this->acquireMetricsView(
                        "daily",
                    ),
                    $this->exchangeMetricsView(
                        "daily",
                    ),
                    $this->skipMetricsView(
                        "daily",
                    ),
                ],
                "weekly" => [
                    $this->acquireMetricsView(
                        "weekly",
                    ),
                    $this->exchangeMetricsView(
                        "weekly",
                    ),
                    $this->skipMetricsView(
                        "weekly",
                    ),
                ],
                "monthly" => [
                    $this->acquireMetricsView(
                        "monthly",
                    ),
                    $this->exchangeMetricsView(
                        "monthly",
                    ),
                    $this->skipMetricsView(
                        "monthly",
                    ),
                ],
            ]);
    }
}
