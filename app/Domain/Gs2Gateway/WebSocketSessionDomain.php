<?php

namespace App\Domain\Gs2Gateway;

use App\Domain\BaseDomain;
use App\Models\GrnKey;
use App\Models\Timeline;
use Gs2\Core\Net\Gs2RestSession;
use Gs2\Gateway\Gs2GatewayRestClient;
use Gs2\Gateway\Model\WebSocketSession;
use Gs2\Gateway\Request\DisconnectByUserIdRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;

class WebSocketSessionDomain extends BaseDomain {

    public UserDomain $user;
    public WebSocketSession|null $webSocketSession;

    public function __construct(
        UserDomain $user,
        WebSocketSession|null $webSocketSession = null,
    ) {
        $this->user = $user;
        $this->webSocketSession = $webSocketSession;
    }

    public function disconnect() {
        $this->gs2(
            function (Gs2RestSession $session) {
                $client = new Gs2GatewayRestClient(
                    $session,
                );
                $client->disconnectByUserId(
                    (new DisconnectByUserIdRequest())
                        ->withNamespaceName($this->user->namespace->namespaceName)
                        ->withUserId($this->user->userId)
                );
            }
        );
    }

    public function timeline(): Builder
    {
        return Timeline::query()
            ->joinSub(
                GrnKey::query()
                    ->where('category', 'webSocketSession')
                    ->addNestedWhereQuery(
                        GrnKey::query()
                            ->where('grn', '=', "grn:gateway:namespace:{$this->user->namespace->namespaceName}:user:{$this->user->userId}:webSocketSession:{$this->webSocketSessionName}")
                            ->orWhere('grn', 'like', "grn:gateway:namespace:{$this->user->namespace->namespaceName}:user:{$this->user->userId}:webSocketSession:{$this->webSocketSessionName}:%")
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
