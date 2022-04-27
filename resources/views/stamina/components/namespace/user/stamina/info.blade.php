@if(isset($stamina->stamina))
<div class="flex flex-col">
    <div class="-my-2 overflow-x-auto">
        <div class="py-2 align-middle inline-block min-w-full">
            <table class="min-w-full divide-y divide-gray-200">
                <tr>
                    <th scope="col" class="px-6 py-3 w-25 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        {{ __('messages.model.stamina.stamina.value') }}
                    </th>
                    <td class="px-6 py-4 whitespace-nowrap">
                        {{ $stamina->stamina->getValue() }} / {{ $stamina->stamina->getMaxValue() }}
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>
{{ $stamina->user->staminaControllerView('stamina/components/namespace/user/stamina/controller') }}
@endif
