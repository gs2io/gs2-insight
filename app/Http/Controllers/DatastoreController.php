<?php

namespace App\Http\Controllers;

use App\Domain\Gs2Datastore\ServiceDomain;
use App\Domain\PlayerDomain;
use Gs2\Core\Exception\Gs2Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DatastoreController extends Controller
{
    public static function namespace(Request $request): View
    {
        $namespaceName = $request->namespaceName;
        $namespace = (new ServiceDomain())
            ->namespace($namespaceName);

        return view('datastore/namespace')
            ->with("namespace", $namespace);
    }

    public static function dataObject(Request $request): View
    {
        $namespaceName = $request->namespaceName;
        $userId = $request->userId;
        $dataObjectName = $request->dataObjectName;

        $dataObject = (new ServiceDomain())
            ->namespace($namespaceName)
            ->user($userId)
            ->dataObject($dataObjectName);

        return view('datastore/dataObject')
            ->with("dataObject", $dataObject);
    }

    public static function download(
        Request $request,
    ): View|RedirectResponse
    {
        $userId = $request->userId;
        $namespaceName = $request->namespaceName;
        $dataObjectName = $request->dataObjectName;

        try {
            $url = (new PlayerDomain($userId))
                ->datastore()
                ->namespace($namespaceName)
                ->user($userId)
                ->dataObject($dataObjectName)
                ->download();
            return redirect()->to($url);
        } catch (Gs2Exception $e) {
            return view('error')
                ->with("errors", $e);
        }
    }

    public static function delete(
        Request $request,
    ): View|RedirectResponse
    {
        $userId = $request->userId;
        $namespaceName = $request->namespaceName;
        $dataObjectName = $request->dataObjectName;

        try {
            (new PlayerDomain($userId))
                ->datastore()
                ->namespace($namespaceName)
                ->user($userId)
                ->dataObject($dataObjectName)
                ->delete();
        } catch (Gs2Exception $e) {
            return view('error')
                ->with("errors", $e);
        }

        return redirect()->to("/players/$userId?mode=datastore");
    }
}
