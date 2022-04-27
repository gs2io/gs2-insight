<div class="flex flex-col">
    <div class="-my-2 overflow-x-auto">
        <div class="py-2 align-middle inline-block min-w-full">
            <div class="overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('messages.model.limit.counter.limitName') }}
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('messages.model.limit.counter.name') }}
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('messages.model.limit.counter.count') }}
                        </th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($counters as $counter)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ $counter->counter->getLimitName() }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ $counter->counter->getName() }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ $counter->counter->getCount() }}
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
{{ $user->counterControllerView('limit/components/namespace/user/limit/counter/controller') }}
