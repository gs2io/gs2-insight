<?php

namespace App\Domain\Gs2Showcase;

use App\Domain\BaseDomain;
use App\Models\GrnKey;
use App\Models\Timeline;
use Gs2\Core\Net\Gs2RestSession;
use Gs2\Distributor\Gs2DistributorRestClient;
use Gs2\Distributor\Request\RunStampSheetExpressWithoutNamespaceRequest;
use Gs2\Showcase\Gs2ShowcaseRestClient;
use Gs2\Showcase\Request\BuyByUserIdRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;

class DisplayItemDomain extends BaseDomain {

    public ShowcaseDomain $showcase;
    public string $displayItemId;

    public function __construct(
        ShowcaseDomain $showcase,
        string     $displayItemId,
    ) {
        $this->showcase = $showcase;
        $this->displayItemId = $displayItemId;
    }

    public function buy(
    )
    {
        $this->gs2(
            function (Gs2RestSession $session) {
                $client = new Gs2ShowcaseRestClient(
                    $session,
                );
                $result = $client->buyByUserId(
                    (new BuyByUserIdRequest())
                        ->withNamespaceName($this->showcase->user->namespace->namespaceName)
                        ->withUserId($this->showcase->user->userId)
                        ->withShowcaseName($this->showcase->showcaseModelName)
                        ->withDisplayItemId($this->displayItemId)
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
            ->with("displayItemModel", $this);
    }

    public function timeline(): Builder
    {
        return Timeline::query()
            ->joinSub(
                GrnKey::query()
                    ->where('category', 'displayItemModel')
                    ->addNestedWhereQuery(
                        GrnKey::query()
                            ->where('grn', '=', "grn:showcase:namespace:{$this->showcase->user->namespace->namespaceName}:user:{$this->showcase->user->userId}:showcaseModel:{$this->showcase->showcaseModelName}:displayItemModel:{$this->displayItemId}")
                            ->orWhere('grn', 'like', "grn:showcase:namespace:{$this->showcase->user->namespace->namespaceName}:user:{$this->showcase->user->userId}:showcaseModel:{$this->showcase->showcaseModelName}:displayItemModel:{$this->displayItemId}:%")
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
