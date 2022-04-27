<?php

namespace App\Domain\Gs2Mission;

use App\Domain\BaseDomain;
use App\Models\GrnKey;
use App\Models\Timeline;
use Gs2\Core\Net\Gs2RestSession;
use Gs2\Mission\Gs2MissionRestClient;
use Gs2\Mission\Model\Complete;
use Gs2\Mission\Model\Counter;
use Gs2\Mission\Request\DeleteCompleteByUserIdRequest;
use Gs2\Mission\Request\DeleteCounterByUserIdRequest;
use Gs2\Mission\Request\GetCounterByUserIdRequest;
use Gs2\Mission\Request\IncreaseCounterByUserIdRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;

class CompleteDomain extends BaseDomain {

    public MissionGroupDomain $missionGroup;
    public Complete|null $complete;

    public function __construct(
        MissionGroupDomain $missionGroup,
        Complete|null $complete = null,
    ) {
        $this->missionGroup = $missionGroup;
        $this->complete = $complete;
    }

    public function reset() {
        $this->gs2(
            function (Gs2RestSession $session) {
                $client = new Gs2MissionRestClient(
                    $session,
                );
                $client->deleteCompleteByUserId(
                    (new DeleteCompleteByUserIdRequest())
                        ->withNamespaceName($this->missionGroup->user->namespace->namespaceName)
                        ->withUserId($this->missionGroup->user->userId)
                        ->withMissionGroupName($this->missionGroup->missionGroupModelName)
                );
                return null;
            }
        );
    }

}
