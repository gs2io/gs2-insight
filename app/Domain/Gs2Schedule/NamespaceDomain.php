<?php

namespace App\Domain\Gs2Schedule;

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

    #[Pure] public function eventModel(
        string $eventModelName,
    ): EventModelDomain {
        return new EventModelDomain(
            $this,
            $eventModelName
        );
    }

    public function eventModels(
        string $eventModelName = null,
    ): Builder {
        $eventModels = Grn::query()
            ->where("parent", "grn:schedule:namespace:{$this->namespaceName}")
            ->where("category", "eventModel");
        if (!is_null($eventModelName)) {
            $eventModels->where('key', 'like', "$eventModelName%");
        }
        return $eventModels;
    }

    #[Pure] public function triggerModel(
        string $triggerModelName,
    ): TriggerModelDomain {
        return new TriggerModelDomain(
            $this,
            $triggerModelName
        );
    }

    public function triggerModels(
        string $triggerModelName = null,
    ): Builder {
        $triggerModels = Grn::query()
            ->where("parent", "grn:schedule:namespace:{$this->namespaceName}")
            ->where("category", "triggerModel");
        if (!is_null($triggerModelName)) {
            $triggerModels->where('key', 'like', "$triggerModelName%");
        }
        return $triggerModels;
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

    public function eventModelsView(
        string $view,
        string $eventModelName = null,
    ): View
    {
        return view($view)
            ->with("namespace", $this)
            ->with("eventModels", (
            tap(
                $this->eventModels(
                    $eventModelName,
                )
                    ->simplePaginate(10, ['*'], 'namespace_eventModels')
            )->transform(
                function ($grn) {
                    return new EventModelDomain(
                        $this,
                        $grn->key,
                    );
                }
            )
            ));
    }

    public function triggerModelsView(
        string $view,
        string $triggerModelName = null,
    ): View
    {
        return view($view)
            ->with("namespace", $this)
            ->with("triggerModels", (
            tap(
                $this->triggerModels(
                    $triggerModelName,
                )
                    ->simplePaginate(10, ['*'], 'namespace_triggerModels')
            )->transform(
                function ($grn) {
                    return new TriggerModelDomain(
                        $this,
                        $grn->key,
                    );
                }
            )
            ));
    }

    public function triggerMetricsView(
        string $timeSpan,
    ): View
    {
        $service = 'schedule';
        $method = 'trigger';
        $event = 'count';

        return $this->metrics(
            $service,
            $method,
            $event,
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
                    $this->triggerMetricsView(
                        "hourly",
                    ),
                ],
                "daily" => [
                    $this->triggerMetricsView(
                        "daily",
                    ),
                ],
                "weekly" => [
                    $this->triggerMetricsView(
                        "weekly",
                    ),
                ],
                "monthly" => [
                    $this->triggerMetricsView(
                        "monthly",
                    ),
                ],
            ]);
    }
}
