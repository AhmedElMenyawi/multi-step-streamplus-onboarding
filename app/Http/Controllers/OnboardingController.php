<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use App\Traits\OnboardingTrait;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class OnboardingController extends Controller
{
    use OnboardingTrait;

    public function getUserInfo()
    {
        try {
            $userInfo = $this->getUserInfoFromSession();
            return view('onboarding.user_info', compact('userInfo'));
        } catch (Exception $e) {
            Log::error('Error displaying User Info form: ' . $e->getMessage());
            return redirect()->back()->with('error', 'There was an issue loading the user information.');
        }
    }

    public function submitUserInfo(Request $request)
    {
        try {
            $this->validateAndStoreUserInfo($request);
            return redirect()->route('onboarding.address_info');
        } catch (Exception $e) {
            Log::error('Error submitting User Info form: ' . $e->getMessage());
            return redirect()->back()->with('error', 'There was an issue submitting your user information. Please try again.');
        }
    }

    public function getAddressInfo()
    {
        try {
            $countries = Config::get('countries.countries');
            $addressInfo = $this->getAddressInfoFromSession();
            return view('onboarding.address_info', compact('addressInfo', 'countries'));
        } catch (Exception $e) {
            Log::error('Error displaying Address Info form: ' . $e->getMessage());
            return redirect()->back()->with('error', 'There was an issue loading the address information.');
        }
    }

    public function submitAddressInfo(Request $request)
    {
        try {
            $this->validateAndStoreAddressInfo($request);
            $subscriptionType = session('user_info')['subscription_type'] ?? null;
            if ($subscriptionType == 'free') {
                return redirect()->route('onboarding.confirmation');
            }
            return redirect()->route('onboarding.payment_info');
        } catch (Exception $e) {
            Log::error('Error submitting Address Info form: ' . $e->getMessage());
            return redirect()->back()->with('error', 'There was an issue submitting your address information. Please try again.');
        }
    }

    public function getPaymentInfo()
    {
        try {
            $paymentInfo = $this->getPaymentInfoFromSession();
            return view('onboarding.payment_info', compact('paymentInfo'));
        } catch (Exception $e) {
            Log::error('Error displaying Payment Info form: ' . $e->getMessage());
            return redirect()->back()->with('error', 'There was an issue loading the payment information.');
        }
    }

    public function submitPaymentInfo(Request $request)
    {
        try {
            $this->validateAndStorePaymentInfo($request);
            return redirect()->route('onboarding.confirmation');
        } catch (Exception $e) {
            Log::error('Error submitting Payment Info form: ' . $e->getMessage());
            return redirect()->back()->with('error', 'There was an issue submitting your payment information. Please try again.');
        }
    }

    public function getConfirmationInfo()
    {
        try {
            $confirmationInfo = [
                'user_info' => $this->getUserInfoFromSession(),
                'address_info' => $this->getAddressInfoFromSession(),
                'payment_info' => $this->getPaymentInfoFromSession(),
            ];

            return view('onboarding.confirmation', compact('confirmationInfo'));
        } catch (Exception $e) {
            Log::error('Error displaying Confirmation Info page: ' . $e->getMessage());
            return redirect()->back()->with('error', 'There was an issue loading the confirmation information.');
        }
    }

    public function submitSubscription(Request $request)
    {
        try {
            $result = $this->finalizeSubscription($request);
            if ($result['success']) {
                return redirect()->route('onboarding.success')->with('success', $result['message']);
            } else {
                return redirect()->route('onboarding.confirmation')->with('error', $result['message']);
            }
        } catch (Exception $e) {
            Log::error('Error finalizing subscription: ' . $e->getMessage());
            return redirect()->route('onboarding.confirmation')->with('error', 'There was an issue finalizing your subscription. Please try again.');
        }
    }


    public function showSuccessPage()
    {
        return view('onboarding.success');
    }
}
