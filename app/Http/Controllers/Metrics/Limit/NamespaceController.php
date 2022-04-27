<?php

namespace App\Http\Controllers\Metrics\Limit;

use App\Domain\Gs2Limit\ServiceDomain;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class NamespaceController extends Controller
{
    public static function index(Request $request): View
    {
        $namespaceName = $request->namespaceName;

        $namespace = (new ServiceDomain())
            ->namespace($namespaceName);

        return view('metrics/service/limit/namespace')
            ->with('namespace', $namespace);
    }
}
