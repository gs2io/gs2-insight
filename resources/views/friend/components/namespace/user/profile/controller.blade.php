@if($permission != 'view')
<div class="p-4 bg-white shadow-sm rounded-lg ">
    <form action="{{ "/players/{$profile->user->userId}/friend/{$profile->user->namespace->namespaceName}/profile/update" }}" method="post">
        @csrf
        <div class="flex flex-wrap items-end">
            <div class="px-6 py-2 whitespace-nowrap">
                <label for="publicProfile" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                    {{ __('messages.model.friend.profile.publicProfile') }}
                </label>
                <input type="text" id="publicProfile" name="publicProfile" value="{{ $profile->profile ? $profile->profile->getPublicProfile() : "" }}" class="border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
            </div>
        </div>
        <div class="flex flex-wrap items-end">
            <div class="px-6 py-2 whitespace-nowrap">
                <label for="followerProfile" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                    {{ __('messages.model.friend.profile.followerProfile') }}
                </label>
                <input type="text" id="followerProfile" name="followerProfile" value="{{ $profile->profile ? $profile->profile->getFollowerProfile() : "" }}" class="border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
            </div>
        </div>
        <div class="flex flex-wrap items-end">
            <div class="px-6 py-2 whitespace-nowrap">
                <label for="friendProfile" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                    {{ __('messages.model.friend.profile.friendProfile') }}
                </label>
                <input type="text" id="friendProfile" name="friendProfile" value="{{ $profile->profile ? $profile->profile->getFriendProfile() : "" }}" class="border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
            </div>
            <div class="px-6 py-2 whitespace-nowrap">
                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-400 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    {{ __('messages.model.friend.profile.action.update') }}
                </button>
            </div>
        </div>
    </form>
</div>
@endif
