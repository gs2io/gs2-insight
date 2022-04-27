<?php

namespace App\Domain\Gs2Stamina;

use App\Domain\BaseDomain;
use App\Models\GrnKey;
use App\Models\Timeline;
use Gs2\Core\Net\Gs2RestSession;
use Gs2\Stamina\Gs2StaminaRestClient;
use Gs2\Stamina\Model\Stamina;
use Gs2\Stamina\Request\ConsumeStaminaByUserIdRequest;
use Gs2\Stamina\Request\GetStaminaByUserIdRequest;
use Gs2\Stamina\Request\RecoverStaminaByUserIdRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;

class StaminaDomain extends BaseDomain {

    public UserDomain $user;
    public string $staminaModelName;
    public Stamina|null $stamina;

    public function __construct(
        UserDomain $user,
        string     $staminaModelName,
        Stamina|null $stamina = null,
    ) {
        $this->user = $user;
        $this->staminaModelName = $staminaModelName;
        $this->stamina = $stamina;
    }

    public function recover(
        int $recoverValue,
    ) {
        $this->gs2(
            function (Gs2RestSession $session) use ($recoverValue) {
                $client = new Gs2StaminaRestClient(
                    $session,
                );
                $client->recoverStaminaByUserId(
                    (new RecoverStaminaByUserIdRequest())
                        ->withNamespaceName($this->user->namespace->namespaceName)
                        ->withUserId($this->user->userId)
                        ->withStaminaName($this->staminaModelName)
                        ->withRecoverValue($recoverValue)
                );
                return null;
            }
        );
    }

    public function consume(
        int $consumeValue,
    ) {
        $this->gs2(
            function (Gs2RestSession $session) use ($consumeValue) {
                $client = new Gs2StaminaRestClient(
                    $session,
                );
                $client->consumeStaminaByUserId(
                    (new ConsumeStaminaByUserIdRequest())
                        ->withNamespaceName($this->user->namespace->namespaceName)
                        ->withUserId($this->user->userId)
                        ->withStaminaName($this->staminaModelName)
                        ->withConsumeValue($consumeValue)
                );
                return null;
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
                    $client = new Gs2StaminaRestClient(
                        $session,
                    );
                    $result = $client->getStaminaByUserId(
                        (new GetStaminaByUserIdRequest())
                            ->withNamespaceName($this->user->namespace->namespaceName)
                            ->withUserId($this->user->userId)
                            ->withStaminaName($this->staminaModelName)
                    );
                    return $result->getItem();
                }
            );

            $stamina = new StaminaDomain(
                $this->user,
                $this->staminaModelName,
                $item,
            );
        } catch (\Exception $e) {
            var_dump($e->getMessage());
            $stamina = $this;
        }

        return view($view)
            ->with("stamina", $stamina);
    }

    public function timeline(): Builder
    {
        return Timeline::query()
            ->joinSub(
                GrnKey::query()
                    ->where('category', 'staminaModel')
                    ->addNestedWhereQuery(
                        GrnKey::query()
                            ->where('grn', '=', "grn:stamina:namespace:{$this->user->namespace->namespaceName}:user:{$this->user->userId}:staminaModel:{$this->staminaModelName}")
                            ->orWhere('grn', 'like', "grn:stamina:namespace:{$this->user->namespace->namespaceName}:user:{$this->user->userId}:staminaModel:{$this->staminaModelName}:%")
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
            ->with("stamina", $this)
            ->with("timeline", $this->timeline()->simplePaginate(10, ['*'], 'user_timeline'));
    }

}
