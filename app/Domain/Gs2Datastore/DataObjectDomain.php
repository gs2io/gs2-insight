<?php

namespace App\Domain\Gs2Datastore;

use App\Domain\BaseDomain;
use App\Models\GrnKey;
use App\Models\Timeline;
use Gs2\Datastore\Gs2DatastoreRestClient;
use Gs2\Datastore\Model\DataObject;
use Gs2\Core\Net\Gs2RestSession;
use Gs2\Datastore\Request\DeleteDataObjectByUserIdRequest;
use Gs2\Datastore\Request\PrepareDownloadByUserIdAndDataObjectNameRequest;
use Gs2\Datastore\Request\PrepareDownloadByUserIdRequest;
use Gs2\Inbox\Gs2InboxRestClient;
use Gs2\Inbox\Request\DeleteMessageByUserIdRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;

class DataObjectDomain extends BaseDomain {

    public UserDomain $user;
    public string $dataObjectName;
    public DataObject|null $dataObject;

    public function __construct(
        UserDomain $user,
        string $dataObjectName,
        DataObject|null $dataObject = null,
    ) {
        $this->user = $user;
        $this->dataObjectName = $dataObjectName;
        $this->dataObject = $dataObject;
    }

    public function download(): string {
        return $this->gs2(
            function (Gs2RestSession $session) {
                $client = new Gs2DatastoreRestClient(
                    $session,
                );
                $result = $client->prepareDownloadByUserIdAndDataObjectName(
                    (new PrepareDownloadByUserIdAndDataObjectNameRequest())
                        ->withNamespaceName($this->user->namespace->namespaceName)
                        ->withUserId($this->user->userId)
                        ->withDataObjectName($this->dataObjectName)
                );
                return $result->getFileUrl();
            }
        );
    }

    public function delete() {
        $this->gs2(
            function (Gs2RestSession $session) {
                $client = new Gs2DatastoreRestClient(
                    $session,
                );
                $client->deleteDataObjectByUserId(
                    (new DeleteDataObjectByUserIdRequest())
                        ->withNamespaceName($this->user->namespace->namespaceName)
                        ->withUserId($this->user->userId)
                        ->withDataObjectName($this->dataObjectName)
                );
            }
        );
    }

    public function infoView(
        string $view,
    ): View
    {
        try {
            $dataObject = $this->gs2(
                function (Gs2RestSession $session) {
                    $client = new Gs2DatastoreRestClient(
                        $session,
                    );
                    $result = $client->prepareDownloadByUserIdAndDataObjectName(
                        (new PrepareDownloadByUserIdAndDataObjectNameRequest())
                            ->withNamespaceName($this->user->namespace->namespaceName)
                            ->withUserId($this->user->userId)
                            ->withDataObjectName($this->dataObjectName)
                    );
                    return new DataObjectDomain(
                        $this->user,
                        $this->dataObjectName,
                        $result->getItem(),
                    );
                }
            );
        } catch (\Exception $e) {
            var_dump($e->getMessage());
            $dataObject = $this;
        }
        return view($view)
            ->with("dataObject", $dataObject);
    }

    public function timeline(): Builder
    {
        return Timeline::query()
            ->joinSub(
                GrnKey::query()
                    ->where('category', 'dataObject')
                    ->addNestedWhereQuery(
                        GrnKey::query()
                            ->where('grn', '=', "grn:datastore:namespace:{$this->user->namespace->namespaceName}:user:{$this->user->userId}:dataObject:{$this->dataObjectName}")
                            ->orWhere('grn', 'like', "grn:datastore:namespace:{$this->user->namespace->namespaceName}:user:{$this->user->userId}:dataObject:{$this->dataObjectName}:%")
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
            ->with("timeline", $this->timeline()->simplePaginate(3, ['*'], 'user_timeline'));
    }

}
