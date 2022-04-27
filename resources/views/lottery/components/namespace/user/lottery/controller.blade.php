@if($permission != 'view')
<div class="p-4 bg-white shadow-sm rounded-lg ">
    <form action="{{ "/players/{$user->userId}/lottery/{$user->namespace->namespaceName}/lottery/lottery" }}" method="post">
        @csrf
        <div class="flex flex-wrap items-end">
            <div class="px-6 py-2 whitespace-nowrap">
                <label for="lotteryModelName" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                    {{ __('messages.model.lottery.lottery.name') }}
                </label>
                <input type="text" id="lotteryModelName" name="lotteryModelName" value="{{ request()->lotteryModelName }}" class="border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
            </div>
            <div class="px-6 py-2 whitespace-nowrap">
                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-400 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    {{ __('messages.model.lottery.lottery.action.lottery') }}
                </button>
            </div>
        </div>
    </form>
</div>
@endif
