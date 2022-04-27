@if(isset($follower->followUser))
<div class="flex flex-col">
    <div class="-my-2 overflow-x-auto">
        <div class="py-2 align-middle inline-block min-w-full">
            <table class="min-w-full divide-y divide-gray-200">
                <tr>
                    <th scope="col" class="px-6 py-3 w-25 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        {{ __('messages.model.friend.follower.publicProfile') }}
                    </th>
                    <td class="px-6 py-4 whitespace-nowrap">
                        {{ $follower->followUser->getPublicProfile() }}
                    </td>
                </tr>
                <tr>
                    <th scope="col" class="px-6 py-3 w-25 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        {{ __('messages.model.friend.follower.followerProfile') }}
                    </th>
                    <td class="px-6 py-4 whitespace-nowrap">
                        {{ $follower->followUser->getFollowerProfile() }}
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>
@endif
