<?php

namespace App\Domain\Gs2JobQueue;

use App\Domain\BaseDomain;
use App\Models\GrnKey;
use App\Models\Timeline;
use Gs2\JobQueue\Model\Job;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;

class JobDomain extends BaseDomain {

    public UserDomain $user;
    public string $jobName;
    public Job|null $job;

    public function __construct(
        UserDomain $user,
        string $jobName,
        Job|null $job = null,
    ) {
        $this->user = $user;
        $this->jobName = $jobName;
        $this->job = $job;
    }

    public function infoView(
        string $view,
    ): View
    {
        return view($view)
            ->with('takeOver', $this);
    }

    public function timeline(): Builder
    {
        return Timeline::query()
            ->joinSub(
                GrnKey::query()
                    ->where('category', 'job')
                    ->addNestedWhereQuery(
                        GrnKey::query()
                            ->where('grn', '=', "grn:jobQueue:namespace:{$this->user->namespace->namespaceName}:user:{$this->user->userId}:job:{$this->jobName}")
                            ->orWhere('grn', 'like', "grn:jobQueue:namespace:{$this->user->namespace->namespaceName}:user:{$this->user->userId}:job:{$this->jobName}:%")
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
