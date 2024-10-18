<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OnboardingController;

Route::get('/', function () {
    return redirect()->route('onboarding.user_info');
})->name('home');

Route::get('/onboarding/user-info', [OnboardingController::class, 'getUserInfo'])->name('onboarding.user_info');
Route::post('/onboarding/user-info', [OnboardingController::class, 'submitUserInfo']);

Route::get('/onboarding/address-info', [OnboardingController::class, 'getAddressInfo'])->name('onboarding.address_info');
Route::post('/onboarding/address-info', [OnboardingController::class, 'submitAddressInfo']);

Route::get('/onboarding/payment-info', [OnboardingController::class, 'getPaymentInfo'])->name('onboarding.payment_info');
Route::post('/onboarding/payment-info', [OnboardingController::class, 'submitPaymentInfo']);

Route::get('/onboarding/confirmation', [OnboardingController::class, 'getConfirmationInfo'])->name('onboarding.confirmation');
Route::post('/onboarding/confirmation', [OnboardingController::class, 'submitSubscription']);

Route::get('/onboarding/success', [OnboardingController::class, 'showSuccessPage'])->name('onboarding.success');
