<form action="{{ $url }}" method="get">
    <div class="bg-white p-6 shadow-sm rounded-lg justify-between items-center">
        <div class="px-6 py-4 whitespace-nowrap">
            <label for="namespaceName" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                {{ __('messages.model.script.namespace.name') }}
            </label>
            <input type="text" id="namespaceName" name="namespaceName" value="{{ request()->input('namespaceName') }}" class="border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
        </div>
        <div class="px-6 whitespace-nowrap">
            <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                {{ __('messages.model.script.namespace.action.search') }}
            </button>
        </div>
    </div>
</form>
