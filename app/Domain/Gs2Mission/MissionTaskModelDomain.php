<?php

namespace App\Domain\Gs2Mission;

use App\Domain\BaseDomain;
use Illuminate\Contracts\View\View;

class MissionTaskModelDomain extends BaseDomain {

    public MissionGroupModelDomain $missionGroupModel;
    public string $missionTaskModelName;

    public function __construct(
        MissionGroupModelDomain $missionGroupModel,
        string $missionTaskModelName,
    ) {
        $this->missionGroupModel = $missionGroupModel;
        $this->missionTaskModelName = $missionTaskModelName;
    }

    public function infoView(
        string $view,
    ): View
    {
        return view($view)
            ->with("missionTaskModel", $this);
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
                'namespaceName' => $this->missionGroupModel->namespace->namespaceName,
                'missionGroupName' => $this->missionGroupModel->missionGroupModelName,
                'missionTaskName' => $this->missionTaskModelName,
            ]
        );
    }

    public function metricsView(
        string $view,
    ): View
    {
        return view($view)
            ->with("missionTaskModel", $this)
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
