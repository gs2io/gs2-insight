@foreach($experiences as $experience)
    <div class="bg-gray-100 shadow-sm rounded-lg justify-between items-center">
        <div class="flex justify-between items-center">
            <div class="flex flex-wrap">
                <div class="px-6 py-4 whitespace-nowrap">
                    <p class="text-xl font-bold text-gray-500">{{ $experience->experienceModelName }}</p>
                </div>
            </div>
        </div>
    </div>
    {{ $experience->currentStatusesView('experience/components/namespace/user/experience/status/current_list') }}
@endforeach
