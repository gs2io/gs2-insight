@foreach($messages as $message)
    <div class="bg-gray-100 shadow-sm rounded-lg justify-between items-center">
        <div class="flex justify-between items-center">
            <div class="flex flex-wrap">
                <div class="px-6 py-4 whitespace-nowrap">
                    <p class="text-xl font-bold text-gray-500">{{ $message->messageName }}</p>
                </div>
            </div>
        </div>
    </div>
    <div class="p-4">
        {{ $message->timelineView('commons/timeline') }}
    </div>
@endforeach
<div class="pt-4">
    {{ $messages->appends(request()->input())->links() }}
</div>
