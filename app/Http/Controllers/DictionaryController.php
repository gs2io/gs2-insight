<?php

namespace App\Http\Controllers;

use App\Domain\Gs2Dictionary\ServiceDomain;
use App\Domain\PlayerDomain;
use Gs2\Core\Exception\Gs2Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DictionaryController extends Controller
{
    public static function namespace(Request $request): View
    {
        $namespaceName = $request->namespaceName;
        $namespace = (new ServiceDomain())
            ->namespace($namespaceName);

        return view('dictionary/namespace')
            ->with("namespace", $namespace);
    }

    public static function entry(Request $request): View
    {
        $namespaceName = $request->namespaceName;
        $userId = $request->userId;
        $entryModelName = $request->entryModelName;

        $entry = (new ServiceDomain())
            ->namespace($namespaceName)
            ->user($userId)
            ->entry($entryModelName);

        return view('dictionary/entry')
            ->with("entry", $entry);
    }

    public static function add(
        Request $request,
    ): View|RedirectResponse
    {
        $userId = $request->userId;
        $namespaceName = $request->namespaceName;
        $entryModelName = $request->entryModelName;

        try {
            (new PlayerDomain($userId))
                ->dictionary()
                ->namespace(
                    $namespaceName,
                )->user(
                    $userId
                )->addEntries(
                    [$entryModelName],
                );
        } catch (Gs2Exception $e) {
            return view('error')
                ->with("errors", $e);
        }

        return redirect()->to("/players/$userId?mode=dictionary");
    }

    public static function reset(
        Request $request,
    ): View|RedirectResponse
    {
        $userId = $request->userId;
        $namespaceName = $request->namespaceName;

        try {
            (new PlayerDomain($userId))
                ->dictionary()
                ->namespace(
                    $namespaceName,
                )->user(
                    $userId
                )->resetEntries();
        } catch (Gs2Exception $e) {
            return view('error')
                ->with("errors", $e);
        }

        return redirect()->to("/players/$userId?mode=dictionary");
    }
}
