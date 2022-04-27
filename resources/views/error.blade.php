@extends('base')
@section('content')
    @include('commons.language')
    @include('commons.breadcrumb', [
        'hierarchy' => [
            'Home' => url('/'),
        ]
    ])
    <div class="p-8">
        <div class="m-4 bg-white p-6 shadow-sm rounded-lg justify-between items-center">
        @foreach($errors->getErrors() as $error)
            <p>
                {{ __('messages.error.'. $error->getMessage()) }}
            </p>
        @endforeach
        </div>
    </div>
@endsection
