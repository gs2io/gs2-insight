<?php

namespace App\Http\Controllers;

use App\Domain\Gs2Experience\ServiceDomain;
use App\Domain\PlayerDomain;
use Gs2\Core\Exception\Gs2Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ExperienceController extends Controller
{
    public static function namespace(Request $request): View
    {
        $namespaceName = $request->namespaceName;
        $namespace = (new ServiceDomain())
            ->namespace($namespaceName);

        return view('experience/namespace')
            ->with("namespace", $namespace);
    }

    public static function experience(Request $request): View
    {
        $namespaceName = $request->namespaceName;
        $userId = $request->userId;
        $experienceModelName = $request->experienceModelName;

        $experience = (new ServiceDomain())
            ->namespace($namespaceName)
            ->user($userId)
            ->experience($experienceModelName);

        return view('experience/experience')
            ->with("experience", $experience);
    }

    public static function status(Request $request): View
    {
        $namespaceName = $request->namespaceName;
        $userId = $request->userId;
        $experienceModelName = $request->experienceModelName;
        $propertyId = $request->propertyId;

        $status = (new ServiceDomain())
            ->namespace($namespaceName)
            ->user($userId)
            ->experience($experienceModelName)
            ->status($propertyId);

        return view('experience/status')
            ->with("status", $status);
    }

    public static function addExperience(
        Request $request,
    ): View|RedirectResponse
    {
        $namespaceName = $request->namespaceName;
        $experienceModelName = $request->experienceModelName;
        $propertyId = $request->propertyId;
        $userId = $request->userId;
        $experienceValue = $request->experienceValue;

        try {
            (new PlayerDomain($userId))->experience(
            )->namespace(
                $namespaceName,
            )->user(
                $userId
            )->experience(
                $experienceModelName,
            )->status(
                $propertyId,
            )->addExperience(
                $experienceValue,
            );
        } catch (Gs2Exception $e) {
            return view('error')
                ->with("errors", $e);
        }

        return redirect()->to("/players/$userId?mode=experience");
    }

    public static function setExperience(
        Request $request,
    ): View|RedirectResponse
    {
        $namespaceName = $request->namespaceName;
        $experienceModelName = $request->experienceModelName;
        $propertyId = $request->propertyId;
        $userId = $request->userId;
        $experienceValue = $request->experienceValue;

        try {
            (new PlayerDomain($userId))->experience(
            )->namespace(
                $namespaceName,
            )->user(
                $userId
            )->experience(
                $experienceModelName,
            )->status(
                $propertyId,
            )->setExperience(
                $experienceValue,
            );
        } catch (Gs2Exception $e) {
            return view('error')
                ->with("errors", $e);
        }

        return redirect()->to("/players/$userId?mode=experience");
    }

    public static function addRankCap(
        Request $request,
    ): View|RedirectResponse
    {
        $namespaceName = $request->namespaceName;
        $experienceModelName = $request->experienceModelName;
        $propertyId = $request->propertyId;
        $userId = $request->userId;
        $rankCapValue = $request->rankCapValue;

        try {
            (new PlayerDomain($userId))->experience(
            )->namespace(
                $namespaceName,
            )->user(
                $userId
            )->experience(
                $experienceModelName,
            )->status(
                $propertyId,
            )->addRankCap(
                $rankCapValue,
            );
        } catch (Gs2Exception $e) {
            return view('error')
                ->with("errors", $e);
        }

        return redirect()->to("/players/$userId?mode=experience");
    }

    public static function setRankCap(
        Request $request,
    ): View|RedirectResponse
    {
        $namespaceName = $request->namespaceName;
        $experienceModelName = $request->experienceModelName;
        $propertyId = $request->propertyId;
        $userId = $request->userId;
        $rankCapValue = $request->rankCapValue;

        try {
            (new PlayerDomain($userId))->experience(
            )->namespace(
                $namespaceName,
            )->user(
                $userId
            )->experience(
                $experienceModelName,
            )->status(
                $propertyId,
            )->setRankCap(
                $rankCapValue,
            );
        } catch (Gs2Exception $e) {
            return view('error')
                ->with("errors", $e);
        }

        return redirect()->to("/players/$userId?mode=experience");
    }

    public static function reset(
        Request $request,
    ): View|RedirectResponse
    {
        $namespaceName = $request->namespaceName;
        $experienceModelName = $request->experienceModelName;
        $propertyId = $request->propertyId;
        $userId = $request->userId;

        try {
            (new PlayerDomain($userId))->experience(
            )->namespace(
                $namespaceName,
            )->user(
                $userId
            )->experience(
                $experienceModelName,
            )->status(
                $propertyId,
            )->delete();
        } catch (Gs2Exception $e) {
            return view('error')
                ->with("errors", $e);
        }

        return redirect()->to("/players/$userId?mode=experience");
    }
}
