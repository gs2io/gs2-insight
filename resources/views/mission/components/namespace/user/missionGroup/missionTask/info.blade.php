<table class="min-w-full">
    <tr>
        <th class="bg-gray-50 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
            {{ __('messages.model.mission.missionTask.completed') }}
        </th>
        <td class="px-6 py-4 whitespace-nowrap">
            {{ $missionTask->isCompleted() ? "Completed" : "Not Completed" }}
        </td>
    </tr>
    <tr>
        <th class="bg-gray-50 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
            {{ __('messages.model.mission.missionTask.received') }}
        </th>
        <td class="px-6 py-4 whitespace-nowrap">
            {{ $missionTask->isReceived() ? "Received" : "Not Received" }}
        </td>
    </tr>
</table>
