@if(isset($inventory->inventory))
<div class="p-4">
    <div class="flex flex-col">
        <div class="-my-2 overflow-x-auto">
            <div class="py-2 align-middle inline-block min-w-full">
                <div class="overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <tr>
                            <th scope="col" class="w-25 bg-gray-50 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('messages.model.inventory.inventory.capacity') }}
                            </th>
                            <td class="w-75 px-6 py-4 whitespace-nowrap">
                                {{ $inventory->inventory->getCurrentInventoryCapacityUsage() }} / {{ $inventory->inventory->getCurrentInventoryMaxCapacity() }}
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
{{ $inventory->controllerView('inventory/components/namespace/user/inventory/controller') }}
