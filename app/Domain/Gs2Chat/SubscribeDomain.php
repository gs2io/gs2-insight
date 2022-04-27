<?php

namespace App\Domain\Gs2Chat;

use App\Domain\BaseDomain;
use Gs2\Chat\Gs2ChatRestClient;
use Gs2\Chat\Model\Subscribe;
use Gs2\Chat\Request\GetSubscribeByUserIdRequest;
use Gs2\Chat\Request\SubscribeByUserIdRequest;
use Gs2\Chat\Request\UnsubscribeByUserIdRequest;
use Gs2\Core\Net\Gs2RestSession;
use Illuminate\Contracts\View\View;

class SubscribeDomain extends BaseDomain {

    public UserDomain $user;
    public string $roomName;
    public Subscribe|null $subscribe;

    public function __construct(
        UserDomain $user,
        string $roomName,
        Subscribe|null $subscribe = null,
    ) {
        $this->user = $user;
        $this->roomName = $roomName;
        $this->subscribe = $subscribe;
    }

    public function add(): Subscribe
    {
        return $this->gs2(
            function (Gs2RestSession $session) {
                $client = new Gs2ChatRestClient(
                    $session,
                );
                $result = $client->subscribeByUserId(
                    (new SubscribeByUserIdRequest())
                        ->withNamespaceName($this->user->namespace->namespaceName)
                        ->withUserId($this->user->userId)
                        ->withRoomName($this->roomName)
                );
                return $result->getItem();
            }
        );
    }

    public function delete(): Subscribe
    {
        return $this->gs2(
            function (Gs2RestSession $session) {
                $client = new Gs2ChatRestClient(
                    $session,
                );
                $result = $client->unsubscribeByUserId(
                    (new UnsubscribeByUserIdRequest())
                        ->withNamespaceName($this->user->namespace->namespaceName)
                        ->withUserId($this->user->userId)
                        ->withRoomName($this->roomName)
                );
                return $result->getItem();
            }
        );
    }

    public function infoView(
        string $view,
    ): View
    {
        try {
            $item = $this->gs2(
                function (Gs2RestSession $session) {
                    $client = new Gs2ChatRestClient(
                        $session,
                    );
                    $result = $client->getSubscribeByUserId(
                        (new GetSubscribeByUserIdRequest())
                            ->withNamespaceName($this->user->namespace->namespaceName)
                            ->withUserId($this->user->userId)
                            ->withRoomName($this->roomName)
                    );
                    return $result->getItem();
                }
            );
            $subscribe = new SubscribeDomain(
                $this->user,
                $this->roomName,
                $item,
            );
        } catch (\Exception $e) {
            var_dump($e->getMessage());
            $subscribe = $this;
        }

        return view($view)
            ->with("subscribe", $subscribe);
    }

}
