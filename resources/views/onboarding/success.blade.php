@extends('layout')

@section('content')
    <div class="container">
        <h1>{{__("Subscription Successful!")}}</h1>
        <h1>{{__("Your subscription has been successfully processed.")}}</h1>
        <a href="{{ url('/') }}" class="btn btn-primary mt-4">{{ __("Go to Homepage") }}</a>

    </div>
@endsection
