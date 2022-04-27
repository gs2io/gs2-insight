<?php

namespace App\Http\Controllers;

use App\Domain\Gs2Inventory\ServiceDomain;
use App\Domain\PlayerDomain;
use Gs2\Core\Exception\Gs2Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public static function namespace(Request $request): View
    {
        $namespaceName = $request->namespaceName;
        $namespace = (new ServiceDomain())
            ->namespace($namespaceName);

        return view('inventory/namespace')
            ->with("namespace", $namespace);
    }

    public static function inventory(Request $request): View
    {
        $namespaceName = $request->namespaceName;
        $userId = $request->userId;
        $inventoryModelName = $request->inventoryModelName;

        $inventory = (new ServiceDomain())
            ->namespace($namespaceName)
            ->user($userId)
            ->inventory($inventoryModelName);

        return view('inventory/inventory')
            ->with("inventory", $inventory);
    }

    public static function item(Request $request): View
    {
        $namespaceName = $request->namespaceName;
        $userId = $request->userId;
        $inventoryModelName = $request->inventoryModelName;
        $itemModelName = $request->itemModelName;

        $item = (new ServiceDomain())
            ->namespace($namespaceName)
            ->user($userId)
            ->inventory($inventoryModelName)
            ->item($itemModelName);

        return view('inventory/item')
            ->with("item", $item);
    }

    public static function acquire(
        Request $request,
    ): View|RedirectResponse
    {
        $namespaceName = $request->namespaceName;
        $inventoryModelName = $request->inventoryModelName;
        $itemModelName = $request->itemModelName;
        $userId = $request->userId;
        $acquireCount = $request->acquireCount;

        try {
            (new PlayerDomain($userId))->inventory(
            )->namespace(
                $namespaceName,
            )->user(
                $userId,
            )->inventory(
                $inventoryModelName,
            )->item(
                $itemModelName,
            )->acquire(
                $acquireCount,
            );
        } catch (Gs2Exception $e) {
            return view('error')
                ->with("errors", $e);
        }

        return redirect()->to("/players/$userId?mode=inventory");
    }

    public static function consume(
        Request $request,
    ): View|RedirectResponse
    {
        $namespaceName = $request->namespaceName;
        $inventoryModelName = $request->inventoryModelName;
        $itemModelName = $request->itemModelName;
        $userId = $request->userId;
        $consumeCount = $request->consumeCount;

        try {
            (new PlayerDomain($userId))->inventory(
            )->namespace(
                $namespaceName,
            )->user(
                $userId,
            )->inventory(
                $inventoryModelName,
            )->item(
                $itemModelName,
            )->consume(
                $consumeCount,
            );
        } catch (Gs2Exception $e) {
            return view('error')
                ->with("errors", $e);
        }

        return redirect()->to("/players/$userId?mode=inventory");
    }

    public static function updateCapacity(
        Request $request,
    ): View|RedirectResponse
    {
        $namespaceName = $request->namespaceName;
        $inventoryModelName = $request->inventoryModelName;
        $userId = $request->userId;
        $capacity = $request->capacity;

        try {
            (new PlayerDomain($userId))->inventory(
            )->namespace(
                $namespaceName,
            )->user(
                $userId,
            )->inventory(
                $inventoryModelName,
            )->updateCapacity(
                $capacity,
            );
        } catch (Gs2Exception $e) {
            return view('error')
                ->with("errors", $e);
        }

        return redirect()->to("/players/$userId?mode=inventory");
    }
}
