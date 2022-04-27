<?php

namespace App\Domain\Gs2Experience;

use App\Domain\BaseDomain;
use App\Models\Grn;
use App\Models\GrnKey;
use App\Models\Timeline;
use Gs2\Core\Net\Gs2RestSession;
use Gs2\Experience\Gs2ExperienceRestClient;
use Gs2\Experience\Model\Status;
use Gs2\Experience\Request\AddExperienceByUserIdRequest;
use Gs2\Experience\Request\DescribeStatusesByUserIdRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use JetBrains\PhpStorm\Pure;

class ExperienceDomain extends BaseDomain {

    public UserDomain $user;
    public string $experienceModelName;

    public function __construct(
        UserDomain $user,
        string     $experienceModelName,
    ) {
        $this->user = $user;
        $this->experienceModelName = $experienceModelName;
    }

    public function infoView(
        string $view,
    ): View
    {
        return view($view)
            ->with("experienceModel", $this);
    }

    #[Pure] public function status(
        string $propertyId = null,
    ): StatusDomain {
        return new StatusDomain(
            $this,
            $propertyId,
        );
    }

    public function statuses(
        string $propertyId = null,
    ): Builder {
        $entries = Grn::query()
            ->where("parent", "grn:experience:namespace:{$this->user->namespace->namespaceName}:user:{$this->user->userId}:experienceModel:{$this->experienceModelName}")
            ->where("category", "status");
        if (!is_null($propertyId)) {
            $entries->where('key', 'like', "$propertyId%");
        }
        return $entries;
    }

    public function currentStatuses(
    ): array {
        try {
            $items = $this->gs2(
                function (Gs2RestSession $session) {
                    $client = new Gs2ExperienceRestClient(
                        $session,
                    );
                    $result = $client->describeStatusesByUserId(
                        (new DescribeStatusesByUserIdRequest())
                            ->withNamespaceName($this->user->namespace->namespaceName)
                            ->withUserId($this->user->userId)
                            ->withExperienceName($this->experienceModelName)
                    );
                    return $result->getItems();
                }
            );

            return array_map(
                function (Status $item) {
                    return new StatusDomain(
                        $this,
                        $item->getPropertyId(),
                        $item,
                    );
                }
            , $items);
        } catch (\Exception $e) {
            var_dump($e->getMessage());
            return [];
        }
    }

    public function statusesView(
        string $view,
        string $propertyId = null,
    ): View
    {
        return view($view)
            ->with("user", $this)
            ->with("statuses", (
            tap(
                $this->statuses(
                    $propertyId,
                )
                    ->simplePaginate(10, ['*'], 'user_statuses')
            )->transform(
                function ($grn) {
                    return new StatusDomain(
                        $this,
                        $grn->key,
                    );
                }
            )
            ));
    }

    public function currentStatusesView(
        string $view,
    ): View
    {
        return view($view)
            ->with('user', $this)
            ->with("statuses", $this->currentStatuses());
    }

    public function timeline(): Builder
    {
        return Timeline::query()
            ->joinSub(
                GrnKey::query()
                    ->where('category', 'property')
                    ->addNestedWhereQuery(
                        GrnKey::query()
                            ->where('grn', '=', "grn:experience:namespace:{$this->user->namespace->namespaceName}:user:{$this->user->userId}:experienceModel:{$this->experienceModelName}")
                            ->orWhere('grn', 'like', "grn:experience:namespace:{$this->user->namespace->namespaceName}:user:{$this->user->userId}:experienceModel:{$this->experienceModelName}:%")
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
            ->with("experience", $this)
            ->with("timeline", $this->timeline()->simplePaginate(10, ['*'], 'user_timeline'));
    }

    public function statusControllerView(
        string $view,
    ): View
    {
        return view($view)
            ->with('experience', $this);
    }
}
