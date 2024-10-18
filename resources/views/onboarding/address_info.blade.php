@extends('layout')

@section('content')
<h2 class="mb-4">{{ __("Address Information") }}</h2>
@if (session('error'))
<div class="alert alert-danger">
    {{ session('error') }}
</div>
@endif
<form action="{{ route('onboarding.address_info') }}" method="POST">
    @csrf

    <div class="mb-3">
        <label for="country" class="form-label">{{ __("Country") }}</label>
        <select class="form-select @error('country') is-invalid @enderror" id="country" name="country" required>
            <option value="" disabled selected>{{ __("Select your country") }}</option>
            @foreach ($countries as $code => $name)
            <option value="{{ $code }}" {{ old('country', $addressInfo['country']) == $code ? 'selected' : '' }}>
                {{ $name }}
            </option>
            @endforeach
        </select>
        @error('country')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="address_line1" class="form-label">{{ __("Address Line 1") }}</label>
        <input type="text" class="form-control @error('address_line1') is-invalid @enderror" name="address_line1" value="{{ old('address_line1', $addressInfo['address_line1']) }}" required>
        @error('address_line1')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="address_line2" class="form-label">{{ __("Address Line 2") }}</label>
        <input type="text" class="form-control @error('address_line2') is-invalid @enderror" name="address_line2" value="{{ old('address_line2', $addressInfo['address_line2'] ?? '') }}">
        @error('address_line2')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="city" class="form-label">{{ __("City") }}</label>
        <input type="text" class="form-control @error('city') is-invalid @enderror" name="city" value="{{ old('city', $addressInfo['city']) }}" required>
        @error('city')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3" id="state-container">
        <label for="state" class="form-label" id="state-label">{{ __("State/Province") }}</label>
        <input type="text" class="form-control @error('state') is-invalid @enderror" name="state" value="{{ old('state', $addressInfo['state']) }}">
        @error('state')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3" id="postal-code-container">
        <label for="postal_code" class="form-label" id="postal-code-label">{{ __("Postal Code") }}</label>
        <input type="text" class="form-control @error('postal_code') is-invalid @enderror" name="postal_code" value="{{ old('postal_code', $addressInfo['postal_code']) }}" required>
        @error('postal_code')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="d-flex justify-content-between">
        <a href="{{ route('onboarding.user_info') }}" class="btn btn-secondary">{{ __("Previous") }}</a>
        <button type="submit" class="btn btn-primary">{{ __("Next") }}</button>
    </div>
</form>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const countrySelect = document.getElementById('country');
        const stateContainer = document.getElementById('state-container');
        const stateLabel = document.getElementById('state-label');
        const postalCodeContainer = document.getElementById('postal-code-container');
        const postalCodeLabel = document.getElementById('postal-code-label');

        const addressFormats = @json(config('address_formats'));

        function updateAddressFields(countryCode) {
            const format = addressFormats[countryCode] || addressFormats['default'];

            if (format.state_required) {
                stateContainer.style.display = 'block';
                stateLabel.textContent = format.state_label || '{{ __("State/Province") }}';
            } else {
                stateContainer.style.display = 'none';
            }

            postalCodeLabel.textContent = format.postal_code_label || '{{ __("Postal Code") }}';
        }

        updateAddressFields(countrySelect.value);

        countrySelect.addEventListener('change', function() {
            updateAddressFields(countrySelect.value);
        });
    });
</script>
@endsection