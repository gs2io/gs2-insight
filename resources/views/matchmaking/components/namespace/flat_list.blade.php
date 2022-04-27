@foreach($namespaces as $namespace)
    <div class="pb-5">
        <div class="bg-gray-100 shadow-sm rounded-lg">
            <div class="p-5 flex justify-between items-center">
                <div class="whitespace-nowrap">
                    <p class="text-xl font-bold text-gray-500">{{ $namespace->namespaceName }}</p>
                </div>
            </div>
            <div class="bg-white p-5 justify-between items-center">
                {{ $namespace->infoView('matchmaking/components/namespace/info') }}
            </div>
        </div>
    </div>
@endforeach
<div class="mb-4"></div>
{{ $namespaces->appends(request()->input())->links() }}
<div class="p-4"></div>
