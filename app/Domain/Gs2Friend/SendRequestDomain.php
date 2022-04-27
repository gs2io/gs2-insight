<?php

namespace App\Domain\Gs2Friend;

use App\Domain\BaseDomain;
use Gs2\Core\Net\Gs2RestSession;
use Gs2\Friend\Gs2FriendRestClient;
use Gs2\Friend\Model\FriendRequest;
use Gs2\Friend\Request\DeleteFriendByUserIdRequest;
use Gs2\Friend\Request\DeleteRequestByUserIdRequest;
use Gs2\Friend\Request\SendRequestByUserIdRequest;

class SendRequestDomain extends BaseDomain {

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

    public function send(): FriendRequest
    {
        return $this->gs2(
            function (Gs2RestSession $session) {
                $client = new Gs2FriendRestClient(
                    $session,
                );
                $result = $client->sendRequestByUserId(
                    (new SendRequestByUserIdRequest())
                        ->withNamespaceName($this->user->namespace->namespaceName)
                        ->withUserId($this->user->userId)
                        ->withTargetUserId($this->targetUserId)
                );
                return $result->getItem();
            }
        );
    }

    public function delete(): FriendRequest
    {
        return $this->gs2(
            function (Gs2RestSession $session) {
                $client = new Gs2FriendRestClient(
                    $session,
                );
                $result = $client->deleteRequestByUserId(
                    (new DeleteRequestByUserIdRequest())
                        ->withNamespaceName($this->user->namespace->namespaceName)
                        ->withUserId($this->user->userId)
                        ->withTargetUserId($this->targetUserId)
                );
                return $result->getItem();
            }
        );
    }

}
