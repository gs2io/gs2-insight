<?php

namespace App\Domain\Gs2Mission;

use App\Domain\BaseDomain;
use App\Models\Grn;
use App\Models\GrnKey;
use App\Models\Timeline;
use Gs2\Core\Net\Gs2RestSession;
use Gs2\Mission\Gs2MissionRestClient;
use Gs2\Mission\Request\GetCompleteByUserIdRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use JetBrains\PhpStorm\Pure;

class MissionGroupDomain extends BaseDomain {

    public UserDomain $user;
    public string $missionGroupModelName;

    public function __construct(
        UserDomain $user,
        string     $missionGroupModelName,
    ) {
        $this->user = $user;
        $this->missionGroupModelName = $missionGroupModelName;
    }

    #[Pure] public function complete(
    ): CompleteDomain {
        return new CompleteDomain(
            $this,
        );
    }

    #[Pure] public function missionTask(
        string $missionTaskModelName,
    ): MissionTaskDomain {
        return new MissionTaskDomain(
            $this,
            $missionTaskModelName
        );
    }

    public function missionTasks(
        string $missionTaskModelName = null,
    ): Builder {
        $entries = Grn::query()
            ->where("parent", "grn:mission:namespace:{$this->user->namespace->namespaceName}:missionGroupModel:{$this->missionGroupModelName}")
            ->where("category", "missionTaskModel");
        if (!is_null($missionTaskModelName)) {
            $entries->where('key', 'like', "$missionTaskModelName%");
        }
        return $entries;
    }

    public function currentMissionTasks(
    ): array {
        try {
            $complete = $this->gs2(
                function (Gs2RestSession $session) {
                    $client = new Gs2MissionRestClient(
                        $session,
                    );
                    $result = $client->getCompleteByUserId(
                        (new GetCompleteByUserIdRequest())
                            ->withNamespaceName($this->user->namespace->namespaceName)
                            ->withUserId($this->user->userId)
                            ->withMissionGroupName($this->missionGroupModelName)
                    );
                    return $result->getItem();
                }
            );

            return $this->missionTasks()->get()->transform(
                function ($grn) use ($complete) {
                    return new MissionTaskDomain(
                        $this,
                        $grn->key,
                        $complete,
                    );
                }
            )->toArray();
        } catch (\Exception $e) {
            var_dump($e->getMessage());
            return [];
        }
    }

    public function infoView(
        string $view,
    ): View
    {
        return view($view)
            ->with("missionGroup", $this);
    }

    public function missionTasksView(
        string $view,
        string $missionTaskModelName = null,
    ): View
    {
        return view($view)
            ->with("missionGroup", $this)
            ->with("missionTasks", (
            tap(
                $this->missionTasks(
                    $missionTaskModelName,
                )
                    ->simplePaginate(10, ['*'], 'user_missionTasks')
            )->transform(
                function ($grn) {
                    return new MissionTaskDomain(
                        $this,
                        $grn->key,
                    );
                }
            )
            ));
    }

    public function currentMissionTasksView(
        string $view,
    ): View
    {
        return view($view)
            ->with('missionGroup', $this)
            ->with("missionTasks", $this->currentMissionTasks());
    }

    public function timeline(): Builder
    {
        return Timeline::query()
            ->joinSub(
                GrnKey::query()
                    ->where('category', 'missionTaskModel')
                    ->addNestedWhereQuery(
                        GrnKey::query()
                            ->where('grn', '=', "grn:mission:namespace:{$this->user->namespace->namespaceName}:user:{$this->user->userId}:missionGroupModel:{$this->missionGroupModelName}")
                            ->orWhere('grn', 'like', "grn:mission:namespace:{$this->user->namespace->namespaceName}:user:{$this->user->userId}:missionGroupModel:{$this->missionGroupModelName}:%")
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
            ->with("missionGroup", $this)
            ->with("timeline", $this->timeline()->simplePaginate(10, ['*'], 'user_timeline'));

    }

    public function missionTaskControllerView(
        string $view,
    ): View
    {
        return view($view)
            ->with('missionGroup', $this);
    }
}
