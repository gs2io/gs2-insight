<?php

namespace App\Domain\Gs2Quest;

use App\Domain\BaseDomain;
use App\Models\Grn;
use Gs2\Core\Net\Gs2RestSession;
use Gs2\Quest\Gs2QuestRestClient;
use Gs2\Quest\Model\QuestGroupModel;
use Gs2\Quest\Request\GetQuestGroupModelRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use JetBrains\PhpStorm\Pure;

class QuestGroupModelDomain extends BaseDomain {

    public NamespaceDomain $namespace;
    public string $questGroupModelName;

    public function __construct(
        NamespaceDomain $namespace,
        string $questGroupModelName,
    ) {
        $this->namespace = $namespace;
        $this->questGroupModelName = $questGroupModelName;
    }

    #[Pure] public function questModel(
        string $questModelName,
    ): QuestModelDomain {
        return new QuestModelDomain(
            $this,
            $questModelName
        );
    }

    public function questModels(
        string $questModelName = null,
    ): Builder {
        $questModels = Grn::query()
            ->where("parent", "grn:quest:namespace:{$this->namespace->namespaceName}:questGroupModel:{$this->questGroupModelName}")
            ->where("category", "questModel");
        if (!is_null($questModelName)) {
            $questModels->where('key', 'like', "$questModelName%");
        }
        return $questModels;
    }

    public function questModelsView(
        string $view,
        string $questModelName = null,
    ): View
    {
        return view($view)
            ->with("questGroupModel", $this)
            ->with("questModels", (
            tap(
                $this->questModels(
                    $questModelName,
                )
                    ->simplePaginate(10, ['*'], 'namespace_questModels')
            )->transform(
                function ($grn) {
                    return new QuestModelDomain(
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
            ->with("questGroupModel", $this);
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
                'namespaceName' => $this->namespace->namespaceName,
                'questGroupName' => $this->questGroupModelName,
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
                'namespaceName' => $this->namespace->namespaceName,
                'questGroupName' => $this->questGroupModelName,
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
                'namespaceName' => $this->namespace->namespaceName,
                'questGroupName' => $this->questGroupModelName,
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
