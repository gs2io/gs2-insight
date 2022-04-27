@if(isset($item))
<div class="bg-gray-100 shadow-sm rounded-lg justify-between items-center">
    <div class="flex justify-between items-center">
        <div class="flex flex-wrap">
            <div class="px-6 py-4 whitespace-nowrap">
                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                    {{ __('messages.model.ranking.ranking.name') }}
                </label>
                {{ $item->getName() }}
            </div>
        </div>
        <div class="flex flex-wrap">
            <div class="px-6 py-4 whitespace-nowrap">
                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                    {{ __('messages.model.ranking.ranking.scope') }}
                </label>
                {{ $item->getScope() }}
            </div>
        </div>
        <div class="flex flex-wrap">
            <div class="px-6 py-4 whitespace-nowrap">
                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                    {{ __('messages.model.ranking.ranking.status') }}
                </label>
                {{ $item->getStatus() }}
            </div>
        </div>
        <div class="justify-content-end pr-4">
            <form action="{{ url()->current(). "/ranking/{$ranking->user->namespace->namespaceName}/ranking/{$item->getName()}/delete" }}" method="post">
                @csrf
                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-pink-600 hover:bg-pink-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-500">
                    {{ __('messages.model.ranking.ranking.action.delete') }}
                </button>
            </form>
        </div>
    </div>

    <div class="pb-2"></div>
</div>
@endif
