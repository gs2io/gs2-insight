@if($permission != 'view')
<div class="p-4 bg-white shadow-sm rounded-lg ">
    <form action="{{ "/players/{$user->userId}/friend/{$user->namespace->namespaceName}/receiveRequest/accept" }}" method="post">
        @csrf
        <div class="flex flex-wrap items-end">
            <div class="px-6 py-2 whitespace-nowrap">
                <label for="fromUserId" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                    {{ __('messages.model.friend.receiveRequest.fromUserId') }}
                </label>
                <input type="text" id="fromUserId" name="fromUserId" value="{{ request()->fromUserId }}" class="border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
            </div>
            <div class="px-6 py-2 whitespace-nowrap">
                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-400 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    {{ __('messages.model.friend.receiveRequest.action.accept') }}
                </button>
            </div>
        </div>
    </form>
    @if($permission != 'operator')
    <form action="{{ "/players/{$user->userId}/friend/{$user->namespace->namespaceName}/receiveRequest/reject" }}" method="post">
        @csrf
        <div class="flex flex-wrap items-end">
            <div class="px-6 py-2 whitespace-nowrap">
                <label for="fromUserId" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                    {{ __('messages.model.friend.receiveRequest.fromUserId') }}
                </label>
                <input type="text" id="fromUserId" name="fromUserId" value="{{ request()->fromUserId }}" class="border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
            </div>
            <div class="px-6 py-2 whitespace-nowrap">
                <div class="btn-group" role="group">
                    <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-yellow-400 hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                        {{ __('messages.model.friend.receiveRequest.action.reject') }}
                    </button>
                </div>
            </div>
        </div>
    </form>
    @endif
</div>
@endif
