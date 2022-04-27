<?php

namespace App\Http\Controllers\Metrics\Chat;

use App\Domain\Gs2Chat\ServiceDomain;
use App\Http\Controllers\Controller;
use App\Models\Grn;
use App\Models\Metrics;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class NamespaceController extends Controller
{
    public static function index(Request $request): View
    {
        $namespaceName = $request->namespaceName;

        $namespace = (new ServiceDomain())
            ->namespace($namespaceName);

        return view('metrics/service/chat/namespace')
            ->with('namespace', $namespace);
    }
}
