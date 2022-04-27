@foreach($questGroups as $questGroup)
    <div class="bg-gray-100 shadow-sm rounded-lg justify-between items-center">
        <div class="flex justify-between items-center">
            <div class="flex flex-wrap">
                <div class="px-6 py-4 whitespace-nowrap">
                    <p class="text-xl font-bold text-gray-500">{{ $questGroup->questGroupModelName }}</p>
                </div>
            </div>
            @if($permission != 'operator')
            <div class="pr-4">
                <form action="{{ "/players/{$questGroup->user->userId}/quest/{$questGroup->user->namespace->namespaceName}/questGroup/{$questGroup->questGroupModelName}/complete/reset" }}" method="post">
                    @csrf
                    <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
                        <div class="btn-group" role="group">
                            <button id="completeReset" type="button" class="btn btn-danger dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                {{ __('messages.model.quest.complete.action.reset') }}
                            </button>
                            <ul class="dropdown-menu p-0" style="min-width: 0" aria-labelledby="completeReset">
                                <li>
                                    <button type="submit" class="btn btn-outline-danger w-100">
                                        {{ __('messages.model.quest.complete.action.accept') }}
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </div>
                </form>
            </div>
            @endif
        </div>
    </div>
    {{ $questGroup->currentQuestsView('quest/components/namespace/user/questGroup/quest/current_list') }}
@endforeach
