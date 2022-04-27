@if(isset($wallet->wallet))
<div class="flex flex-col">
    <div class="-my-2 overflow-x-auto">
        <div class="py-2 align-middle inline-block min-w-full">
            <div class="overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('messages.model.money.wallet.balance') }}
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('messages.model.money.wallet.detail') }}
                        </th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <p>
                                {{ $wallet->wallet->getFree() + $wallet->wallet->getPaid() }}
                            </p>
                            <p>
                                &nbsp;{{ __('messages.model.money.wallet.paid') }}: {{ $wallet->wallet->getPaid() }}
                            </p>
                        </td>
                        <td class="whitespace-nowrap">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('messages.model.money.walletDetail.price') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('messages.model.money.walletDetail.count') }}
                                    </th>
                                </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($wallet->wallet->getDetail() as $detail)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ $detail->getPrice() }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ $detail->getCount() }}
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endif
{{ $wallet->user->walletControllerView('money/components/namespace/user/wallet/controller') }}
