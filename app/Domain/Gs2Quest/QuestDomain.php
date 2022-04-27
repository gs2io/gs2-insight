<?php

namespace App\Domain\Gs2Quest;

use App\Domain\BaseDomain;
use App\Models\GrnKey;
use App\Models\Timeline;
use Gs2\Core\Net\Gs2RestSession;
use Gs2\Distributor\Gs2DistributorRestClient;
use Gs2\Distributor\Request\RunStampSheetExpressWithoutNamespaceRequest;
use Gs2\Quest\Gs2QuestRestClient;
use Gs2\Quest\Model\Progress;
use Gs2\Quest\Request\DeleteProgressByUserIdRequest;
use Gs2\Quest\Request\StartByUserIdRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;

class QuestDomain extends BaseDomain {

    public QuestGroupDomain $questGroup;
    public string $questModelName;

    public function __construct(
        QuestGroupDomain $questGroup,
        string     $questModelName,
    ) {
        $this->questGroup = $questGroup;
        $this->questModelName = $questModelName;
    }

    public function start(
    )
    {
        $this->gs2(
            function (Gs2RestSession $session) {
                $client = new Gs2QuestRestClient(
                    $session,
                );
                $result = $client->startByUserId(
                    (new StartByUserIdRequest())
                        ->withNamespaceName($this->questGroup->user->namespace->namespaceName)
                        ->withUserId($this->questGroup->user->userId)
                        ->withQuestGroupName($this->questGroup->questGroupModelName)
                        ->withQuestName($this->questModelName)
                );
                $stampSheet = $result->getStampSheet();
                $stampSheetEncryptionKeyId = $result->getStampSheetEncryptionKeyId();

                while (true) {
                    $result = (new Gs2DistributorRestClient(
                        $session,
                    ))->runStampSheetExpressWithoutNamespace(
                        (new RunStampSheetExpressWithoutNamespaceRequest())
                            ->withStampSheet($stampSheet)
                            ->withKeyId($stampSheetEncryptionKeyId)
                    );
                    if ($result->getSheetResult() != null) {
                        $response = json_decode($result->getSheetResult(), true);
                        if (in_array('stampSheet', array_keys($response))) {
                            $stampSheet = $response['stampSheet'];
                            $stampSheetEncryptionKeyId = $response['stampSheetEncryptionKeyId'];
                            continue;
                        }
                    }
                    break;
                }
            }
        );
    }

    public function infoView(
        string $view,
    ): View
    {
        return view($view)
            ->with("quest", $this)
            ->with("complete", $this->questGroup->complete());
    }

    public function timeline(): Builder
    {
        return Timeline::query()
            ->joinSub(
                GrnKey::query()
                    ->where('grn', '=', "grn:quest:namespace:{$this->questGroup->user->namespace->namespaceName}:user:{$this->questGroup->user->userId}:questGroupModel:{$this->questGroup->questGroupModelName}:questModel:{$this->questModelName}"),
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

}
