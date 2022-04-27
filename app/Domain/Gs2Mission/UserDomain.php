<?php

namespace App\Domain\Gs2Mission;

use App\Domain\BaseDomain;
use App\Models\Grn;
use App\Models\GrnKey;
use App\Models\Timeline;
use Gs2\Core\Net\Gs2RestSession;
use Gs2\Mission\Gs2MissionRestClient;
use Gs2\Mission\Model\Counter;
use Gs2\Mission\Request\DescribeCountersByUserIdRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use JetBrains\PhpStorm\Pure;

class UserDomain extends BaseDomain {

    public NamespaceDomain $namespace;
    public string $userId;

    public function __construct(
        NamespaceDomain $namespace,
        string $userId,
    ) {
        $this->namespace = $namespace;
        $this->userId = $userId;
    }

    #[Pure] public function missionGroup(
        string $missionGroupModelName,
    ): MissionGroupDomain {
        return new MissionGroupDomain(
            $this,
            $missionGroupModelName
        );
    }

    public function missionGroups(
        string $missionGroupModelName = null,
    ): Builder {
        $entries = Grn::query()
            ->where("parent", "grn:mission:namespace:{$this->namespace->namespaceName}")
            ->where("category", "missionGroupModel");
        if (!is_null($missionGroupModelName)) {
            $entries->where('key', 'like', "$missionGroupModelName%");
        }
        return $entries;
    }

    #[Pure] public function counter(
        string $counterModelName,
    ): CounterDomain {
        return new CounterDomain(
            $this,
            $counterModelName
        );
    }

    public function counters(
        string $counterModelName = null,
    ): Builder {
        $entries = Grn::query()
            ->where("parent", "grn:mission:namespace:{$this->namespace->namespaceName}")
            ->where("category", "counterModel");
        if (!is_null($counterModelName)) {
            $entries->where('key', 'like', "$counterModelName%");
        }
        return $entries;
    }

    public function infoView(
        string $view,
    ): View
    {
        return view($view)
            ->with("user", $this);
    }

    public function currentMissionGroups(
    ): array {
        return $this->missionGroups()->get()->transform(
            function ($grn) {
                return new MissionGroupDomain(
                    $this,
                    $grn->key,
                );
            }
        )->toArray();
    }

    public function currentCounters(
    ): array {
        $counters = $this->gs2(
            function (Gs2RestSession $session) {
                $client = new Gs2MissionRestClient(
                    $session,
                );
                $result = $client->describeCountersByUserId(
                    (new DescribeCountersByUserIdRequest())
                        ->withNamespaceName($this->namespace->namespaceName)
                        ->withUserId($this->userId)
                        ->withLimit(1000)
                );
                return $result->getItems();
            }
        );
        return $this->counters()->get()->transform(
            function ($grn) use ($counters) {
                $filteredCounters = array_filter($counters, function (Counter $counter) use ($grn) {
                    return $counter->getName() == $grn->key;
                });
                if (count($filteredCounters) > 0) {
                    return new CounterDomain(
                        $this,
                        $grn->key,
                        $filteredCounters[array_key_first($filteredCounters)],
                    );
                } else {
                    return new CounterDomain(
                        $this,
                        $grn->key,
                        (new Counter())
                            ->withName($grn->key)
                            ->withUserId($this->userId)
                            ->withValues([]),
                    );
                }
            }
        )->toArray();
    }

    public function missionGroupsView(
        string $view,
        string $missionGroupModelName = null,
    ): View
    {
        return view($view)
            ->with("user", $this)
            ->with("missionGroups", (
            tap(
                $this->missionGroups(
                    $missionGroupModelName,
                )
                    ->simplePaginate(10, ['*'], 'user_missionGroups')
            )->transform(
                function ($grn) {
                    return new MissionGroupDomain(
                        $this,
                        $grn->key,
                    );
                }
            )
            ));
    }

    public function currentMissionGroupsView(
        string $view,
    ): View
    {
        return view($view)
            ->with('user', $this)
            ->with("missionGroups", $this->currentMissionGroups());
    }

    public function currentCountersView(
        string $view,
    ): View
    {
        return view($view)
            ->with('user', $this)
            ->with("counters", $this->currentCounters());
    }

    public function countersView(
        string $view,
        string $counterModelName = null,
    ): View
    {
        return view($view)
            ->with("user", $this)
            ->with("counters", (
            tap(
                $this->counters(
                    $counterModelName,
                )
                    ->simplePaginate(10, ['*'], 'user_counters')
            )->transform(
                function ($grn) {
                    return new CounterDomain(
                        $this,
                        $grn->key,
                    );
                }
            )
            ));
    }

    public function timeline(): Builder
    {
        return Timeline::query()
            ->joinSub(
                GrnKey::query()
                    ->where('category', 'missionGroupModel')
                    ->addNestedWhereQuery(
                        GrnKey::query()
                            ->where('grn', '=', "grn:mission:namespace:{$this->namespace->namespaceName}:user:{$this->userId}")
                            ->orWhere('grn', 'like', "grn:mission:namespace:{$this->namespace->namespaceName}:user:{$this->userId}:%")
                            ->getQuery()
                    ),
                "grnKey",
                "transactionId",
                "=",
                "grnKey.requestId",
            )->orderByDesc(
                "timestamp"
            );
    }

    public function timelineView(
        string $view,
    ): View
    {
        return view($view)
            ->with("user", $this)
            ->with("timeline", $this->timeline()->simplePaginate(10, ['*'], 'user_timeline'));

    }

    public function counterControllerView(
        string $view,
    ): View
    {
        return view($view)
            ->with("user", $this);
    }

}
