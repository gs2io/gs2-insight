<?php

namespace App\Domain\Gs2Quest;

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

    #[Pure] public function questGroupModel(
        string $questGroupModelName,
    ): QuestGroupModelDomain {
        return new QuestGroupModelDomain(
            $this,
            $questGroupModelName
        );
    }

    public function questGroupModels(
        string $questGroupModelName = null,
    ): Builder {
        $questGroupModels = Grn::query()
            ->where("parent", "grn:quest:namespace:{$this->namespaceName}")
            ->where("category", "questGroupModel");
        if (!is_null($questGroupModelName)) {
            $questGroupModels->where('key', 'like', "$questGroupModelName%");
        }
        return $questGroupModels;
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

    public function questGroupModelsView(
        string $view,
        string $questGroupModelName = null,
    ): View
    {
        return view($view)
            ->with("namespace", $this)
            ->with("questGroupModels", (
            tap(
                $this->questGroupModels(
                    $questGroupModelName,
                )
                    ->simplePaginate(10, ['*'], 'namespace_questGroupModels')
            )->transform(
                function ($grn) {
                    return new QuestGroupModelDomain(
                        $this,
                        $grn->key,
                    );
                }
            )
            ));
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
                'namespaceName' => $this->namespaceName,
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
                'namespaceName' => $this->namespaceName,
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
