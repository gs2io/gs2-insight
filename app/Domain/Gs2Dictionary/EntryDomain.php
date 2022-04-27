<?php

namespace App\Domain\Gs2Dictionary;

use App\Domain\BaseDomain;
use App\Domain\PlayerDomain;
use App\Models\GrnKey;
use App\Models\Timeline;
use Gs2\Dictionary\Gs2DictionaryRestClient;
use Gs2\Dictionary\Model\Entry;
use Gs2\Core\Net\Gs2RestSession;
use Gs2\Dictionary\Request\GetEntryByUserIdRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;

class EntryDomain extends BaseDomain {

    public UserDomain $user;
    public string $entryModelName;
    public Entry|null $entry;

    public function __construct(
        UserDomain $user,
        string $entryModelName,
        Entry|null $entry = null,
    ) {
        $this->user = $user;
        $this->entryModelName = $entryModelName;
        $this->entry = $entry;
    }

    public function timeline(): Builder
    {
        return Timeline::query()
            ->joinSub(
                GrnKey::query()
                    ->where('category', 'entryModel')
                    ->addNestedWhereQuery(
                        GrnKey::query()
                            ->where('grn', '=', "grn:dictionary:namespace:{$this->user->namespace->namespaceName}:user:{$this->user->userId}:entryModel:{$this->entryModelName}")
                            ->orWhere('grn', 'like', "grn:dictionary:namespace:{$this->user->namespace->namespaceName}:user:{$this->user->userId}:entryModel:{$this->entryModelName}:%")
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

    public function infoView(
        string $view,
    ): View
    {
        try {
            $item = $this->gs2(
                function (Gs2RestSession $session) {
                    $client = new Gs2DictionaryRestClient(
                        $session,
                    );
                    $result = $client->getEntryByUserId(
                        (new GetEntryByUserIdRequest())
                            ->withNamespaceName($this->user->namespace->namespaceName)
                            ->withUserId($this->user->userId)
                            ->withEntryModelName($this->entryModelName)
                    );
                    return $result->getItem();
                }
            );

            $entry = new EntryDomain(
                $this->user,
                $this->entryModelName,
                $item,
            );
        } catch (\Exception $e) {
            var_dump($e->getMessage());
            $entry = $this;
        }

        return view($view)
            ->with("entry", $entry);
    }

    public function timelineView(
        string $view,
    ): View
    {
        return view($view)
            ->with("entry", $this)
            ->with("timeline", $this->timeline()->simplePaginate(10, ['*'], 'user_timeline'));
    }

}
