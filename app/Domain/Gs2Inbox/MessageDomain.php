<?php

namespace App\Domain\Gs2Inbox;

use App\Domain\BaseDomain;
use App\Models\GrnKey;
use App\Models\Timeline;
use Gs2\Core\Net\Gs2RestSession;
use Gs2\Distributor\Gs2DistributorRestClient;
use Gs2\Distributor\Request\RunStampSheetExpressWithoutNamespaceRequest;
use Gs2\Inbox\Gs2InboxRestClient;
use Gs2\Inbox\Model\Message;
use Gs2\Inbox\Request\DeleteMessageByUserIdRequest;
use Gs2\Inbox\Request\ReadMessageByUserIdRequest;
use Gs2\Stamina\Gs2StaminaRestClient;
use Gs2\Stamina\Request\ConsumeStaminaByUserIdRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;

class MessageDomain extends BaseDomain {

    public UserDomain $user;
    public string $messageName;
    public Message|null $message;

    public function __construct(
        UserDomain $user,
        string $messageName,
        Message|null $message = null,
    ) {
        $this->user = $user;
        $this->messageName = $messageName;
        $this->message = $message;
    }

    public function read() {
        $this->gs2(
            function (Gs2RestSession $session) {
                $client = new Gs2InboxRestClient(
                    $session,
                );
                $result = $client->readMessageByUserId(
                    (new ReadMessageByUserIdRequest())
                        ->withNamespaceName($this->user->namespace->namespaceName)
                        ->withUserId($this->user->userId)
                        ->withMessageName($this->messageName)
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

    public function delete() {
        $this->gs2(
            function (Gs2RestSession $session) {
                $client = new Gs2InboxRestClient(
                    $session,
                );
                $client->deleteMessageByUserId(
                    (new DeleteMessageByUserIdRequest())
                        ->withNamespaceName($this->user->namespace->namespaceName)
                        ->withUserId($this->user->userId)
                        ->withMessageName($this->messageName)
                );
            }
        );
    }

    public function timeline(): Builder
    {
        return Timeline::query()
            ->joinSub(
                GrnKey::query()
                    ->where('category', 'message')
                    ->addNestedWhereQuery(
                        GrnKey::query()
                            ->where('grn', '=', "grn:inbox:namespace:{$this->user->namespace->namespaceName}:user:{$this->user->userId}:message:{$this->messageName}")
                            ->orWhere('grn', 'like', "grn:inbox:namespace:{$this->user->namespace->namespaceName}:user:{$this->user->userId}:message:{$this->messageName}:%")
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

    public function infoView(
        string $view,
    ): View
    {
        return view($view)
            ->with("message", $this);
    }

    public function timelineView(
        string $view,
    ): View
    {
        return view($view)
            ->with("message", $this)
            ->with("timeline", $this->timeline()->simplePaginate(10, ['*'], 'user_timeline'));
    }

}
