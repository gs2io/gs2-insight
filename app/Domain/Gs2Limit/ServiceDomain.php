<?php

namespace App\Domain\Gs2Limit;

use App\Domain\BaseDomain;
use App\Models\Grn;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use JetBrains\PhpStorm\Pure;

class ServiceDomain extends BaseDomain {

    public function __construct(
    ) {
    }

    #[Pure] public function namespace(
        string $namespaceName,
    ): NamespaceDomain {
        return new NamespaceDomain(
            $namespaceName,
        );
    }

    public function namespaces(
        string $namespaceName = null,
    ): Builder {
        $namespaces = Grn::query()
            ->where("parent", "grn:limit")
            ->where("category", "namespace");
        if (!is_null($namespaceName)) {
            $namespaces->where('key', 'like', "$namespaceName%");
        }
        return $namespaces;
    }

    public function namespacesView(
        string $view,
        string $namespaceName = null,
    ): View
    {
        return view($view)
            ->with("namespaces", (
                tap(
                    $this->namespaces(
                            $namespaceName,
                        )
                        ->simplePaginate(10, ['*'], 'service_namespaces')
                )->transform(
                    function ($grn) {
                        return new NamespaceDomain(
                            $grn->key,
                        );
                    }
                )
            ));
    }

}
