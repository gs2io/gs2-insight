<?php

namespace App\Domain\Gs2Quest;

use App\Domain\BaseDomain;
use Gs2\Core\Net\Gs2RestSession;
use Gs2\Distributor\Gs2DistributorRestClient;
use Gs2\Distributor\Request\RunStampSheetExpressWithoutNamespaceRequest;
use Gs2\Quest\Gs2QuestRestClient;
use Gs2\Quest\Model\CompletedQuestList;
use Gs2\Quest\Request\DeleteCompletedQuestListByUserIdRequest;
use Gs2\Quest\Request\GetCompletedQuestListByUserIdRequest;
use Gs2\Quest\Request\StartByUserIdRequest;
use Illuminate\Contracts\View\View;

class CompleteDomain extends BaseDomain {

    public QuestGroupDomain $questGroup;
    public CompletedQuestList|null $completedQuestList;

    public function __construct(
        QuestGroupDomain $questGroup,
        CompletedQuestList|null $completedQuestList,
    ) {
        $this->questGroup = $questGroup;
        $this->completedQuestList = $completedQuestList;
    }

    public function reset(
    )
    {
        $this->gs2(
            function (Gs2RestSession $session) {
                $client = new Gs2QuestRestClient(
                    $session,
                );
                $client->deleteCompletedQuestListByUserId(
                    (new DeleteCompletedQuestListByUserIdRequest())
                        ->withNamespaceName($this->questGroup->user->namespace->namespaceName)
                        ->withUserId($this->questGroup->user->userId)
                        ->withQuestGroupName($this->questGroup->questGroupModelName)
                );
            }
        );
    }

    public function infoView(
        string $view,
    ): View
    {
        return view($view)
            ->with('complete', $this);
    }

    public function isCompleted(
        string $questName,
    ) {
        if ($this->completedQuestList == null) {
            return false;
        }
        return in_array($questName, $this->completedQuestList->getCompleteQuestNames());
    }
}
