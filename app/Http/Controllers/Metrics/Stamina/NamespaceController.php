<?php

namespace App\Http\Controllers\Metrics\Stamina;

use App\Domain\Gs2Stamina\ServiceDomain;
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

        return view('metrics/service/stamina/namespace')
            ->with('namespace', $namespace);
    }
}
