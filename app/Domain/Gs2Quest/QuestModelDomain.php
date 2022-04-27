<?php

namespace App\Domain\Gs2Quest;

use App\Domain\BaseDomain;
use Gs2\Core\Net\Gs2RestSession;
use Gs2\Quest\Gs2QuestRestClient;
use Gs2\Quest\Model\QuestModel;
use Gs2\Quest\Request\GetQuestModelRequest;
use Illuminate\Contracts\View\View;

class QuestModelDomain extends BaseDomain {

    public QuestGroupModelDomain $questGroupModel;
    public string $questModelName;

    public function __construct(
        QuestGroupModelDomain $questGroupModel,
        string $questModelName,
    ) {
        $this->questGroupModel = $questGroupModel;
        $this->questModelName = $questModelName;
    }

    public function infoView(
        string $view,
    ): View
    {
        return view($view)
            ->with("questModel", $this);
    }

    public function startMetricsView(
        string $timeSpan,
    ): View
    {
        $service = 'quest';
        $method = 'start';
        $category = 'count';

        return $this->metrics(
            $service,
            $method,
            $category,
            $timeSpan,
            [
                'namespaceName' => $this->questGroupModel->namespace->namespaceName,
                'questGroupName' => $this->questGroupModel->questGroupModelName,
                'questName' => $this->questModelName,
            ]
        );
    }

    public function endMetricsView(
        string $timeSpan,
    ): View
    {
        $service = 'quest';
        $method = 'end';
        $category = 'count';

        return $this->metrics(
            $service,
            $method,
            $category,
            $timeSpan,
            [
                'namespaceName' => $this->questGroupModel->namespace->namespaceName,
                'questGroupName' => $this->questGroupModel->questGroupModelName,
                'questName' => $this->questModelName,
            ]
        );
    }

    public function completeMetricsView(
        string $timeSpan,
    ): View
    {
        $service = 'quest';
        $method = 'complete';
        $category = 'count';

        return $this->metrics(
            $service,
            $method,
            $category,
            $timeSpan,
            [
                'namespaceName' => $this->questGroupModel->namespace->namespaceName,
                'questGroupName' => $this->questGroupModel->questGroupModelName,
                'questName' => $this->questModelName,
            ]
        );
    }

    public function metricsView(
        string $view,
    ): View
    {
        return view($view)
            ->with("questGroupModel", $this)
            ->with('metrics', [
                "hourly" => [
                    $this->startMetricsView(
                        "hourly",
                    ),
                    $this->endMetricsView(
                        "hourly",
                    ),
                    $this->completeMetricsView(
                        "hourly",
                    ),
                ],
                "daily" => [
                    $this->startMetricsView(
                        "daily",
                    ),
                    $this->endMetricsView(
                        "daily",
                    ),
                    $this->completeMetricsView(
                        "daily",
                    ),
                ],
                "weekly" => [
                    $this->startMetricsView(
                        "weekly",
                    ),
                    $this->endMetricsView(
                        "weekly",
                    ),
                    $this->completeMetricsView(
                        "weekly",
                    ),
                ],
                "monthly" => [
                    $this->startMetricsView(
                        "monthly",
                    ),
                    $this->endMetricsView(
                        "monthly",
                    ),
                    $this->completeMetricsView(
                        "monthly",
                    ),
                ],
            ]);
    }
}
