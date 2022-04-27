<?php

namespace App\Domain\Gs2Mission;

use App\Domain\BaseDomain;
use App\Models\GrnKey;
use App\Models\Timeline;
use Gs2\Core\Net\Gs2RestSession;
use Gs2\Distributor\Gs2DistributorRestClient;
use Gs2\Distributor\Request\RunStampSheetExpressWithoutNamespaceRequest;
use Gs2\Mission\Gs2MissionRestClient;
use Gs2\Mission\Model\Complete;
use Gs2\Mission\Request\CompleteByUserIdRequest;
use Gs2\Mission\Request\GetCompleteByUserIdRequest;
use Gs2\Mission\Request\ReceiveByUserIdRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;

class MissionTaskDomain extends BaseDomain {

    public MissionGroupDomain $missionGroup;
    public string $missionTaskModelName;
    public Complete|null $complete;

    public function __construct(
        MissionGroupDomain $missionGroup,
        string     $missionTaskModelName,
        Complete|null $complete = null,
    ) {
        $this->missionGroup = $missionGroup;
        $this->missionTaskModelName = $missionTaskModelName;
        $this->complete = $complete;
    }

    public function receive() {
        return $this->gs2(
            function (Gs2RestSession $session) {
                $client = new Gs2MissionRestClient(
                    $session,
                );
                $result = $client->completeByUserId(
                    (new CompleteByUserIdRequest())
                        ->withNamespaceName($this->missionGroup->user->namespace->namespaceName)
                        ->withUserId($this->missionGroup->user->userId)
                        ->withMissionGroupName($this->missionGroup->missionGroupModelName)
                        ->withMissionTaskName($this->missionTaskModelName)
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

    public function isCompleted() {
        return in_array($this->missionTaskModelName, $this->complete->getCompletedMissionTaskNames());
    }

    public function isReceived() {
        return in_array($this->missionTaskModelName, $this->complete->getReceivedMissionTaskNames());
    }

    public function infoView(
        string $view,
    ): View
    {
        try {
            $item = $this->gs2(
                function (Gs2RestSession $session) {
                    $client = new Gs2MissionRestClient(
                        $session,
                    );
                    $result = $client->getCompleteByUserId(
                        (new GetCompleteByUserIdRequest())
                            ->withNamespaceName($this->missionGroup->user->namespace->namespaceName)
                            ->withUserId($this->missionGroup->user->userId)
                            ->withMissionGroupName($this->missionGroup->missionGroupModelName)
                    );
                    return $result->getItem();
                }
            );

            $missionTask = new MissionTaskDomain(
                $this->missionGroup,
                $this->missionTaskModelName,
                $item,
            );
        } catch (\Exception $e) {
            var_dump($e->getMessage());
            $missionTask = $this;
        }

        return view($view)
            ->with("missionTask", $missionTask);
    }

    public function timeline(): Builder
    {
        return Timeline::query()
            ->joinSub(
                GrnKey::query()
                    ->where('category', 'missionTaskModel')
                    ->addNestedWhereQuery(
                        GrnKey::query()
                            ->where('grn', '=', "grn:mission:namespace:{$this->missionGroup->user->namespace->namespaceName}:user:{$this->missionGroup->user->userId}:missionGroupModel:{$this->missionGroup->missionGroupModelName}:missionTaskModel:{$this->missionTaskModelName}")
                            ->orWhere('grn', 'like', "grn:mission:namespace:{$this->missionGroup->user->namespace->namespaceName}:user:{$this->missionGroup->user->userId}:missionGroupModel:{$this->missionGroup->missionGroupModelName}:missionTaskModel:{$this->missionTaskModelName}:%")
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
            ->with("missionTask", $this)
            ->with("timeline", $this->timeline()->simplePaginate(10, ['*'], 'user_timeline'));
    }

}
