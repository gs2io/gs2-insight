@foreach($namespaces as $namespace)
    <div class="bg-gray-100 shadow-sm rounded-lg justify-between items-center">
        <div class="flex justify-between items-center">
            <div class="flex flex-wrap">
                <div class="px-6 py-4 whitespace-nowrap">
                    <p class="text-xl font-bold text-gray-500">{{ $namespace->namespaceName }}</p>
                </div>
            </div>
            <div class="pr-4">
                <a href="{{ url()->current() . "/lottery/$namespace->namespaceName" }}" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    {{ __('messages.model.lottery.namespace.action.detail') }}
                </a>
            </div>
        </div>
    </div>
@endforeach
<div class="pt-4">
    {{ $namespaces->appends(request()->input())->links() }}
</div>
