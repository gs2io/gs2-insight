@foreach($awaits as $await)
    {{ $await->infoView('exchange/components/namespace/user/await/info') }}
@endforeach
<div class="mb-4"></div>
{{ $entries->appends(request()->input())->links() }}

{{ $user->rateControllerView('exchange/components/namespace/user/await/controller') }}
