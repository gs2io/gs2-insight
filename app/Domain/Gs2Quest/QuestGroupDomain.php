<?php

namespace App\Domain\Gs2Quest;

use App\Domain\BaseDomain;
use App\Models\Grn;
use App\Models\GrnKey;
use App\Models\Timeline;
use Gs2\Core\Net\Gs2RestSession;
use Gs2\Quest\Gs2QuestRestClient;
use Gs2\Quest\Request\DescribeQuestModelsRequest;
use Gs2\Quest\Request\GetCompletedQuestListByUserIdRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use JetBrains\PhpStorm\Pure;

class QuestGroupDomain extends BaseDomain {

    public UserDomain $user;
    public string $questGroupModelName;

    public function __construct(
        UserDomain $user,
        string     $questGroupModelName,
    ) {
        $this->user = $user;
        $this->questGroupModelName = $questGroupModelName;
    }

    #[Pure] public function quest(
        string $questModelName,
    ): QuestDomain {
        return new QuestDomain(
            $this,
            $questModelName
        );
    }

    public function currentQuests(
    ): array {
        return $this->quests()->get()->transform(
            function ($grn) {
                return new QuestDomain(
                    $this,
                    $grn->key,
                );
            }
        )->toArray();
    }

    public function quests(
        string $questModelName = null,
    ): Builder {
        $types = Grn::query()
            ->where("parent", "grn:quest:namespace:{$this->user->namespace->namespaceName}:questGroupModel:{$this->questGroupModelName}")
            ->where("category", "questModel");
        if (!is_null($questModelName)) {
            $types->where('key', '=', $questModelName);
        }
        return $types;
    }

    #[Pure] public function complete(): CompleteDomain {
        try {
            return $this->gs2(
                function (Gs2RestSession $session) {
                    $client = new Gs2QuestRestClient(
                        $session,
                    );
                    $result = $client->getCompletedQuestListByUserId(
                        (new GetCompletedQuestListByUserIdRequest())
                            ->withNamespaceName($this->user->namespace->namespaceName)
                            ->withUserId($this->user->userId)
                            ->withQuestGroupName($this->questGroupModelName)
                    );
                    return new CompleteDomain(
                        $this,
                        $result->getItem(),
                    );
                }
            );
        } catch (\Exception) {
            return new CompleteDomain(
                $this,
                null,
            );
        }
    }

    public function infoView(
        string $view,
    ): View
    {
        return view($view)
            ->with("questGroup", $this);
    }

    public function questsView(
        string $view,
        string $questModelName = null,
    ): View
    {
        return view($view)
            ->with('questGroup', $this)
            ->with("quests", (
            tap(
                $this->quests(
                    $questModelName
                )->simplePaginate(10, ['*'], 'user_quests')
            )->transform(
                function ($grn) {
                    return new QuestDomain(
                        $this,
                        $grn->key,
                    );
                }
            )
            ));
    }

    public function currentQuestsView(
        string $view,
    ): View
    {
        return view($view)
            ->with('questGroup', $this)
            ->with("quests", $this->currentQuests())
            ->with("complete", $this->complete());
    }

    public function timeline(): Builder
    {
        return Timeline::query()
            ->joinSub(
                GrnKey::query()
                    ->where('grn', 'like', "grn:quest:namespace:{$this->user->namespace->namespaceName}:user:{$this->user->userId}:questGroupModel:{$this->questGroupModelName}%"),
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
            ->with("questGroup", $this)
            ->with("timeline", $this->timeline()->simplePaginate(10, ['*'], 'questGroup_timeline'));
    }

    public function questControllerView(
        string $view,
    ): View
    {
        return view($view)
            ->with('questGroup', $this);
    }
}
