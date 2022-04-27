<?php

namespace App\Domain\Gs2Mission;

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

    #[Pure] public function missionGroupModel(
        string $missionGroupModelName,
    ): MissionGroupModelDomain {
        return new MissionGroupModelDomain(
            $this,
            $missionGroupModelName
        );
    }

    public function missionGroupModels(
        string $missionGroupModelName = null,
    ): Builder {
        $missionGroupModels = Grn::query()
            ->where("parent", "grn:mission:namespace:{$this->namespaceName}")
            ->where("category", "missionGroupModel");
        if (!is_null($missionGroupModelName)) {
            $missionGroupModels->where('key', 'like', "$missionGroupModelName%");
        }
        return $missionGroupModels;
    }

    #[Pure] public function counterModel(
        string $counterModelName,
    ): CounterModelDomain {
        return new CounterModelDomain(
            $this,
            $counterModelName
        );
    }

    public function counterModels(
        string $counterModelName = null,
    ): Builder {
        $counterModels = Grn::query()
            ->where("parent", "grn:mission:namespace:{$this->namespaceName}")
            ->where("category", "counterModel");
        if (!is_null($counterModelName)) {
            $counterModels->where('key', 'like', "$counterModelName%");
        }
        return $counterModels;
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

    public function missionGroupModelsView(
        string $view,
        string $missionGroupModelName = null,
    ): View
    {
        return view($view)
            ->with("namespace", $this)
            ->with("missionGroupModels", (
            tap(
                $this->missionGroupModels(
                    $missionGroupModelName,
                )
                    ->simplePaginate(10, ['*'], 'namespace_missionGroupModels')
            )->transform(
                function ($grn) {
                    return new MissionGroupModelDomain(
                        $this,
                        $grn->key,
                    );
                }
            )
            ));
    }

    public function counterModelsView(
        string $view,
        string $counterModelName = null,
    ): View
    {
        return view($view)
            ->with("namespace", $this)
            ->with("counterModels", (
            tap(
                $this->counterModels(
                    $counterModelName,
                )
                    ->simplePaginate(10, ['*'], 'namespace_counterModels')
            )->transform(
                function ($grn) {
                    return new CounterModelDomain(
                        $this,
                        $grn->key,
                    );
                }
            )
            ));
    }

    public function increaseCounterMetricsView(
        string $timeSpan,
    ): View
    {
        $service = 'mission';
        $method = 'increaseCounter';
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

    public function increaseCounterSumMetricsView(
        string $timeSpan,
    ): View
    {
        $service = 'mission';
        $method = 'increaseCounter';
        $category = 'sum:value';

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

    public function receiveMetricsView(
        string $timeSpan,
    ): View
    {
        $service = 'mission';
        $method = 'receive';
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
                    $this->increaseCounterMetricsView(
                        "hourly",
                    ),
                    $this->increaseCounterSumMetricsView(
                        "hourly",
                    ),
                    $this->receiveMetricsView(
                        "hourly",
                    ),
                ],
                "daily" => [
                    $this->increaseCounterMetricsView(
                        "daily",
                    ),
                    $this->increaseCounterSumMetricsView(
                        "daily",
                    ),
                    $this->receiveMetricsView(
                        "daily",
                    ),
                ],
                "weekly" => [
                    $this->increaseCounterMetricsView(
                        "weekly",
                    ),
                    $this->increaseCounterSumMetricsView(
                        "weekly",
                    ),
                    $this->receiveMetricsView(
                        "weekly",
                    ),
                ],
                "monthly" => [
                    $this->increaseCounterMetricsView(
                        "monthly",
                    ),
                    $this->increaseCounterSumMetricsView(
                        "monthly",
                    ),
                    $this->receiveMetricsView(
                        "monthly",
                    ),
                ],
            ]);
    }
}
