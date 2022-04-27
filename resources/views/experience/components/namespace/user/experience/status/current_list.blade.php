<div class="flex flex-col">
    <div class="-my-2 overflow-x-auto">
        <div class="py-2 align-middle inline-block min-w-full">
            <div class="overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('messages.model.experience.status.propertyId') }}
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('messages.model.experience.status.rank') }}
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('messages.model.experience.status.experience') }}
                        </th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($statuses as $status)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ $status->propertyId }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ $status->status->getRankValue() }} / {{ $status->status->getRankCapValue() }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ $status->status->getExperienceValue() }}
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
{{ $user->statusControllerView('experience/components/namespace/user/experience/status/controller') }}
