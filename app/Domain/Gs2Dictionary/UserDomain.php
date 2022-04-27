<?php

namespace App\Domain\Gs2Dictionary;

use App\Domain\BaseDomain;
use App\Domain\PlayerDomain;
use App\Models\Grn;
use App\Models\GrnKey;
use App\Models\Timeline;
use Gs2\Dictionary\Gs2DictionaryRestClient;
use Gs2\Dictionary\Model\Entry;
use Gs2\Dictionary\Request\AddEntriesByUserIdRequest;
use Gs2\Dictionary\Request\DescribeEntriesByUserIdRequest;
use Gs2\Chat\Gs2ChatRestClient;
use Gs2\Core\Net\Gs2RestSession;
use Gs2\Dictionary\Request\ResetByUserIdRequest;
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

    public function addEntries(
        array $entryNames,
    ) {
        $this->gs2(
            function (Gs2RestSession $session) use ($entryNames) {
                $client = new Gs2DictionaryRestClient(
                    $session,
                );
                $client->addEntriesByUserId(
                    (new AddEntriesByUserIdRequest())
                        ->withNamespaceName($this->namespace->namespaceName)
                        ->withUserId($this->userId)
                        ->withEntryModelNames($entryNames)
                );
                return null;
            }
        );
    }

    public function resetEntries() {
        $this->gs2(
            function (Gs2RestSession $session) {
                $client = new Gs2DictionaryRestClient(
                    $session,
                );
                $client->resetByUserId(
                    (new ResetByUserIdRequest())
                        ->withNamespaceName($this->namespace->namespaceName)
                        ->withUserId($this->userId)
                );
                return null;
            }
        );
    }

    #[Pure] public function entry(
        string $entryModelName,
    ): EntryDomain {
        return new EntryDomain(
            $this,
            $entryModelName
        );
    }

    public function entries(
        string $entryModelName = null,
    ): Builder {
        $entries = Grn::query()
            ->where("parent", "grn:dictionary:namespace:{$this->namespace->namespaceName}:user:{$this->userId}")
            ->where("category", "entryModel");
        if (!is_null($entryModelName)) {
            $entries->where('key', 'like', "$entryModelName%");
        }
        return $entries;
    }

    public function currentEntries(
    ): array {
        try {
            $entries = $this->gs2(
                function (Gs2RestSession $session) {
                    $client = new Gs2DictionaryRestClient(
                        $session,
                    );
                    $result = $client->describeEntriesByUserId(
                        (new DescribeEntriesByUserIdRequest())
                            ->withNamespaceName($this->namespace->namespaceName)
                            ->withUserId($this->userId)
                            ->withLimit(1000)
                    );
                    return $result->getItems();
                }
            );
            return array_map(
                function (Entry $entry) {
                    return new EntryDomain(
                        $this,
                        $entry->getName(),
                        $entry,
                    );
                },
                $entries
            );
        } catch (\Exception $e) {
            var_dump($e->getMessage());
            return [];
        }
    }

    public function entriesView(
        string $view,
        string $entryModelName = null,
    ): View
    {
        return view($view)
            ->with("user", $this)
            ->with("entries", (
            tap(
                $this->entries(
                    $entryModelName,
                )
                    ->simplePaginate(10, ['*'], 'user_entries')
            )->transform(
                function ($grn) {
                    return new EntryDomain(
                        $this,
                        $grn->key,
                    );
                }
            )
            ));
    }

    public function currentEntriesView(
        string $view,
    ): View
    {
        return view($view)
            ->with('user', $this)
            ->with("entries", $this->currentEntries());
    }

    public function timeline(): Builder
    {
        return Timeline::query()
            ->joinSub(
                GrnKey::query()
                    ->where('category', 'entryModel')
                    ->addNestedWhereQuery(
                        GrnKey::query()
                            ->where('grn', '=', "grn:dictionary:namespace:{$this->namespace->namespaceName}:user:{$this->userId}")
                            ->orWhere('grn', 'like', "grn:dictionary:namespace:{$this->namespace->namespaceName}:user:{$this->userId}:%")
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

    public function entryControllerView(
        string $view,
    ): View
    {
        return view($view)
            ->with('user', $this);
    }

}
