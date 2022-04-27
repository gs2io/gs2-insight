<div class="bg-gray-100 shadow-sm rounded-lg">
    <div class="p-6 justify-between items-center">
        @include('experience/components/namespace/experienceModel/search', [
            'url' => url()->current(),
        ])
    </div>
    <div class="bg-white p-6 justify-between items-center">
        <div class="flex flex-col">
            <div class="-my-2 overflow-x-auto">
                <div class="py-2 align-middle inline-block min-w-full">
                    <div class="overflow-hidden">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('messages.model.experience.experienceModel.name') }}
                                </th>
                                <th scope="col" class="relative px-6 py-3">
                                </th>
                            </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($experienceModels as $experienceModel)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            {{ $experienceModel->experienceModelName }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ url()->current() . "/experienceModel/$experienceModel->experienceModelName" }}" class="text-indigo-600 hover:text-indigo-900">
                                            {{ __('messages.model.experience.experienceModel.action.detail') }}
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        {{ $experienceModels->appends(request()->input())->links() }}
    </div>
    <div class="p-1"></div>
</div>
