<div class="flex flex-col">
    <div class="-my-2 overflow-x-auto">
        <div class="py-2 align-middle inline-block min-w-full">
            <div class="overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('messages.model.mission.missionTask.name') }}
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('messages.model.mission.missionTask.completed') }}
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('messages.model.mission.missionTask.received') }}
                        </th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($missionTasks as $missionTask)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ $missionTask->missionTaskModelName }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ $missionTask->isCompleted() ? "Completed" : "Not Completed" }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ $missionTask->isReceived() ? "Received" : "Not Received" }}
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
{{ $missionGroup->missionTaskControllerView('mission/components/namespace/user/missionGroup/missionTask/controller') }}
