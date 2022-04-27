<?php

namespace App\Domain\Gs2Datastore;

use App\Domain\BaseDomain;
use App\Domain\PlayerDomain;
use App\Models\Grn;
use App\Models\GrnKey;
use App\Models\Timeline;
use Gs2\Core\Net\Gs2RestSession;
use Gs2\Datastore\Gs2DatastoreRestClient;
use Gs2\Datastore\Model\DataObject;
use Gs2\Datastore\Request\DescribeDataObjectsByUserIdRequest;
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

    #[Pure] public function dataObject(
        string $dataObjectName,
    ): DataObjectDomain {
        return new DataObjectDomain(
            $this,
            $dataObjectName
        );
    }

    public function dataObjects(
        string $dataObjectName = null,
    ): Builder {
        $dataObjects = Grn::query()
            ->where("parent", "grn:datastore:namespace:{$this->namespace->namespaceName}:user:{$this->userId}")
            ->where("category", "dataObject");
        if (!is_null($dataObjectName)) {
            $dataObjects->where('key', 'like', "$dataObjectName%");
        }
        return $dataObjects;
    }

    public function currentDataObjects(
    ): array {
        try {
            return $this->gs2(
                function (Gs2RestSession $session) {
                    $client = new Gs2DatastoreRestClient(
                        $session,
                    );
                    $result = $client->describeDataObjectsByUserId(
                        (new DescribeDataObjectsByUserIdRequest())
                            ->withNamespaceName($this->namespace->namespaceName)
                            ->withUserId($this->userId)
                            ->withLimit(1000)
                    );
                    return array_map(
                        function (DataObject $item) {
                            return new DataObjectDomain(
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

    public function dataObjectsView(
        string $view,
        string $dataObjectName = null,
    ): View
    {
        return view($view)
            ->with("user", $this)
            ->with("dataObjects", (
            tap(
                $this->dataObjects(
                    $dataObjectName,
                )
                    ->simplePaginate(10, ['*'], 'user_dataObjects')
            )->transform(
                function ($grn) {
                    return new DataObjectDomain(
                        $this,
                        $grn->key,
                    );
                }
            )
            ));
    }

    public function currentDataObjectsView(
        string $view,
    ): View
    {
        return view($view)
            ->with('user', $this)
            ->with("dataObjects", $this->currentDataObjects());
    }

    public function timeline(): Builder
    {
        return Timeline::query()
            ->joinSub(
                GrnKey::query()
                    ->where('category', 'dataObject')
                    ->addNestedWhereQuery(
                        GrnKey::query()
                            ->where('grn', '=', "grn:datastore:namespace:{$this->namespace->namespaceName}:user:{$this->userId}")
                            ->orWhere('grn', 'like', "grn:datastore:namespace:{$this->namespace->namespaceName}:user:{$this->userId}:%")
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

    public function dataObjectControllerView(
        string $view,
    ): View
    {
        return view($view)
            ->with('user', $this);
    }

}
