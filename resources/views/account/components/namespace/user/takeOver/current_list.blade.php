<div class="flex flex-col">
    <div class="-my-2 overflow-x-auto">
        <div class="py-2 align-middle inline-block min-w-full">
            <div class="overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('messages.model.account.takeOver.type') }}
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('messages.model.account.takeOver.userIdentifier') }}
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        </th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($takeOvers as $takeOver)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ $takeOver->takeOver->getType() }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ $takeOver->takeOver->getUserIdentifier() }}
                            </td>
                            <td>
                                @if($permission != 'operator')
                                    <div class="btn-group" role="group">
                                        <button id="dataOwnerDelete" type="button" class="btn btn-danger dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                            {{ __('messages.model.account.takeOver.action.delete') }}
                                        </button>
                                        <ul class="dropdown-menu p-0" style="min-width: 0" aria-labelledby="dataOwnerDelete">
                                            <li>
                                                <form action="{{ "/players/{$user->userId}/account/{$user->namespace->namespaceName}/takeOver/{$takeOver->takeOver->getType()}/{$takeOver->takeOver->getUserIdentifier()}/delete" }}" method="post">
                                                    @csrf
                                                    <button type="submit" class="btn btn-outline-danger w-100">
                                                        {{ __('messages.model.account.takeOver.action.accept') }}
                                                    </button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
{{ $user->takeOverControllerView('account/components/namespace/user/takeOver/controller') }}
