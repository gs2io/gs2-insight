<?php

namespace App\Http\Controllers;

use App\Domain\Gs2Gateway\NamespaceDomain;
use App\Domain\Gs2Gateway\ServiceDomain;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DangerController extends Controller
{
    public static function index(Request $request): View
    {
        return view('danger/index')
            ->with("namespaces", (
            tap(
                (new ServiceDomain())->namespaces()
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

    public static function disconnectAll(Request $request): RedirectResponse
    {
        $namespaceName = $request->namespaceName;

        (new ServiceDomain())
            ->namespace($namespaceName)
            ->disconnectAll();

        return redirect()->to("/");
    }
}
