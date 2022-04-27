<?php

namespace App\Http\Controllers\Metrics\Money;

use App\Domain\Gs2Money\ServiceDomain;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ContentController extends Controller
{
    public static function index(Request $request): View
    {
        $namespaceName = $request->namespaceName;
        $contentsId = $request->contentsId;

        $content = (new ServiceDomain())
            ->namespace($namespaceName)
            ->content($contentsId);

        return view('metrics/service/money/content')
            ->with('content', $content);
    }
}
