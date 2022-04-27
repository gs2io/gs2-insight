<?php

namespace App\Domain\Gs2Account;

use App\Domain\BaseDomain;
use App\Domain\PlayerDomain;
use App\Models\GrnKey;
use App\Models\Timeline;
use Gs2\Account\Gs2AccountRestClient;
use Gs2\Account\Model\TakeOver;
use Gs2\Account\Request\CreateTakeOverByUserIdRequest;
use Gs2\Account\Request\DeleteTakeOverByUserIdentifierRequest;
use Gs2\Account\Request\DescribeTakeOversByUserIdRequest;
use Gs2\Account\Request\GetTakeOverByUserIdRequest;
use Gs2\Core\Net\Gs2RestSession;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class TakeOverDomain extends BaseDomain {

    public UserDomain $user;
    public int $type;
    public TakeOver|null $takeOver;

    public function __construct(
        UserDomain $user,
        int $type,
        TakeOver $takeOver = null,
    ) {
        $this->user = $user;
        $this->type = $type;
        $this->takeOver = $takeOver;
    }

    public function add(
        string $userIdentifier,
        string $password,
    ): TakeOverDomain
    {
        return $this->gs2(
            function (Gs2RestSession $session) use ($userIdentifier, $password) {
                $client = new Gs2AccountRestClient(
                    $session,
                );
                $result = $client->createTakeOverByUserId(
                    (new CreateTakeOverByUserIdRequest())
                        ->withNamespaceName($this->user->namespace->namespaceName)
                        ->withUserId($this->user->userId)
                        ->withType($this->type)
                        ->withUserIdentifier($userIdentifier)
                        ->withPassword($password)
                );
                return new TakeOverDomain(
                    $this->user,
                    $this->type,
                    $result->getItem(),
                );
            }
        );
    }

    public function delete(
        string $userIdentifier,
    ): TakeOver
    {
        return $this->gs2(
            function (Gs2RestSession $session) use ($userIdentifier) {
                $client = new Gs2AccountRestClient(
                    $session,
                );
                $result = $client->deleteTakeOverByUserIdentifier(
                    (new DeleteTakeOverByUserIdentifierRequest())
                        ->withNamespaceName($this->user->namespace->namespaceName)
                        ->withType($this->type)
                        ->withUserIdentifier($userIdentifier)
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
                    $client = new Gs2AccountRestClient(
                        $session,
                    );
                    $result = $client->getTakeOverByUserId(
                        (new GetTakeOverByUserIdRequest())
                            ->withNamespaceName($this->user->namespace->namespaceName)
                            ->withUserId($this->user->userId)
                            ->withType($this->type)
                    );
                    return $result->getItem();
                }
            );
            $takeOver = new TakeOverDomain(
                $this->user,
                $this->type,
                $item,
            );
        } catch (\Exception $e) {
            var_dump($e->getMessage());
            $takeOver = $this;
        }

        return view($view)
            ->with('takeOver', $takeOver);
    }

    public function timeline(): Builder
    {
        return Timeline::query()
            ->joinSub(
                GrnKey::query()
                    ->where('category', 'takeOver')
                    ->addNestedWhereQuery(
                        GrnKey::query()
                            ->where('grn', '=', "grn:account:namespace:{$this->user->namespace->namespaceName}:user:{$this->user->userId}:type:{$this->type}")
                            ->orWhere('grn', 'like', "grn:account:namespace:{$this->user->namespace->namespaceName}:user:{$this->user->userId}:type:{$this->type}:%")
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
}
