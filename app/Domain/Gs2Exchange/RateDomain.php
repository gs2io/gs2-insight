<?php

namespace App\Domain\Gs2Exchange;

use App\Domain\BaseDomain;
use App\Models\GrnKey;
use App\Models\Timeline;
use Gs2\Core\Net\Gs2RestSession;
use Gs2\Distributor\Gs2DistributorRestClient;
use Gs2\Distributor\Request\RunStampSheetExpressWithoutNamespaceRequest;
use Gs2\Exchange\Gs2ExchangeRestClient;
use Gs2\Exchange\Model\Await;
use Gs2\Exchange\Request\DescribeAwaitsByUserIdRequest;
use Gs2\Exchange\Request\ExchangeByUserIdRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;

class RateDomain extends BaseDomain {

    public UserDomain $user;
    public string $rateModelName;

    public function __construct(
        UserDomain $user,
        string $rateModelName,
    ) {
        $this->user = $user;
        $this->rateModelName = $rateModelName;
    }

    public function exchange(
    )
    {
        $this->gs2(
            function (Gs2RestSession $session) {
                $client = new Gs2ExchangeRestClient(
                    $session,
                );
                $result = $client->exchangeByUserId(
                    (new ExchangeByUserIdRequest())
                        ->withNamespaceName($this->user->namespace->namespaceName)
                        ->withUserId($this->user->userId)
                        ->withRateName($this->rateModelName)
                        ->withCount(1)
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

    public function currentAwaits(
    ): array {
        try {
            return $this->gs2(
                function (Gs2RestSession $session) {
                    $client = new Gs2ExchangeRestClient(
                        $session,
                    );
                    $result = $client->describeAwaitsByUserId(
                        (new DescribeAwaitsByUserIdRequest())
                            ->withNamespaceName($this->user->namespace->namespaceName)
                            ->withUserId($this->user->userId)
                            ->withRateName($this->rateModelName)
                    );
                    return array_map(
                        function (Await $item) {
                            return new AwaitDomain(
                                $this,
                                $item->getRateName(),
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

    public function infoView(
        string $view,
    ): View
    {
        return view($view)
            ->with("rate", $this);
    }

    public function currentAwaitsView(
        string $view,
    ): View
    {
        return view($view)
            ->with('user', $this)
            ->with("awaits", $this->currentAwaits());
    }

    public function timeline(): Builder
    {
        return Timeline::query()
            ->joinSub(
                GrnKey::query()
                    ->where('category', 'rateModel')
                    ->addNestedWhereQuery(
                        GrnKey::query()
                            ->where('grn', '=', "grn:exchange:namespace:{$this->user->namespace->namespaceName}:user:{$this->user->userId}:rateModel:{$this->rateModelName}")
                            ->orWhere('grn', 'like', "grn:exchange:namespace:{$this->user->namespace->namespaceName}:user:{$this->user->userId}:rateModel:{$this->rateModelName}:%")
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
