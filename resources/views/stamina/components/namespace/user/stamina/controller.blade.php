<div class="p-4 bg-white shadow-sm rounded-lg ">
    <form action="{{ "/players/{$user->userId}/stamina/{$user->namespace->namespaceName}/stamina/recover" }}" method="post">
        @csrf
        <div class="flex flex-wrap items-end">
            <div class="px-6 py-2 whitespace-nowrap">
                <label for="staminaModelName" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                    {{ __('messages.model.stamina.stamina.name') }}
                </label>
                <input type="text" id="staminaModelName" name="staminaModelName" value="{{ request()->staminaModelName }}" class="border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
            </div>
            <div class="px-6 py-2 whitespace-nowrap">
                <label for="recoverValue" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                    {{ __('messages.model.stamina.stamina.value') }}
                </label>
                <input type="number" id="recoverValue" name="recoverValue" class="border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
            </div>
            <div class="px-6 py-2 whitespace-nowrap">
                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-400 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    {{ __('messages.model.stamina.stamina.action.recover') }}
                </button>
            </div>
        </div>
    </form>
    @if($permission != 'operator')
    <form action="{{ "/players/{$user->userId}/stamina/{$user->namespace->namespaceName}/stamina/consume" }}" method="post">
        @csrf
        <div class="flex flex-wrap items-end">
            <div class="px-6 py-2 whitespace-nowrap">
                <label for="staminaModelName" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                    {{ __('messages.model.stamina.stamina.name') }}
                </label>
                <input type="text" id="staminaModelName" name="staminaModelName" value="{{ request()->staminaModelName }}" class="border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
            </div>
            <div class="px-6 py-2 whitespace-nowrap">
                <label for="consumeValue" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                    {{ __('messages.model.stamina.stamina.value') }}
                </label>
                <input type="number" id="consumeValue" name="consumeValue" class="border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
            </div>
            <div class="px-6 py-2 whitespace-nowrap">
                <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
                    <div class="btn-group" role="group">
                        <button id="consumeStatus" type="button" class="btn btn-danger dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            {{ __('messages.model.stamina.stamina.action.consume') }}
                        </button>
                        <ul class="dropdown-menu p-0" style="min-width: 0" aria-labelledby="consumeStatus">
                            <li>
                                <button type="submit" class="btn btn-outline-danger w-100">
                                    {{ __('messages.model.stamina.stamina.action.accept') }}
                                </button>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </form>
    @endif
</div>
