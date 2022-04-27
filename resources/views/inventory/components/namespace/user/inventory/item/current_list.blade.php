<div class="flex flex-col">
    <div class="-my-2 overflow-x-auto">
        <div class="py-2 align-middle inline-block min-w-full">
            <div class="overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('messages.model.inventory.item.itemName') }}
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('messages.model.inventory.item.name') }}
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('messages.model.inventory.item.count') }}
                        </th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($items as $item)
                        @foreach($item->itemSets as $itemSet)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ $item->itemModelName }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ $itemSet->getName() }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ $itemSet->getCount() }}
                            </td>
                        </tr>
                        @endforeach
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
{{ $inventory->itemControllerView('inventory/components/namespace/user/inventory/item/controller') }}
