@foreach($counters as $counter)
    <div class="bg-gray-100 shadow-sm rounded-lg justify-between items-center">
        <div class="flex justify-between items-center">
            <div class="flex flex-wrap">
                <div class="px-6 py-4 whitespace-nowrap">
                    <p class="text-xl font-bold text-gray-500">{{ $counter->counterModelName }}</p>
                </div>
            </div>
            @if($permission != 'view')
                @if($permission != 'operator')
            <div class="pr-4">
                <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
                    <div class="btn-group" role="group">
                        <button id="counterReset" type="button" class="btn btn-danger dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            {{ __('messages.model.mission.complete.action.reset') }}
                        </button>
                        <ul class="dropdown-menu p-0" style="min-width: 0" aria-labelledby="counterReset">
                            <li>
                                <form action="{{ "/players/{$counter->user->userId}/mission/{$counter->user->namespace->namespaceName}/counter/{$counter->counterModelName}/reset" }}" method="post">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-danger w-100">
                                        {{ __('messages.model.mission.counter.action.accept') }}
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
    <div class="flex flex-col">
        <div class="-my-2 overflow-x-auto">
            <div class="py-2 align-middle inline-block min-w-full">
                <div class="overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('messages.model.mission.scopedValue.resetType') }}
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('messages.model.mission.scopedValue.value') }}
                            </th>
                        </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($counter->counter->getValues() as $value)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{ $value->getResetType() }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{ $value->getValue() }}
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endforeach
{{ $user->counterControllerView('mission/components/namespace/user/counter/controller') }}
