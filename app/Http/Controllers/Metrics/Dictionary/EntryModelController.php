<?php

namespace App\Http\Controllers\Metrics\Dictionary;

use App\Domain\Gs2Dictionary\ServiceDomain;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class EntryModelController extends Controller
{
    public static function index(Request $request): View
    {
        $namespaceName = $request->namespaceName;
        $entryModelName = $request->entryModelName;

        $entryModel = (new ServiceDomain())
            ->namespace($namespaceName)
            ->entryModel($entryModelName);

        return view('metrics/service/dictionary/entryModel')
            ->with('entryModel', $entryModel);
    }
}
