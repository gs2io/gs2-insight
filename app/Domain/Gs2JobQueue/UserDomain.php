<?php

namespace App\Domain\Gs2JobQueue;

use App\Domain\BaseDomain;
use App\Models\Grn;
use App\Models\GrnKey;
use App\Models\Timeline;
use Gs2\Core\Net\Gs2RestSession;
use Gs2\Distributor\Gs2DistributorRestClient;
use Gs2\Distributor\Request\RunStampSheetExpressWithoutNamespaceRequest;
use Gs2\JobQueue\Gs2JobQueueRestClient;
use Gs2\JobQueue\Model\Job;
use Gs2\JobQueue\Request\DescribeJobsByUserIdRequest;
use Gs2\JobQueue\Request\RunByUserIdRequest;
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

    public function run() {
        $this->gs2(
            function (Gs2RestSession $session) {
                $client = new Gs2JobQueueRestClient(
                    $session,
                );
                $result = $client->runByUserId(
                    (new RunByUserIdRequest())
                        ->withNamespaceName($this->namespace->namespaceName)
                        ->withUserId($this->userId)
                );
                if ($result->getResult() != null && $result->getResult()->getStatusCode() == 200) {
                    $response = json_decode($result->getResult()->getResult(), true);
                    if (in_array('stampSheet', array_keys($response))) {
                        $stampSheet = $response['stampSheet'];
                        $stampSheetEncryptionKeyId = $response['stampSheetEncryptionKeyId'];

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
                }
                return null;
            }
        );
    }

    #[Pure] public function job(
        string $type,
    ): JobDomain {
        return new JobDomain(
            $this,
            $type
        );
    }

    public function jobs(
        string $type = null,
    ): Builder {
        $types = Grn::query()
            ->where("parent", "grn:jobQueue:namespace:{$this->namespace->namespaceName}:user:{$this->userId}")
            ->where("category", "job");
        if (!is_null($type)) {
            $types->where('key', '=', $type);
        }
        return $types;
    }

    public function currentJobs(
    ): array {
        try {
            return $this->gs2(
                function (Gs2RestSession $session) {
                    $client = new Gs2JobQueueRestClient(
                        $session,
                    );
                    $result = $client->describeJobsByUserId(
                        (new DescribeJobsByUserIdRequest())
                            ->withNamespaceName($this->namespace->namespaceName)
                            ->withUserId($this->userId)
                    );
                    return array_map(
                        function (Job $item) {
                            return new JobDomain(
                                $this,
                                $item->getName(),
                                $item,
                            );
                        }
                        , $result->getItems());
                }
            );
        } catch (\Exception $e) {
            var_dump($e->getMessage());
        }
        return [];
    }

    public function infoView(
        string $view,
    ): View
    {
        return view($view)
            ->with('user', $this);
    }

    public function jobsView(
        string $view,
        string $type = null,
    ): View
    {
        return view($view)
            ->with('user', $this)
            ->with("jobs", (
            tap(
                $this->jobs(
                    $type
                )->simplePaginate(10, ['*'], 'user_jobs')
            )->transform(
                function ($grn) {
                    return new JobDomain(
                        $this,
                        $grn->key,
                    );
                }
            )
            ));
    }

    public function currentJobsView(
        string $view,
    ): View
    {
        return view($view)
            ->with('user', $this)
            ->with("jobs", $this->currentJobs());
    }

    public function timeline(): Builder
    {
        return Timeline::query()
            ->joinSub(
                GrnKey::query()
                    ->where('category', 'job')
                    ->addNestedWhereQuery(
                        GrnKey::query()
                            ->where('grn', '=', "grn:jobQueue:namespace:{$this->namespace->namespaceName}:user:{$this->userId}")
                            ->orWhere('grn', 'like', "grn:jobQueue:namespace:{$this->namespace->namespaceName}:user:{$this->userId}:%")
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

    public function jobControllerView(
        string $view,
    ): View
    {
        return view($view)
            ->with('user', $this);
    }
}
