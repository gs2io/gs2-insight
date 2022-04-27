<?php

namespace App\Http\Controllers\Metrics\Schedule;

use App\Domain\Gs2Schedule\ServiceDomain;
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

        return view('metrics/service/schedule/namespace')
            ->with('namespace', $namespace);
    }
}
