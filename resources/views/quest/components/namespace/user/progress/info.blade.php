<div class="bg-gray-100 shadow-sm rounded-lg justify-between items-center">
    <div class="flex justify-between items-center">
        <div class="flex flex-wrap">
            <div class="px-6 py-4 whitespace-nowrap">
                <p class="text-xl font-bold text-gray-500">{{ __('messages.model.quest.progress') }}</p>
            </div>
        </div>
        @if(isset($progress->progress))
        <div class="flex pr-4">
            <form action="{{ "/players/{$progress->user->userId}/quest/{$progress->user->namespace->namespaceName}/progress/complete" }}" method="post">
                @csrf
                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-400 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    {{ __('messages.model.quest.progress.action.complete') }}
                </button>
            </form>
            <div class="p-1"></div>
            <form action="{{ "/players/{$progress->user->userId}/quest/{$progress->user->namespace->namespaceName}/progress/failed" }}" method="post">
                @csrf
                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-yellow-400 hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                    {{ __('messages.model.quest.progress.action.failed') }}
                </button>
            </form>
            @if($permission != 'operator')
            <div class="p-1"></div>
            <form action="{{ "/players/{$progress->user->userId}/quest/{$progress->user->namespace->namespaceName}/progress/delete" }}" method="post">
                @csrf
                <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
                    <div class="btn-group" role="group">
                        <button id="progressDelete" type="button" class="btn btn-danger dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            {{ __('messages.model.quest.progress.action.delete') }}
                        </button>
                        <ul class="dropdown-menu p-0" style="min-width: 0" aria-labelledby="progressDelete">
                            <li>
                                <button type="submit" class="btn btn-outline-danger w-100">
                                    {{ __('messages.model.quest.progress.action.accept') }}
                                </button>
                            </li>
                        </ul>
                    </div>
                </div>
            </form>
            @endif
        </div>
        @endif
    </div>
</div>
@if(isset($progress->progress))
<div class="flex flex-col">
    <div class="-my-2 overflow-x-auto">
        <div class="py-2 align-middle inline-block min-w-full">
            <table class="min-w-full divide-y divide-gray-200">
                <tr>
                    <th scope="col" class="px-6 py-3 w-25 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        {{ __('messages.model.quest.progress.transactionId') }}
                    </th>
                    <td class="px-6 py-4 whitespace-nowrap">
                        {{ $progress->progress->getTransactionId() }}
                    </td>
                </tr>
                <tr>
                    <th scope="col" class="px-6 py-3 w-25 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        {{ __('messages.model.quest.progress.questModelId') }}
                    </th>
                    <td class="px-6 py-4 whitespace-nowrap">
                        {{ $progress->progress->getQuestModelId() }}
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>
@else
<div class="p-4">
    {{ __('messages.model.quest.progress.notFound') }}
</div>
@endif
