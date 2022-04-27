<?php

namespace App\Domain\Gs2Mission;

use App\Domain\BaseDomain;
use App\Models\Grn;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use JetBrains\PhpStorm\Pure;

class MissionGroupModelDomain extends BaseDomain {

    public NamespaceDomain $namespace;
    public string $missionGroupModelName;

    public function __construct(
        NamespaceDomain $namespace,
        string $missionGroupModelName,
    ) {
        $this->namespace = $namespace;
        $this->missionGroupModelName = $missionGroupModelName;
    }

    #[Pure] public function missionTaskModel(
        string $missionTaskModelName,
    ): MissionTaskModelDomain {
        return new MissionTaskModelDomain(
            $this,
            $missionTaskModelName
        );
    }

    public function missionTaskModels(
        string $missionTaskModelName = null,
    ): Builder {
        $missionTaskModels = Grn::query()
            ->where("parent", "grn:mission:namespace:{$this->namespace->namespaceName}:missionGroupModel:{$this->missionGroupModelName}")
            ->where("category", "missionTaskModel");
        if (!is_null($missionTaskModelName)) {
            $missionTaskModels->where('key', 'like', "$missionTaskModelName%");
        }
        return $missionTaskModels;
    }

    public function missionTaskModelsView(
        string $view,
        string $missionTaskModelName = null,
    ): View
    {
        return view($view)
            ->with("missionGroupModel", $this)
            ->with("missionTaskModels", (
            tap(
                $this->missionTaskModels(
                    $missionTaskModelName,
                )
                    ->simplePaginate(10, ['*'], 'namespace_missionTaskModels')
            )->transform(
                function ($grn) {
                    return new MissionTaskModelDomain(
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
            ->with("missionGroupModel", $this);
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
                'namespaceName' => $this->namespace->namespaceName,
                'missionGroupName' => $this->missionGroupModelName,
            ]
        );
    }

    public function metricsView(
        string $view,
    ): View
    {
        return view($view)
            ->with("missionGroupModel", $this)
            ->with('metrics', [
                "hourly" => [
                    $this->receiveMetricsView(
                        "hourly",
                    ),
                ],
                "daily" => [
                    $this->receiveMetricsView(
                        "daily",
                    ),
                ],
                "weekly" => [
                    $this->receiveMetricsView(
                        "weekly",
                    ),
                ],
                "monthly" => [
                    $this->receiveMetricsView(
                        "monthly",
                    ),
                ],
            ]);
    }
}
