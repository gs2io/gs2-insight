<?php

namespace App\Domain\Gs2Friend;

use App\Domain\BaseDomain;
use Gs2\Core\Net\Gs2RestSession;
use Gs2\Friend\Gs2FriendRestClient;
use Gs2\Friend\Model\FriendRequest;
use Gs2\Friend\Request\AcceptRequestByUserIdRequest;
use Gs2\Friend\Request\DeleteFriendByUserIdRequest;
use Gs2\Friend\Request\DeleteRequestByUserIdRequest;
use Gs2\Friend\Request\RejectRequestByUserIdRequest;
use Gs2\Friend\Request\SendRequestByUserIdRequest;

class ReceiveRequestDomain extends BaseDomain {

    public UserDomain $user;
    public string $targetUserId;
    public FriendRequest|null $friendRequest;

    public function __construct(
        UserDomain $user,
        string $targetUserId,
        FriendRequest|null $friendRequest = null,
    ) {
        $this->user = $user;
        $this->targetUserId = $targetUserId;
        $this->friendRequest = $friendRequest;
    }

    public function accept(): FriendRequest
    {
        return $this->gs2(
            function (Gs2RestSession $session) {
                $client = new Gs2FriendRestClient(
                    $session,
                );
                $result = $client->acceptRequestByUserId(
                    (new AcceptRequestByUserIdRequest())
                        ->withNamespaceName($this->user->namespace->namespaceName)
                        ->withUserId($this->user->userId)
                        ->withFromUserId($this->targetUserId)
                );
                return $result->getItem();
            }
        );
    }

    public function reject(): FriendRequest
    {
        return $this->gs2(
            function (Gs2RestSession $session) {
                $client = new Gs2FriendRestClient(
                    $session,
                );
                $result = $client->rejectRequestByUserId(
                    (new RejectRequestByUserIdRequest())
                        ->withNamespaceName($this->user->namespace->namespaceName)
                        ->withUserId($this->user->userId)
                        ->withFromUserId($this->targetUserId)
                );
                return $result->getItem();
            }
        );
    }

}
