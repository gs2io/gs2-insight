@if($permission != 'view')
<div class="p-4 bg-white shadow-sm rounded-lg ">
    <form action="{{ "/players/{$user->userId}/exchange/{$user->namespace->namespaceName}/rate/exchange" }}" method="post">
        @csrf
        <div class="flex flex-wrap items-end">
            <div class="px-6 py-2 whitespace-nowrap">
                <label for="rateModelName" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                    {{ __('messages.model.exchange.rate.name') }}
                </label>
                <input type="text" id="rateModelName" name="rateModelName" value="{{ request()->rateModelName }}" class="border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
            </div>
            <div class="px-6 py-2 whitespace-nowrap">
                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-400 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    {{ __('messages.model.exchange.rate.action.exchange') }}
                </button>
            </div>
        </div>
    </form>
</div>
@endif
