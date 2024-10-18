@extends('layout')

@section('content')
<h2 class="mb-4">{{ __("Confirmation") }}</h2>

@if (session('error'))
<div class="alert alert-danger">
    {{ session('error') }}
</div>
@endif

<h3>{{ __("User Information") }}</h3>
<p>{{ __("Name") }}: {{ $confirmationInfo['user_info']['name'] }}</p>
<p>{{ __("Email") }}: {{ $confirmationInfo['user_info']['email'] }}</p>
<p>{{ __("Phone") }}: {{ $confirmationInfo['user_info']['phone'] }}</p>
<p>{{ __("Subscription Type") }}: {{ $confirmationInfo['user_info']['subscription_type'] }}</p>

<h3>{{ __("Address Information") }}</h3>
<p>{{ __("Address Line 1") }}: {{ $confirmationInfo['address_info']['address_line1'] }}</p>
<p>{{ __("City") }}: {{ $confirmationInfo['address_info']['city'] }}</p>
<p>{{ __("Postal Code") }}: {{ $confirmationInfo['address_info']['postal_code'] }}</p>
<p>{{ __("State") }}: {{ $confirmationInfo['address_info']['state'] }}</p>
<p>{{ __("Country") }}: {{ $confirmationInfo['address_info']['country'] }}</p>

@if ($confirmationInfo['user_info']['subscription_type'] == 'premium')
<h3>{{ __("Payment Information") }}</h3>
<p>{{ __("Credit Card") }}: **** **** **** {{ substr($confirmationInfo['payment_info']['credit_card_number'], -4) }}</p>
<p>{{ __("Expiration Date") }}: {{ $confirmationInfo['payment_info']['expiration_date'] }}</p>
@endif

<form action="{{ route('onboarding.confirmation') }}" method="POST">
    @csrf
    <div class="d-flex justify-content-between">
        @if (session('user_info.subscription_type') === 'premium')
        <a href="{{ route('onboarding.payment_info') }}" class="btn btn-secondary">{{ __("Previous") }}</a>
        @else
        <a href="{{ route('onboarding.address_info') }}" class="btn btn-secondary">{{ __("Previous") }}</a>
        @endif

        <button type="submit" class="btn btn-success">{{ __("Confirm and Submit") }}</button>
    </div>
</form>
@endsection