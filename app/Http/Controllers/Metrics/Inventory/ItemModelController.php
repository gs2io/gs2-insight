<?php

namespace App\Http\Controllers\Metrics\Inventory;

use App\Domain\Gs2Inventory\ServiceDomain;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ItemModelController extends Controller
{
    public static function index(Request $request): View
    {
        $namespaceName = $request->namespaceName;
        $inventoryModelName = $request->inventoryModelName;
        $itemModelName = $request->itemModelName;

        $itemModel = (new ServiceDomain())
            ->namespace($namespaceName)
            ->inventoryModel($inventoryModelName)
            ->itemModel($itemModelName);

        return view('metrics/service/inventory/itemModel')
            ->with('itemModel', $itemModel);
    }
}
