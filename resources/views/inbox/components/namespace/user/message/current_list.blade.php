<div class="flex flex-col">
    <div class="-my-2 overflow-x-auto">
        <div class="py-2 align-middle inline-block min-w-full">
            <div class="overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('messages.model.inbox.message.name') }}
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('messages.model.inbox.message.metadata') }}
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('messages.model.inbox.message.isRead') }}
                        </th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($messages as $message)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ $message->message->getName() }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ $message->message->getMetadata() }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ $message->message->getIsRead() ?  __('messages.model.inbox.message.isRead.true') :  __('messages.model.inbox.message.isRead.false') }}
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
{{ $user->messageControllerView('inbox/components/namespace/user/message/controller') }}
