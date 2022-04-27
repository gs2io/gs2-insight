<?php

namespace App\Domain\Gs2Exchange;

use App\Domain\BaseDomain;
use App\Domain\PlayerDomain;
use App\Models\GrnKey;
use Gs2\Exchange\Gs2ExchangeRestClient;
use Gs2\Exchange\Model\Await;
use Gs2\Core\Net\Gs2RestSession;
use Gs2\Exchange\Request\GetAwaitByUserIdRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;

class AwaitDomain extends BaseDomain {

    public RateDomain $rate;
    public string $awaitName;
    public Await|null $await;

    public function __construct(
        RateDomain $rate,
        string $awaitName,
        Await|null $await = null,
    ) {
        $this->rate = $rate;
        $this->awaitName = $awaitName;
        $this->await = $await;
    }

    public function infoView(
        string $view,
    ): View
    {
        try {
            $item = $this->gs2(
                function (Gs2RestSession $session) {
                    $client = new Gs2ExchangeRestClient(
                        $session,
                    );
                    $result = $client->getAwaitByUserId(
                        (new GetAwaitByUserIdRequest())
                            ->withNamespaceName($this->rate->user->namespace->namespaceName)
                            ->withUserId($this->rate->user->userId)
                            ->withRateName($this->rate->rateModelName)
                            ->withAwaitName($this->awaitName)
                    );
                    return $result->getItem();
                }
            );
            $await = new AwaitDomain(
                $this->rate,
                $this->awaitName,
                $item,
            );
        } catch (\Exception $e) {
            var_dump($e->getMessage());
            $await = $this;
        }

        return view($view)
            ->with("await", $await);
    }

}
