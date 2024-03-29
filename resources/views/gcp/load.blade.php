@extends('base')
@section('content')
<div class="p-8">
<div class="bg-white p-6 shadow-sm rounded-lg justify-between items-center">

    <div class="px-6 py-4 whitespace-nowrap">
        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">
            {{ __('messages.model.gcp.properties.datasetName') }}
        </label>
        <div class="w-full">
            {{ $gcp->datasetName }}
        </div>
    </div>

    <div class="px-6 py-4 whitespace-nowrap">
        {{ floor($workingStatus->progress * 10000) / 100 }} %
    </div>

    <script>
        setTimeout(() => {
            location.reload()
        }, 1000);
    </script>
</div>
</div>
@endsection
