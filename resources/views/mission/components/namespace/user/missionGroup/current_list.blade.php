@foreach($missionGroups as $missionGroup)
    <div class="bg-gray-100 shadow-sm rounded-lg justify-between items-center">
        <div class="flex justify-between items-center">
            <div class="flex flex-wrap">
                <div class="px-6 py-4 whitespace-nowrap">
                    <p class="text-xl font-bold text-gray-500">{{ $missionGroup->missionGroupModelName }}</p>
                </div>
            </div>
            @if($permission != 'view')
                @if($permission != 'operator')
            <div class="pr-4">
                <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
                    <div class="btn-group" role="group">
                        <button id="completeReset" type="button" class="btn btn-danger dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            {{ __('messages.model.mission.complete.action.reset') }}
                        </button>
                        <ul class="dropdown-menu p-0" style="min-width: 0" aria-labelledby="completeReset">
                            <li>
                                <form action="{{ "/players/{$missionGroup->user->userId}/mission/{$missionGroup->user->namespace->namespaceName}/missionGroup/{$missionGroup->missionGroupModelName}/complete/reset" }}" method="post">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-danger w-100">
                                        {{ __('messages.model.mission.complete.action.accept') }}
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
                @endif
            @endif
        </div>
    </div>
    {{ $missionGroup->currentMissionTasksView('mission/components/namespace/user/missionGroup/missionTask/current_list') }}
@endforeach
