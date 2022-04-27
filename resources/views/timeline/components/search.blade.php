<form action="{{ $url }}" method="get">
    <div class="bg-white p-6 shadow-sm rounded-lg justify-between items-center">
        <div class="px-6 py-4 whitespace-nowrap">
            <label for="select-action" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                Action
            </label>
            <select
                id="select-action"
                name="actions[]"
                autocomplete="off"
                class="block w-full rounded-lg cursor-pointer focus:outline-none"
                multiple
            >
                @foreach($actions as $action)
                    @if(isset(request()->actions) && in_array($action, request()->actions))
                        <option value="{{ $action }}" selected>{{ __('messages.action.'. $action) }}</option>
                    @else
                        <option value="{{ $action }}">{{ __('messages.action.'. $action) }}</option>
                    @endif
                @endforeach
            </select>
        </div>

        <div class="px-6 py-4 whitespace-nowrap">
            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                TimeSpan
            </label>
            <div class="flex flex-wrap">
                <div class="whitespace-nowrap">
                    <input type="datetime-local" name="beginAt" value="{{ (request()->beginAt ? DateTime::createFromFormat('Y-m-d\TH:i', request()->beginAt, new DateTimeZone($timezone)) : (new DateTime($gcp->beginAt, new DateTimeZone($timezone))))->format('Y-m-d\TH:i') }}" class="border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                </div>
                <div class="px-6 py-3.5 whitespace-nowrap">
                〜
                </div>
                <div class="whitespace-nowrap">
                    <input type="datetime-local" name="endAt" value="{{ (request()->endAt ? DateTime::createFromFormat('Y-m-d\TH:i', request()->endAt, new DateTimeZone($timezone)) : (new DateTime($gcp->endAt, new DateTimeZone($timezone))))->format('Y-m-d\TH:i') }}" class="border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                </div>
            </div>
        </div>

        <div class="px-6 whitespace-nowrap">
            <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                {{ __('messages.model.players.action.search') }}
            </button>
        </div>
    </div>
</form>
<script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>
<script>
    new TomSelect('#select-action');
</script>

