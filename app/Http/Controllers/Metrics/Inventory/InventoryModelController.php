<?php

namespace App\Http\Controllers\Metrics\Inventory;

use App\Domain\Gs2Inventory\ServiceDomain;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class InventoryModelController extends Controller
{
    public static function index(Request $request): View
    {
        $namespaceName = $request->namespaceName;
        $inventoryModelName = $request->inventoryModelName;

        $inventoryModel = (new ServiceDomain())
            ->namespace($namespaceName)
            ->inventoryModel($inventoryModelName);

        return view('metrics/service/inventory/inventoryModel')
            ->with('inventoryModel', $inventoryModel);
    }
}
