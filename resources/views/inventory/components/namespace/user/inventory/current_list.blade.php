@foreach($inventories as $inventory)
    <div class="bg-gray-100 shadow-sm rounded-lg justify-between items-center">
        <div class="flex justify-between items-center">
            <div class="flex flex-wrap">
                <div class="px-6 py-4 whitespace-nowrap">
                    <p class="text-xl font-bold text-gray-500">{{ $inventory->inventoryModelName }}</p>
                </div>
            </div>
        </div>
    </div>
    <div class="p-4">
        {{ $inventory->infoView('inventory/components/namespace/user/inventory/info') }}
        {{ $inventory->currentItemsView('inventory/components/namespace/user/inventory/item/current_list') }}
    </div>
@endforeach
