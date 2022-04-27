<div class="flex flex-col">
    <div class="-my-2 overflow-x-auto">
        <div class="py-2 align-middle inline-block min-w-full">
            <div class="overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('messages.model.account.takeOver.type') }}
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('messages.model.account.takeOver.userIdentifier') }}
                        </th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($takeOvers as $takeOver)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ $takeOver->takeOver->getType() }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ $takeOver->takeOver->getUserIdentifier() }}
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
{{ $user->takeOverControllerView('account/components/namespace/user/takeOver/controller') }}
