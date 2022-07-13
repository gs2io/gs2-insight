<?php

namespace App\Domain\Gs2Gateway;

use App\Domain\BaseDomain;
use App\Domain\PlayerDomain;
use App\Models\Grn;
use App\Models\GrnKey;
use App\Models\Timeline;
use Gs2\Core\Net\Gs2RestSession;
use Gs2\Gateway\Gs2GatewayRestClient;
use Gs2\Gateway\Model\WebSocketSession;
use Gs2\Gateway\Request\DescribeWebSocketSessionsByUserIdRequest;
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

    #[Pure] public function webSocketSession(
    ): WebSocketSessionDomain {
        return new WebSocketSessionDomain(
            $this,
        );
    }

    public function webSocketSessions(
    ): Builder {
        $webSocketSessions = Grn::query()
            ->where("parent", "grn:gateway:namespace:{$this->namespace->namespaceName}:user:{$this->userId}")
            ->where("category", "webSocketSession");
        return $webSocketSessions;
    }

    public function currentWebSocketSessions(
    ): array {
        try {
            return $this->gs2(
                function (Gs2RestSession $session) {
                    $client = new Gs2GatewayRestClient(
                        $session,
                    );
                    $result = $client->describeWebSocketSessionsByUserId(
                        (new DescribeWebSocketSessionsByUserIdRequest())
                            ->withNamespaceName($this->namespace->namespaceName)
                            ->withUserId($this->userId)
                            ->withLimit(1000)
                    );
                    return array_map(
                        function (WebSocketSession $item) {
                            return new WebSocketSessionDomain(
                                $this,
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

    public function webSocketSessionsView(
        string $view,
        string $webSocketSessionName = null,
    ): View
    {
        return view($view)
            ->with("user", $this)
            ->with("webSocketSessions", (
            tap(
                $this->webSocketSessions(
                    $webSocketSessionName,
                )
                    ->simplePaginate(10, ['*'], 'user_webSocketSessions')
            )->transform(
                function ($grn) {
                    return new WebSocketSessionDomain(
                        $this,
                        $grn->key,
                    );
                }
            )
            ));
    }

    public function currentWebSocketSessionsView(
        string $view,
    ): View
    {
        return view($view)
            ->with('user', $this)
            ->with("webSocketSessions", $this->currentWebSocketSessions());
    }

    public function timeline(): Builder
    {
        return Timeline::query()
            ->joinSub(
                GrnKey::query()
                    ->where('category', 'webSocketSession')
                    ->addNestedWhereQuery(
                        GrnKey::query()
                            ->where('grn', '=', "grn:gateway:namespace:{$this->namespace->namespaceName}:user:{$this->userId}")
                            ->orWhere('grn', 'like', "grn:gateway:namespace:{$this->namespace->namespaceName}:user:{$this->userId}:%")
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

    public function webSocketSessionControllerView(
        string $view,
    ): View
    {
        return view($view)
            ->with('user', $this);
    }

}
