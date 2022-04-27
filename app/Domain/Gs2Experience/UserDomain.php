<?php

namespace App\Domain\Gs2Experience;

use App\Domain\BaseDomain;
use App\Models\Grn;
use App\Models\GrnKey;
use App\Models\Timeline;
use Gs2\Core\Net\Gs2RestSession;
use Gs2\Experience\Gs2ExperienceRestClient;
use Gs2\Experience\Request\DescribeExperienceModelsRequest;
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

    #[Pure] public function experience(
        string $experienceName,
    ): ExperienceDomain {
        return new ExperienceDomain(
            $this,
            $experienceName,
        );
    }

    public function experiences(
        string $experienceName = null,
    ): Builder {
        $entries = Grn::query()
            ->where("parent", "grn:experience:namespace:{$this->namespace->namespaceName}")
            ->where("category", "experienceModel");
        if (!is_null($experienceName)) {
            $entries->where('key', 'like', "$experienceName%");
        }
        return $entries;
    }

    public function currentExperiences(
    ): array {
        return $this->experiences()->get()->transform(
            function ($grn) {
                return new ExperienceDomain(
                    $this,
                    $grn->key,
                );
            }
        )->toArray();
    }

    public function infoView(
        string $view,
    ): View
    {
        return view($view)
            ->with("experience", $this);
    }

    public function experiencesView(
        string $view,
        string $propertyId = null,
    ): View
    {
        return view($view)
            ->with("user", $this)
            ->with("experiences", (
            tap(
                $this->experiences(
                    $propertyId,
                )
                    ->simplePaginate(10, ['*'], 'user_experiences')
            )->transform(
                function ($grn) {
                    return new ExperienceDomain(
                        $this,
                        $grn->key,
                    );
                }
            )
            ));
    }

    public function currentExperiencesView(
        string $view,
    ): View
    {
        return view($view)
            ->with('user', $this)
            ->with("experiences", $this->currentExperiences());
    }

    public function experienceControllerView(
        string $view,
    ): View
    {
        return view($view)
            ->with('user', $this);
    }

    public function timeline(): Builder
    {
        return Timeline::query()
            ->joinSub(
                GrnKey::query()
                    ->where('category', 'experienceModel')
                    ->addNestedWhereQuery(
                        GrnKey::query()
                            ->where('grn', '=', "grn:experience:namespace:{$this->namespace->namespaceName}:user:{$this->userId}")
                            ->orWhere('grn', 'like', "grn:experience:namespace:{$this->namespace->namespaceName}:user:{$this->userId}:%")
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

}
