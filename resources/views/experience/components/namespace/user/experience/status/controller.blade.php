@if($permission != 'view')
<div class="p-4 bg-white shadow-sm rounded-lg ">
    <form action="{{ "/players/{$experience->user->userId}/experience/{$experience->user->namespace->namespaceName}/experience/{$experience->experienceModelName}/status/experience/add" }}" method="post">
        @csrf
        <div class="flex flex-wrap items-end">
            <div class="px-6 py-2 whitespace-nowrap">
                <label for="propertyId" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                    {{ __('messages.model.experience.status.propertyId') }}
                </label>
                <input type="text" id="propertyId" name="propertyId" value="{{ request()->propertyId }}" class="border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
            </div>
            <div class="px-6 py-2 whitespace-nowrap">
                <label for="experienceValue" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                    {{ __('messages.model.experience.status.experienceValue') }}
                </label>
                <input type="number" id="experienceValue" name="experienceValue" class="border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
            </div>
            <div class="px-6 py-2 whitespace-nowrap">
                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-400 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    {{ __('messages.model.experience.status.action.addExperience') }}
                </button>
            </div>
        </div>
    </form>
    <form action="{{ "/players/{$experience->user->userId}/experience/{$experience->user->namespace->namespaceName}/experience/{$experience->experienceModelName}/status/experience/set" }}" method="post">
        @csrf
        <div class="flex flex-wrap items-end">
            <div class="px-6 py-2 whitespace-nowrap">
                <label for="propertyId" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                    {{ __('messages.model.experience.status.propertyId') }}
                </label>
                <input type="text" id="propertyId" name="propertyId" value="{{ request()->propertyId }}" class="border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
            </div>
            <div class="px-6 py-2 whitespace-nowrap">
                <label for="experienceValue" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                    {{ __('messages.model.experience.status.experienceValue') }}
                </label>
                <input type="number" id="experienceValue" name="experienceValue" class="border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
            </div>
            <div class="px-6 py-2 whitespace-nowrap">
                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-yellow-400 hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                    {{ __('messages.model.experience.status.action.setExperience') }}
                </button>
            </div>
        </div>
    </form>
    <form action="{{ "/players/{$experience->user->userId}/experience/{$experience->user->namespace->namespaceName}/experience/{$experience->experienceModelName}/status/rankCap/add" }}" method="post">
        @csrf
        <div class="flex flex-wrap items-end">
            <div class="px-6 py-2 whitespace-nowrap">
                <label for="propertyId" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                    {{ __('messages.model.experience.status.propertyId') }}
                </label>
                <input type="text" id="propertyId" name="propertyId" value="{{ request()->propertyId }}" class="border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
            </div>
            <div class="px-6 py-2 whitespace-nowrap">
                <label for="rankCapValue" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                    {{ __('messages.model.experience.status.rankCapValue') }}
                </label>
                <input type="number" id="rankCapValue" name="rankCapValue" class="border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
            </div>
            <div class="px-6 py-2 whitespace-nowrap">
                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-400 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    {{ __('messages.model.experience.status.action.addRankCap') }}
                </button>
            </div>
        </div>
    </form>
    <form action="{{ "/players/{$experience->user->userId}/experience/{$experience->user->namespace->namespaceName}/experience/{$experience->experienceModelName}/status/rankCap/set" }}" method="post">
        @csrf
        <div class="flex flex-wrap items-end">
            <div class="px-6 py-2 whitespace-nowrap">
                <label for="propertyId" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                    {{ __('messages.model.experience.status.propertyId') }}
                </label>
                <input type="text" id="propertyId" name="propertyId" value="{{ request()->propertyId }}" class="border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
            </div>
            <div class="px-6 py-2 whitespace-nowrap">
                <label for="rankCapValue" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                    {{ __('messages.model.experience.status.rankCapValue') }}
                </label>
                <input type="number" id="rankCapValue" name="rankCapValue" class="border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
            </div>
            <div class="px-6 py-2 whitespace-nowrap">
                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-yellow-400 hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                    {{ __('messages.model.experience.status.action.setRankCap') }}
                </button>
            </div>
        </div>
    </form>
    @if($permission != 'operator')
    <form action="{{ "/players/{$experience->user->userId}/experience/{$experience->user->namespace->namespaceName}/experience/{$experience->experienceModelName}/status/reset" }}" method="post">
        @csrf
        <div class="flex flex-wrap items-end">
            <div class="px-6 py-2 whitespace-nowrap">
                <label for="propertyId" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                    {{ __('messages.model.experience.status.propertyId') }}
                </label>
                <input type="text" id="propertyId" name="propertyId" value="{{ request()->propertyId }}" class="border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
            </div>
            <div class="btn-group" role="group">
                <button id="statusReset" type="button" class="btn btn-danger dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    {{ __('messages.model.experience.status.action.reset') }}
                </button>
                <ul class="dropdown-menu p-0" style="min-width: 0" aria-labelledby="statusReset">
                    <li>
                        <button type="submit" class="btn btn-outline-danger w-100">
                            {{ __('messages.model.experience.status.action.accept') }}
                        </button>
                    </li>
                </ul>
            </div>
        </div>
    </form>
    @endif
</div>
@endif
