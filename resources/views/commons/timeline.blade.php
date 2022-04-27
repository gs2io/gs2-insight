<div class="flex flex-col">
    <div class="-my-2 overflow-x-auto">
        <div class="py-2 align-middle inline-block min-w-full">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        {{ __('messages.model.timestamp') }}
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        {{ __('messages.model.method') }}
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        {{ __('messages.model.args') }}
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    </th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                @foreach ($timeline as $event)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ $event['timestamp'] }}
                        </td>
                        @if($event->isAccessLog())
                            <?php $actionName = str_replace('ByStampTask', '', str_replace('ByStampSheet', '', str_replace('ByUserId', '', $event['action']))) ?>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ __('messages.action.'. $actionName) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if(in_array('stampSheet', array_keys(json_decode($event->accessLog()['request'], true))))
                                    @include('commons.arguments', ['args' => json_decode(json_decode(json_decode(json_decode($event->accessLog()['request'], true)['stampSheet'], true)['body'], true)['args'], true), 'ignoreFields' => ['namespaceName' => true, 'messageName' => true, 'password' => true, 'userId' => true]])
                                @else
                                    @include('commons.arguments', ['args' => json_decode($event->accessLog()['request'], true), 'ignoreFields' => ['namespaceName' => true, 'messageName' => true, 'password' => true, 'userId' => true]])
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <a href="{{ '/players/'. $event->userId. '/timelines/'. $event->transactionId }}" class="text-indigo-600 hover:text-indigo-900">Detail</a>
                            </td>
                        @else
                            <?php $actionName = str_replace('ByStampTask', '', str_replace('ByStampSheet', '', str_replace('ByUserId', '', $event->issueStampSheetLog()['action']))) ?>
                            @if(str_starts_with($actionName, 'Gs2Chat:'))
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{ __('messages.action.'. $actionName) }}
                                </td>
                            @elseif($event['taskAction'])
                                <?php $actionName = str_replace('ByStampTask', '', str_replace('ByStampSheet', '', str_replace('ByUserId', '', $event['taskAction']))) ?>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{ __('messages.action.'. $actionName) }}
                                </td>
                            @else
                                <?php $actionName = str_replace('ByStampTask', '', str_replace('ByStampSheet', '', str_replace('ByUserId', '', $event['action']))) ?>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{ __('messages.action.'. $actionName) }}
                                </td>
                            @endif
                            <td class="px-6 py-4 whitespace-nowrap">
                                <a href="{{ '/players/'. $event->userId. '/timelines/'. $event->transactionId }}" class="text-indigo-600 hover:text-indigo-900">Detail</a>
                            </td>
                        @endif
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
{{ $timeline->appends(request()->input())->links() }}
