<?php

namespace App\Domain\Gs2Inbox;

use App\Domain\BaseDomain;
use App\Models\Grn;
use App\Models\GrnKey;
use App\Models\Timeline;
use Gs2\Core\Net\Gs2RestSession;
use Gs2\Inbox\Gs2InboxRestClient;
use Gs2\Inbox\Model\Message;
use Gs2\Inbox\Request\DescribeMessagesByUserIdRequest;
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

    #[Pure] public function message(
        string $messageName,
    ): MessageDomain {
        return new MessageDomain(
            $this,
            $messageName
        );
    }

    public function messages(
        string $messageName = null,
    ): Builder {
        $messages = Grn::query()
            ->where("parent", "grn:inbox:namespace:{$this->namespace->namespaceName}:user:{$this->userId}")
            ->where("category", "message");
        if (!is_null($messageName)) {
            $messages->where('key', 'like', "$messageName%");
        }
        return $messages;
    }

    public function currentMessages(
    ): array {
        try {
            return $this->gs2(
                function (Gs2RestSession $session) {
                    $client = new Gs2InboxRestClient(
                        $session,
                    );
                    $result = $client->describeMessagesByUserId(
                        (new DescribeMessagesByUserIdRequest())
                            ->withNamespaceName($this->namespace->namespaceName)
                            ->withUserId($this->userId)
                    );
                    return array_map(
                        function (Message $item) {
                            return new MessageDomain(
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

    public function messagesView(
        string $view,
        string $messageName = null,
    ): View
    {
        return view($view)
            ->with("user", $this)
            ->with("messages", (
            tap(
                $this->messages(
                    $messageName,
                )
                    ->simplePaginate(10, ['*'], 'user_messages')
            )->transform(
                function ($grn) {
                    return new MessageDomain(
                        $this,
                        $grn->key,
                    );
                }
            )
            ));
    }

    public function currentMessagesView(
        string $view,
    ): View
    {
        return view($view)
            ->with('user', $this)
            ->with("messages", $this->currentMessages());
    }

    public function timeline(): Builder
    {
        return Timeline::query()
            ->joinSub(
                GrnKey::query()
                    ->where('category', 'message')
                    ->addNestedWhereQuery(
                        GrnKey::query()
                            ->where('grn', '=', "grn:inbox:namespace:{$this->namespace->namespaceName}:user:{$this->userId}")
                            ->orWhere('grn', 'like', "grn:inbox:namespace:{$this->namespace->namespaceName}:user:{$this->userId}:%")
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

    public function messageControllerView(
        string $view,
    ): View
    {
        return view($view)
            ->with('user', $this);
    }

}
