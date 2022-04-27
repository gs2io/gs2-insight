<div class="p-4 bg-white shadow-sm rounded-lg ">
    <form action="{{ "/players/{$user->userId}/datastore/{$user->namespace->namespaceName}/dataObject/download" }}" method="post">
        @csrf
        <div class="flex flex-wrap items-end">
            <div class="px-6 py-2 whitespace-nowrap">
                <label for="dataObjectName" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                    {{ __('messages.model.datastore.dataObject.name') }}
                </label>
                <input type="text" id="dataObjectName" name="dataObjectName" value="{{ request()->dataObjectName }}" class="border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
            </div>
            <div class="px-6 py-2 whitespace-nowrap">
                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-400 hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    {{ __('messages.model.datastore.dataObject.action.download') }}
                </button>
            </div>
        </div>
    </form>
    @if($permission != 'view')
        @if($permission != 'operator')
    <form action="{{ "/players/{$user->userId}/datastore/{$user->namespace->namespaceName}/dataObject/delete" }}" method="post">
        @csrf
        <div class="flex flex-wrap items-end">
            <div class="px-6 py-2 whitespace-nowrap">
                <label for="dataObjectName" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                    {{ __('messages.model.datastore.dataObject.name') }}
                </label>
                <input type="text" id="dataObjectName" name="dataObjectName" value="{{ request()->dataObjectName }}" class="border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
            </div>
            <div class="px-6 py-2 whitespace-nowrap">
                <div class="btn-group" role="group">
                    <button id="takeOverDelete" type="button" class="btn btn-danger dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                        {{ __('messages.model.datastore.dataObject.action.delete') }}
                    </button>
                    <ul class="dropdown-menu p-0" style="min-width: 0" aria-labelledby="takeOverDelete">
                        <li>
                            <button type="submit" class="btn btn-outline-danger w-100">
                                {{ __('messages.model.datastore.dataObject.action.accept') }}
                            </button>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </form>
        @endif
    @endif
</div>
