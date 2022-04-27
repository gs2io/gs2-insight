<div class="bg-white p-6 shadow-sm rounded-lg justify-between items-center">
    <div class="flex justify-content-end">
        <div class="align-items-end">
            <a href="{{ "/players/". request()->userId. "/reload" }}" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-gray-700 hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900">
                {{ __('messages.model.reload') }}
            </a>
        </div>
    </div>
    <div class="flex flex-wrap">
        <div class="px-6 whitespace-nowrap">
            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                {{ __('messages.model.players.userId') }}
            </label>
            {{ $item->userId }}
        </div>
        <div class="px-6 whitespace-nowrap text-right">
            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                {{ __('messages.model.players.purchasedAmount') }}
            </label>
            {{ $item->purchasedAmount }}
        </div>
        <div class="px-6 whitespace-nowrap">
            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                {{ __('messages.model.players.lastAccessAt') }}
            </label>
            {{ $item->lastAccessAt }}
        </div>
    </div>
    <div class="p-3"></div>
</div>
