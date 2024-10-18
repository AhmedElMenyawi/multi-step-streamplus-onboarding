@extends('layout')

@section('content')
<h2 class="mb-4">{{ __("User Information") }}</h2>
@if (session('error'))
<div class="alert alert-danger">
    {{ session('error') }}
</div>
@endif
<form action="{{ route('onboarding.user_info') }}" method="POST">
    @csrf

    <div class="mb-3">
        <label for="name" class="form-label">{{ __("Name") }}</label>
        <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name', $userInfo['name']) }}" placeholder="{{ __('Enter your name') }}" required>
        @error('name')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="email" class="form-label">{{ __("Email") }}</label>
        <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email', $userInfo['email']) }}" placeholder="{{ __('Enter your email') }}" required>
        @error('email')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="phone" class="form-label">{{ __("Phone Number") }}</label>
        <input type="text" class="form-control @error('phone') is-invalid @enderror" name="phone" value="{{ old('phone', $userInfo['phone']) }}" placeholder="{{ __('Enter your phone number') }}" required>
        @error('phone')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="subscription_type" class="form-label">{{ __("Subscription Type") }}</label>
        <select class="form-select @error('subscription_type') is-invalid @enderror" name="subscription_type" required>
            <option value="" disabled selected>{{ __("Select Type") }}</option>
            <option value="free" {{ old('subscription_type', $userInfo['subscription_type']) == 'free' ? 'selected' : '' }}>{{ __("Free") }}</option>
            <option value="premium" {{ old('subscription_type', $userInfo['subscription_type']) == 'premium' ? 'selected' : '' }}>{{ __("Premium") }}</option>
        </select>
        @error('subscription_type')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <button type="submit" class="btn btn-primary">{{ __("Next") }}</button>
</form>
@endsection