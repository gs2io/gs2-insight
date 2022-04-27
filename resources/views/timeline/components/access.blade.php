<div class="flex timeline-content">
    <div class="flex flex-col items-center mr-4">
        <div class="w-px h-1.5 bg-gray-300"></div>
        <div>
            <div class="flex items-center justify-center w-5 h-5 bg-gray-300 rounded-full">
            </div>
        </div>
        <div class="w-px h-full bg-gray-300"></div>
    </div>
    <div class="pb-5">
        <p class="mb-2 text-xl font-bold text-gray-400">{{ $timeline->timestamp }}</p>
        <p class="text-gray-700">
            <div class="bg-gray-100 shadow-sm rounded-lg">
                <div class="p-5 flex justify-between items-center">
                    <div class="whitespace-nowrap">
                        <p class="text-xl font-bold text-gray-500">{{ __('messages.action.' . str_replace('ByStampTask', '', str_replace('ByStampSheet', '', str_replace('ByUserId', '', $timeline->action)))) }}</p>
                    </div>
                    <a href="{{ url()->current(). '/timelines/'. $timeline->transactionId }}" target="_blank" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-gray-600 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        {{ __('messages.model.stampSheet.action.detail') }}
                    </a>
                </div>
                <div class="bg-white p-5 justify-between items-center">
                    @include('commons.arguments', ['args' => json_decode($timeline->args, true)])
                </div>
                <div class="p-1"></div>
            </div>
        </p>
    </div>
</div>
