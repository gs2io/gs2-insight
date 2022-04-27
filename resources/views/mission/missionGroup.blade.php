@extends('base')
@section('content')
    @include('commons.language')
    @include('commons.breadcrumb', [
        'hierarchy' => [
            'Home' => url('/'),
            __('messages.model.players') => url("/players"),
            $missionGroup->user->userId => url("/players/". $missionGroup->user->userId. "?mode=mission"),
            "Mission" => url("/players/". $missionGroup->user->userId. "?mode=mission"),
            $missionGroup->user->namespace->namespaceName => url("/players/". $missionGroup->user->userId. "/mission/". $missionGroup->user->namespace->namespaceName),
            "Mission Group Model" => url("/players/". $missionGroup->user->userId. "/mission/". $missionGroup->user->namespace->namespaceName),
            $missionGroup->missionGroupModelName => url()->current(),
        ]
    ])
    <div class="p-8">
        <div class="bg-white shadow-sm rounded-lg">
            <div class="bg-gray-100 shadow-sm rounded-lg justify-between items-center">
                <div class="flex justify-between items-center">
                    <div class="flex flex-wrap">
                        <div class="px-6 py-4 whitespace-nowrap">
                            <p class="text-xl font-bold text-gray-500">{{ $missionGroup->missionGroupModelName }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex">
                @if($permission != 'null')
                <div class="w-25">
                    <div class="m-4 bg-white shadow rounded-lg">
                        <label class="p-4 block mb-2 text-lg font-medium text-gray-900 dark:text-gray-300">
                            {{ __('messages.model.current') }}
                        </label>
                        {{ $missionGroup->currentMissionTasksView('mission/components/namespace/user/missionGroup/missionTask/current_list') }}
                        <div class="p-2"></div>
                    </div>
                </div>
                @endif
                <div class="w-75">
                    <div class="m-4 bg-white shadow rounded-lg">
                        <label class="p-4 block mb-2 text-lg font-medium text-gray-900 dark:text-gray-300">
                            {{ __('messages.model.timelines') }}
                        </label>
                        {{ $missionGroup->timelineView('commons/timeline') }}
                        <div class="p-2"></div>
                    </div>
                </div>
            </div>
            <div class="m-4 bg-white shadow rounded-lg">
                <label class="p-4 block mb-2 text-lg font-medium text-gray-900 dark:text-gray-300">
                    {{ __('messages.model.mission.missionTasks') }}
                </label>
                {{ $missionGroup->missionTasksView('mission/components/namespace/user/missionGroup/missionTask/list') }}
                <div class="p-2"></div>
            </div>
            <div class="p-2"></div>
        </div>
    </div>
@endsection
