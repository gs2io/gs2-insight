<?php

namespace App\Domain\Gs2Quest;

use App\Domain\BaseDomain;
use App\Models\Grn;
use App\Models\GrnKey;
use App\Models\Timeline;
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

    #[Pure] public function progress(
    ): ProgressDomain {
        return new ProgressDomain(
            $this,
        );
    }

    public function currentQuestGroups(
    ): array {
        return $this->questGroups()->get()->transform(
            function ($grn) {
                return new QuestGroupDomain(
                    $this,
                    $grn->key,
                );
            }
        )->toArray();
    }

    #[Pure] public function questGroup(
        string $questGroupModelName,
    ): QuestGroupDomain {
        return new QuestGroupDomain(
            $this,
            $questGroupModelName
        );
    }

    public function questGroups(
        string $questGroupModelName = null,
    ): Builder {
        $types = Grn::query()
            ->where("parent", "grn:quest:namespace:{$this->namespace->namespaceName}")
            ->where("category", "questGroupModel");
        if (!is_null($questGroupModelName)) {
            $types->where('key', '=', $questGroupModelName);
        }
        return $types;
    }

    public function infoView(
        string $view,
    ): View
    {
        return view($view)
            ->with('user', $this);
    }

    public function questGroupsView(
        string $view,
        string $questGroupModelName = null,
    ): View
    {
        return view($view)
            ->with('user', $this)
            ->with("questGroups", (
            tap(
                $this->questGroups(
                    $questGroupModelName
                )->simplePaginate(10, ['*'], 'user_questGroups')
            )->transform(
                function ($grn) {
                    return new QuestGroupDomain(
                        $this,
                        $grn->key,
                    );
                }
            )
            ));
    }

    public function currentQuestGroupsView(
        string $view,
    ): View
    {
        return view($view)
            ->with('user', $this)
            ->with("questGroups", $this->currentQuestGroups());
    }

    public function timeline(): Builder
    {
        return Timeline::query()
            ->joinSub(
                GrnKey::query()
                    ->where('grn', 'like', "grn:quest:namespace:{$this->namespace->namespaceName}:user:{$this->userId}:%"),
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

    public function questControllerView(
        string $view,
    ): View
    {
        return view($view)
            ->with('user', $this);
    }

}
