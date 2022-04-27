<?php

namespace App\Domain\Gs2Stamina;

use App\Domain\BaseDomain;
use App\Models\Grn;
use App\Models\GrnKey;
use App\Models\Timeline;
use Gs2\Core\Net\Gs2RestSession;
use Gs2\Stamina\Gs2StaminaRestClient;
use Gs2\Stamina\Model\Stamina;
use Gs2\Stamina\Request\DescribeStaminasByUserIdRequest;
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

    #[Pure] public function stamina(
        string $staminaModelName,
    ): StaminaDomain {
        return new StaminaDomain(
            $this,
            $staminaModelName
        );
    }

    public function staminas(
        string $itemName = null,
    ): Builder {
        $entries = Grn::query()
            ->where("parent", "grn:stamina:namespace:{$this->namespace->namespaceName}")
            ->where("category", "staminaModel");
        if (!is_null($itemName)) {
            $entries->where('key', 'like', "$itemName%");
        }
        return $entries;
    }

    public function currentStaminas(
    ): array {
        $staminas = $this->gs2(
            function (Gs2RestSession $session) {
                $client = new Gs2StaminaRestClient(
                    $session,
                );
                $result = $client->describeStaminasByUserId(
                    (new DescribeStaminasByUserIdRequest())
                        ->withNamespaceName($this->namespace->namespaceName)
                        ->withUserId($this->userId)
                        ->withLimit(1000)
                );
                return $result->getItems();
            }
        );
        return $this->staminas()->get()->transform(
            function ($grn) use ($staminas) {
                $filteredStaminas = array_filter($staminas, function (Stamina $stamina) use ($grn) {
                    return $stamina->getStaminaName() == $grn->key;
                });
                if (count($filteredStaminas) > 0) {
                    return new StaminaDomain(
                        $this,
                        $grn->key,
                        $filteredStaminas[array_key_first($filteredStaminas)],
                    );
                } else {
                    return new StaminaDomain(
                        $this,
                        $grn->key,
                        (new Stamina())
                            ->withStaminaName($grn->key)
                            ->withUserId($this->userId),
                    );
                }
            }
        )->toArray();
    }

    public function infoView(
        string $view,
    ): View
    {
        return view($view)
            ->with("stamina", $this);
    }

    public function staminasView(
        string $view,
        string $propertyId = null,
    ): View
    {
        return view($view)
            ->with("user", $this)
            ->with("staminas", (
            tap(
                $this->staminas(
                    $propertyId,
                )
                    ->simplePaginate(10, ['*'], 'user_staminas')
            )->transform(
                function ($grn) {
                    return new StaminaDomain(
                        $this,
                        $grn->key,
                    );
                }
            )
            ));
    }

    public function currentStaminasView(
        string $view,
    ): View
    {
        return view($view)
            ->with('user', $this)
            ->with("staminas", $this->currentStaminas());
    }

    public function timeline(): Builder
    {
        return Timeline::query()
            ->joinSub(
                GrnKey::query()
                    ->where('category', 'staminaModel')
                    ->addNestedWhereQuery(
                        GrnKey::query()
                            ->where('grn', '=', "grn:stamina:namespace:{$this->namespace->namespaceName}:user:{$this->userId}")
                            ->orWhere('grn', 'like', "grn:stamina:namespace:{$this->namespace->namespaceName}:user:{$this->userId}:%")
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

    public function staminaControllerView(
        string $view,
    ): View
    {
        return view($view)
            ->with('user', $this);
    }

}
