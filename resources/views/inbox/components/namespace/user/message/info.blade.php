@if(isset($message->message))
<div class="flex flex-col">
    <div class="-my-2 overflow-x-auto">
        <div class="py-2 align-middle inline-block min-w-full">
            <table class="min-w-full divide-y divide-gray-200">
                <tr>
                    <th scope="col" class="px-6 py-3 w-25 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        {{ __('messages.model.inbox.message.name') }}
                    </th>
                    <td class="px-6 py-4 whitespace-nowrap">
                        {{ $message->message->getName() }}
                    </td>
                </tr>
                <tr>
                    <th scope="col" class="px-6 py-3 w-25 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        {{ __('messages.model.inbox.message.metadata') }}
                    </th>
                    <td class="px-6 py-4 whitespace-nowrap">
                        {{ $message->message->getMetadata() }}
                    </td>
                </tr>
                <tr>
                    <th scope="col" class="px-6 py-3 w-25 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        {{ __('messages.model.inbox.message.isRead') }}
                    </th>
                    <td class="px-6 py-4 whitespace-nowrap">
                        {{ $message->message->getIsRead() ?  __('messages.model.inbox.message.isRead.true') :  __('messages.model.inbox.message.isRead.false') }}
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>
@endif
{{ $message->user->messageControllerView('inbox/components/namespace/user/message/controller') }}
