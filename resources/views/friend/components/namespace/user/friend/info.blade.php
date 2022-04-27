@if(isset($friend->friendUser))
<div class="flex flex-col">
    <div class="-my-2 overflow-x-auto">
        <div class="py-2 align-middle inline-block min-w-full">
            <table class="min-w-full divide-y divide-gray-200">
                <tr>
                    <th scope="col" class="px-6 py-3 w-25 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        {{ __('messages.model.friend.friend.publicProfile') }}
                    </th>
                    <td class="px-6 py-4 whitespace-nowrap">
                        {{ $friend->friendUser->getPublicProfile() }}
                    </td>
                </tr>
                <tr>
                    <th scope="col" class="px-6 py-3 w-25 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        {{ __('messages.model.friend.friend.friendProfile') }}
                    </th>
                    <td class="px-6 py-4 whitespace-nowrap">
                        {{ $friend->friendUser->getFriendProfile() }}
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>
@endif
