<?php

namespace App\Domain\Gs2Lottery;

use App\Domain\BaseDomain;
use App\Models\GrnKey;
use App\Models\Timeline;
use Gs2\Core\Net\Gs2RestSession;
use Gs2\Distributor\Gs2DistributorRestClient;
use Gs2\Distributor\Request\RunStampSheetExpressWithoutNamespaceRequest;
use Gs2\Lottery\Gs2LotteryRestClient;
use Gs2\Lottery\Request\DrawByUserIdRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;

class LotteryDomain extends BaseDomain {

    public UserDomain $user;
    public string $lotteryModelName;

    public function __construct(
        UserDomain $user,
        string     $lotteryModelName,
    ) {
        $this->user = $user;
        $this->lotteryModelName = $lotteryModelName;
    }

    public function draw(
    )
    {
        $this->gs2(
            function (Gs2RestSession $session) {
                $client = new Gs2LotteryRestClient(
                    $session,
                );
                $result = $client->drawByUserId(
                    (new DrawByUserIdRequest())
                        ->withNamespaceName($this->user->namespace->namespaceName)
                        ->withUserId($this->user->userId)
                        ->withLotteryName($this->lotteryModelName)
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

    public function infoView(
        string $view,
    ): View
    {
        return view($view)
            ->with("lotteryModel", $this);
    }

    public function timeline(): Builder
    {
        return Timeline::query()
            ->joinSub(
                GrnKey::query()
                    ->where('category', 'lotteryModel')
                    ->addNestedWhereQuery(
                        GrnKey::query()
                            ->where('grn', '=', "grn:lottery:namespace:{$this->user->namespace->namespaceName}:user:{$this->user->userId}:lotteryModel:{$this->lotteryModelName}")
                            ->orWhere('grn', 'like', "grn:lottery:namespace:{$this->user->namespace->namespaceName}:user:{$this->user->userId}:lotteryModel:{$this->lotteryModelName}:%")
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
