<?php

namespace App\Domain\Gs2Money;

use App\Domain\BaseDomain;
use App\Models\GrnKey;
use App\Models\Timeline;
use Gs2\Core\Net\Gs2RestSession;
use Gs2\Money\Gs2MoneyRestClient;
use Gs2\Money\Model\Wallet;
use Gs2\Money\Request\DepositByUserIdRequest;
use Gs2\Money\Request\GetWalletByUserIdRequest;
use Gs2\Money\Request\WithdrawByUserIdRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;

class WalletDomain extends BaseDomain {

    public UserDomain $user;
    public int $slot;
    public Wallet|null $wallet;

    public function __construct(
        UserDomain $user,
        int $slot,
        Wallet|null $wallet = null,
    ) {
        $this->user = $user;
        $this->slot = $slot;
        $this->wallet = $wallet;
    }

    public function infoView(
        string $view,
    ): View
    {
        try {
            $item = $this->gs2(
                function (Gs2RestSession $session) {
                    $client = new Gs2MoneyRestClient(
                        $session,
                    );
                    $result = $client->getWalletByUserId(
                        (new GetWalletByUserIdRequest())
                            ->withNamespaceName($this->user->namespace->namespaceName)
                            ->withUserId($this->user->userId)
                            ->withSlot($this->slot)
                    );
                    return $result->getItem();
                }
            );

            $wallet = new WalletDomain(
                $this->user,
                $this->slot,
                $item
            );
        } catch (\Exception $e) {
            var_dump($e->getMessage());
            $wallet = $this;
        }

        return view($view)
            ->with("wallet", $wallet);
    }

    public function deposit(
        float $price,
        int $depositCount,
    ) {
        $this->gs2(
            function (Gs2RestSession $session) use ($price, $depositCount) {
                $client = new Gs2MoneyRestClient(
                    $session,
                );
                $client->depositByUserId(
                    (new DepositByUserIdRequest())
                        ->withNamespaceName($this->user->namespace->namespaceName)
                        ->withUserId($this->user->userId)
                        ->withSlot($this->slot)
                        ->withPrice($price)
                        ->withCount($depositCount)
                );
                return null;
            }
        );
    }

    public function withdraw(
        int $withdrawCount,
    ) {
        $this->gs2(
            function (Gs2RestSession $session) use ($withdrawCount) {
                $client = new Gs2MoneyRestClient(
                    $session,
                );
                $client->withdrawByUserId(
                    (new WithdrawByUserIdRequest())
                        ->withNamespaceName($this->user->namespace->namespaceName)
                        ->withUserId($this->user->userId)
                        ->withSlot($this->slot)
                        ->withCount($withdrawCount)
                );
                return null;
            }
        );
    }

    public function timeline(): Builder
    {
        return Timeline::query()
            ->joinSub(
                GrnKey::query()
                    ->where('category', 'wallet')
                    ->addNestedWhereQuery(
                        GrnKey::query()
                            ->where('grn', '=', "grn:money:namespace:{$this->user->namespace->namespaceName}:user:{$this->user->userId}:wallet:{$this->slot}")
                            ->orWhere('grn', 'like', "grn:money:namespace:{$this->user->namespace->namespaceName}:user:{$this->user->userId}:wallet:{$this->slot}:%")
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
            ->with("money", $this)
            ->with("timeline", $this->timeline()->simplePaginate(10, ['*'], 'user_timeline'));
    }
}
