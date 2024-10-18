@extends('layout')

@section('content')
<h2 class="mb-4">{{ __("Payment Information") }}</h2>
@if (session('error'))
<div class="alert alert-danger">
    {{ session('error') }}
</div>
@endif
<form action="{{ route('onboarding.payment_info') }}" method="POST">
    @csrf

    <div class="mb-3">
        <label for="credit_card_number" class="form-label">{{ __("Credit Card Number") }}</label>
        <input type="text" class="form-control @error('credit_card_number') is-invalid @enderror" name="credit_card_number" value="{{ old('credit_card_number', $paymentInfo['credit_card_number']) }}" placeholder="{{ __('Enter your credit card number') }}" required>
        @error('credit_card_number')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="expiration_date" class="form-label">{{ __("Expiration Date") }} (MM/YY)</label>
        <input type="text" class="form-control @error('expiration_date') is-invalid @enderror" name="expiration_date" value="{{ old('expiration_date', $paymentInfo['expiration_date']) }}" placeholder="{{ __('Enter expiration date (MM/YY)') }}" required>
        @error('expiration_date')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="cvv" class="form-label">{{ __("CVV") }}</label>
        <input type="text" class="form-control @error('cvv') is-invalid @enderror" name="cvv" value="{{ old('cvv', $paymentInfo['cvv']) }}" placeholder="{{ __('Enter CVV') }}" required>
        @error('cvv')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="d-flex justify-content-between">
        <a href="{{ route('onboarding.address_info') }}" class="btn btn-secondary">{{ __("Previous") }}</a>
        <button type="submit" class="btn btn-primary">{{ __("Next") }}</button>
    </div>
</form>
@endsection