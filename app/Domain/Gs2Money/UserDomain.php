<?php

namespace App\Domain\Gs2Money;

use App\Domain\BaseDomain;
use App\Models\Grn;
use App\Models\GrnKey;
use App\Models\Timeline;
use Gs2\Core\Net\Gs2RestSession;
use Gs2\Money\Gs2MoneyRestClient;
use Gs2\Money\Model\Wallet;
use Gs2\Money\Request\DescribeWalletsByUserIdRequest;
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

    #[Pure] public function wallet(
        int $slot,
    ): WalletDomain {
        return new WalletDomain(
            $this,
            $slot
        );
    }

    public function wallets(
        string $itemName = null,
    ): Builder {
        $entries = Grn::query()
            ->where("parent", "grn:money:namespace:{$this->namespace->namespaceName}:user:{$this->userId}")
            ->where("category", "wallet");
        if (!is_null($itemName)) {
            $entries->where('key', 'like', "$itemName%");
        }
        return $entries;
    }

    public function currentWallets(
    ): array {
        try {
            $items = $this->gs2(
                function (Gs2RestSession $session) {
                    $client = new Gs2MoneyRestClient(
                        $session,
                    );
                    $result = $client->describeWalletsByUserId(
                        (new DescribeWalletsByUserIdRequest())
                            ->withNamespaceName($this->namespace->namespaceName)
                            ->withUserId($this->userId)
                    );
                    return $result->getItems();
                }
            );

            return array_map(
                function (Wallet $item) {
                    return new WalletDomain(
                        $this,
                        $item->getSlot(),
                        $item,
                    );
                }
                , $items);
        } catch (\Exception $e) {
            var_dump($e->getMessage());
            return [];
        }
    }

    public function infoView(
        string $view,
    ): View
    {
        return view($view)
            ->with("wallet", $this);
    }

    public function walletsView(
        string $view,
        string $propertyId = null,
    ): View
    {
        return view($view)
            ->with("user", $this)
            ->with("wallets", (
            tap(
                $this->wallets(
                    $propertyId,
                )
                    ->simplePaginate(10, ['*'], 'user_wallets')
            )->transform(
                function ($grn) {
                    return new WalletDomain(
                        $this,
                        $grn->key,
                    );
                }
            )
            ));
    }

    public function currentWalletsView(
        string $view,
    ): View
    {
        return view($view)
            ->with('user', $this)
            ->with("wallets", $this->currentWallets());
    }

    public function timeline(): Builder
    {
        return Timeline::query()
            ->joinSub(
                GrnKey::query()
                    ->where('category', 'wallet')
                    ->addNestedWhereQuery(
                        GrnKey::query()
                            ->where('grn', '=', "grn:money:namespace:{$this->namespace->namespaceName}:user:{$this->userId}")
                            ->orWhere('grn', 'like', "grn:money:namespace:{$this->namespace->namespaceName}:user:{$this->userId}:%")
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

    public function walletControllerView(
        string $view,
    ): View
    {
        return view($view)
            ->with('user', $this);
    }

}
