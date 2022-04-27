<?php

namespace App\Domain\Gs2Experience;

use App\Domain\BaseDomain;
use App\Domain\Gs2Inventory\ItemDomain;
use App\Domain\PlayerDomain;
use App\Models\GrnKey;
use App\Models\Timeline;
use Gs2\Experience\Gs2ExperienceRestClient;
use Gs2\Experience\Model\Status;
use Gs2\Core\Net\Gs2RestSession;
use Gs2\Experience\Request\AddExperienceByUserIdRequest;
use Gs2\Experience\Request\AddRankCapByUserIdRequest;
use Gs2\Experience\Request\DeleteStatusByUserIdRequest;
use Gs2\Experience\Request\GetStatusByUserIdRequest;
use Gs2\Experience\Request\SetExperienceByUserIdRequest;
use Gs2\Experience\Request\SetRankCapByUserIdRequest;
use Gs2\Inventory\Gs2InventoryRestClient;
use Gs2\Inventory\Model\ItemSet;
use Gs2\Inventory\Request\GetItemSetByUserIdRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;

class StatusDomain extends BaseDomain {

    public ExperienceDomain $experience;
    public string $propertyId;
    public Status|null $status;

    public function __construct(
        ExperienceDomain $experience,
        string           $propertyId,
        Status|null      $status = null,
    ) {
        $this->experience = $experience;
        $this->propertyId = $propertyId;
        $this->status = $status;
    }

    public function addExperience(
        int $experienceValue,
    )
    {
        return $this->gs2(
            function (Gs2RestSession $session) use ($experienceValue) {
                $client = new Gs2ExperienceRestClient(
                    $session,
                );
                $client->addExperienceByUserId(
                    (new AddExperienceByUserIdRequest())
                        ->withNamespaceName($this->experience->user->namespace->namespaceName)
                        ->withUserId($this->experience->user->userId)
                        ->withExperienceName($this->experience->experienceModelName)
                        ->withPropertyId($this->propertyId)
                        ->withExperienceValue($experienceValue)
                );
            }
        );
    }

    public function setExperience(
        int $experienceValue,
    )
    {
        return $this->gs2(
            function (Gs2RestSession $session) use ($experienceValue) {
                $client = new Gs2ExperienceRestClient(
                    $session,
                );
                $client->setExperienceByUserId(
                    (new SetExperienceByUserIdRequest())
                        ->withNamespaceName($this->experience->user->namespace->namespaceName)
                        ->withUserId($this->experience->user->userId)
                        ->withExperienceName($this->experience->experienceModelName)
                        ->withPropertyId($this->propertyId)
                        ->withExperienceValue($experienceValue)
                );
            }
        );
    }

    public function addRankCap(
        int $rankCapValue,
    )
    {
        return $this->gs2(
            function (Gs2RestSession $session) use ($rankCapValue) {
                $client = new Gs2ExperienceRestClient(
                    $session,
                );
                $client->addRankCapByUserId(
                    (new AddRankCapByUserIdRequest())
                        ->withNamespaceName($this->experience->user->namespace->namespaceName)
                        ->withUserId($this->experience->user->userId)
                        ->withExperienceName($this->experience->experienceModelName)
                        ->withPropertyId($this->propertyId)
                        ->withRankCapValue($rankCapValue)
                );
            }
        );
    }

    public function setRankCap(
        int $rankCapValue,
    )
    {
        return $this->gs2(
            function (Gs2RestSession $session) use ($rankCapValue) {
                $client = new Gs2ExperienceRestClient(
                    $session,
                );
                $client->setRankCapByUserId(
                    (new SetRankCapByUserIdRequest())
                        ->withNamespaceName($this->experience->user->namespace->namespaceName)
                        ->withUserId($this->experience->user->userId)
                        ->withExperienceName($this->experience->experienceModelName)
                        ->withPropertyId($this->propertyId)
                        ->withRankCapValue($rankCapValue)
                );
            }
        );
    }

    public function delete()
    {
        return $this->gs2(
            function (Gs2RestSession $session) {
                $client = new Gs2ExperienceRestClient(
                    $session,
                );
                $client->deleteStatusByUserId(
                    (new DeleteStatusByUserIdRequest())
                        ->withNamespaceName($this->experience->user->namespace->namespaceName)
                        ->withUserId($this->experience->user->userId)
                        ->withExperienceName($this->experience->experienceModelName)
                        ->withPropertyId($this->propertyId)
                );
            }
        );
    }

    public function infoView(
        string $view,
    ): View
    {
        try {
            $item = $this->gs2(
                function (Gs2RestSession $session) {
                    $client = new Gs2ExperienceRestClient(
                        $session,
                    );
                    $result = $client->getStatusByUserId(
                        (new GetStatusByUserIdRequest())
                            ->withNamespaceName($this->experience->user->namespace->namespaceName)
                            ->withUserId($this->experience->user->userId)
                            ->withExperienceName($this->experience->experienceModelName)
                            ->withPropertyId($this->propertyId)
                    );
                    return $result->getItem();
                }
            );

            $status = new StatusDomain(
                $this->experience,
                $this->propertyId,
                $item
            );
        } catch (\Exception $e) {
            var_dump($e->getMessage());
            $status = $this;
        }

        return view($view)
            ->with("status", $status);
    }

    public function timeline(): Builder
    {
        return Timeline::query()
            ->joinSub(
                GrnKey::query()
                    ->where('category', 'property')
                    ->addNestedWhereQuery(
                        GrnKey::query()
                            ->where('grn', '=', "grn:experience:namespace:{$this->experience->user->namespace->namespaceName}:user:{$this->experience->user->userId}:experienceModel:{$this->experience->experienceModelName}:property:{$this->propertyId}")
                            ->orWhere('grn', 'like', "grn:experience:namespace:{$this->experience->user->namespace->namespaceName}:user:{$this->experience->user->userId}:experienceModel:{$this->experience->experienceModelName}:property:{$this->propertyId}:%")
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
            ->with("status", $this)
            ->with("timeline", $this->timeline()->simplePaginate(10, ['*'], 'user_timeline'));
    }
}
