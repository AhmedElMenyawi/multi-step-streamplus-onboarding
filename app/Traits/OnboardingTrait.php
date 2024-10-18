<?php

namespace App\Traits;

use Exception;
use App\Models\UserCard;
use App\Models\UserDetail;
use App\Models\Transaction;
use App\Models\UserAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Config;
use Illuminate\Database\QueryException;

trait OnboardingTrait
{
    /**
     * Validate user information from the request and store it in session.
     * This handles basic user info like name, email, phone, and subscription type.
     *
     * @param Request $request
     * @return void
     * @throws Exception
     */
    public function validateAndStoreUserInfo(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'phone' => 'required|numeric|min:10',
                'subscription_type' => 'required|in:free,premium',
            ]);

            session(['user_info' => $validated]);
        } catch (Exception $e) {
            Log::error('Failed to validate and store user info: ' . $e->getMessage());
            throw $e;
        }
    }
    /**
     * Retrieve user information from session.
     * If data is missing from the session, it will return default values.
     *
     * @return array
     * @throws Exception
     */
    public function getUserInfoFromSession()
    {
        try {
            return session('user_info', [
                'name' => '',
                'email' => '',
                'phone' => '',
                'subscription_type' => '',
            ]);
        } catch (Exception $e) {
            Log::error('Failed to retrieve user info from session: ' . $e->getMessage());
            throw $e;
        }
    }
    /**
     * Validate address information from the request and store it in session.
     * This includes dynamic validation based on country-specific address formats.
     *
     * @param Request $request
     * @return void
     * @throws Exception
     */
    public function validateAndStoreAddressInfo(Request $request)
    {
        try {
            $country = $request->input('country');
            $addressFormats = Config::get('address_formats');
            $format = $addressFormats[$country] ?? $addressFormats['default'];

            $rules = [
                'address_line1' => 'required|string|max:255',
                'city' => 'required|string|max:255',
                'country' => 'required|string|max:255',
                'postal_code' => ['required', 'string'],
            ];

            if (!empty($format['postal_code_pattern'])) {
                $rules['postal_code'][] = 'regex:' . $format['postal_code_pattern'];
            }

            if ($format['state_required']) {
                $rules['state'] = 'required|string|max:255';
            }

            $validated = $request->validate($rules);
            session(['address_info' => $validated]);
        } catch (Exception $e) {
            Log::error('Failed to validate and store address info: ' . $e->getMessage());
            throw $e;
        }
    }
    /**
     * Retrieve address information from session.
     * If data is missing, return default values.
     *
     * @return array
     * @throws Exception
     */
    public function getAddressInfoFromSession()
    {
        try {
            return session('address_info', [
                'address_line1' => '',
                'address_line2' => '',
                'city' => '',
                'postal_code' => '',
                'state' => '',
                'country' => '',
            ]);
        } catch (Exception $e) {
            Log::error('Failed to retrieve address info from session: ' . $e->getMessage());
            throw $e;
        }
    }
    /**
     * Validate payment information from the request and store it in session.
     * This handles credit card number, expiration date, and CVV, with basic validation.
     * Also checks that the expiration date is in the future.
     *
     * @param Request $request
     * @return void
     * @throws Exception
     */
    public function validateAndStorePaymentInfo(Request $request)
    {
        try {
            $validated = $request->validate([
                'credit_card_number' => 'required|digits:16',
                'expiration_date' => [
                    'required',
                    'regex:/^(0[1-9]|1[0-2])\/[0-9]{2}$/',
                    function ($attribute, $value, $fail) {
                        $parts = explode('/', $value);

                        if (count($parts) !== 2) {
                            return $fail('Invalid expiration date format.');
                        }

                        [$month, $year] = $parts;
                        $fullYear = '20' . $year;
                        $currentYear = date('Y');
                        $currentMonth = date('m');

                        if ($fullYear < $currentYear || ($fullYear == $currentYear && $month < $currentMonth)) {
                            return $fail('The expiration date must be in the future.');
                        }
                    },
                ],
                'cvv' => 'required|digits:3',
            ]);

            session(['payment_info' => $validated]);
        } catch (Exception $e) {
            Log::error('Failed to validate and store payment info: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Retrieve payment information from session.
     * If data is missing, return default values.
     *
     * @return array
     * @throws Exception
     */
    public function getPaymentInfoFromSession()
    {
        try {
            return session('payment_info', [
                'credit_card_number' => '',
                'expiration_date' => '',
                'cvv' => '',
            ]);
        } catch (Exception $e) {
            Log::error('Failed to retrieve payment info from session: ' . $e->getMessage());
            throw $e;
        }
    }
    /**
     * Finalizes the subscription process.
     * 
     * This function handles the entire subscription flow:
     * 1. It first saves the user details.
     * 2. It then stores the address details for the user.
     * 3. If the subscription type is 'premium', it processes the payment information and saves card details.
     * 4. A transaction record is created, associating the user, address, and optionally card (for premium subscriptions).
     * 5. Session data is cleared after successful completion.
     * 
     * If any step fails, the function returns an error and logs the issue.
     * On success, the user is redirected to a success page.
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function finalizeSubscription(Request $request)
    {
        try {
            $userInfo = $this->prepareUserData();
            $saveUser = UserDetail::create($userInfo);

            if (!$saveUser) {
                return ['success' => false, 'message' => 'There was an issue saving user details. Please try again.'];
            }

            $addressInfo = $this->prepareAddressData();
            $saveAddress = UserAddress::create(array_merge($addressInfo, ['user_id' => $saveUser->id]));

            if (!$saveAddress) {
                $saveUser->delete();
                return ['success' => false, 'message' => 'There was an issue saving address details. Please try again.'];
            }

            $cardId = null;

            if ($userInfo['subscription_type'] == 'premium') {
                $paymentInfo = $this->preparePaymentData();
                $saveCard = UserCard::create(array_merge($paymentInfo, ['user_id' => $saveUser->id]));

                if (!$saveCard) {
                    $saveAddress->delete();
                    $saveUser->delete();
                    return ['success' => false, 'message' => 'There was an issue saving card details. Please try again.'];
                }

                $cardId = $saveCard->id;
            }

            $saveTransaction = Transaction::create([
                'user_id' => $saveUser->id,
                'address_id' => $saveAddress->id,
                'card_id' => $cardId
            ]);

            if (!$saveTransaction) {
                if ($cardId) {
                    UserCard::find($cardId)->delete();
                }
                $saveAddress->delete();
                $saveUser->delete();
                return ['success' => false, 'message' => 'There was an issue saving transaction details. Please try again.'];
            }

            session()->forget(['user_info', 'address_info', 'payment_info']);

            return ['success' => true, 'message' => 'Subscription completed successfully!'];
        } catch (QueryException $qe) {
            Log::error('Query error during subscription finalization: ' . $qe->getMessage());
            if (isset($saveTransaction) && $saveTransaction) {
                $saveTransaction->delete();
            }
            if (isset($cardId) && $cardId) {
                UserCard::find($cardId)->delete();
            }
            if (isset($saveAddress) && $saveAddress) {
                $saveAddress->delete();
            }
            if (isset($saveUser) && $saveUser) {
                $saveUser->delete();
            }

            return ['success' => false, 'message' => 'There was an issue completing your subscription. Please try again.'];
        } catch (Exception $e) {
            Log::error('Failed to finalize subscription: ' . $e->getMessage());

            if (isset($saveTransaction) && $saveTransaction) {
                $saveTransaction->delete();
            }
            if (isset($cardId) && $cardId) {
                UserCard::find($cardId)->delete();
            }
            if (isset($saveAddress) && $saveAddress) {
                $saveAddress->delete();
            }
            if (isset($saveUser) && $saveUser) {
                $saveUser->delete();
            }

            return ['success' => false, 'message' => 'There was an issue completing your subscription. Please try again.'];
        }
    }


    private function prepareUserData()
    {
        $userInfo = session('user_info');

        return [
            'name' => $userInfo['name'] ?? null,
            'email' => $userInfo['email'] ?? null,
            'phone' => $userInfo['phone'] ?? null,
            'subscription_type' => $userInfo['subscription_type'] ?? null,
        ];
    }

    private function prepareAddressData()
    {
        $addressInfo = session('address_info');

        return [
            'address_line1' => $addressInfo['address_line1'] ?? null,
            'address_line2' => $addressInfo['address_line2'] ?? null,
            'city' => $addressInfo['city'] ?? null,
            'state' => $addressInfo['state'] ?? null,
            'postal_code' => $addressInfo['postal_code'] ?? null,
            'country' => $addressInfo['country'] ?? null,
        ];
    }

    private function preparePaymentData()
    {
        $paymentInfo = session('payment_info');

        return [
            'credit_card_number' => isset($paymentInfo['credit_card_number']) ? Crypt::encrypt($paymentInfo['credit_card_number']) : null,
            'expiration_month' => isset($paymentInfo['expiration_date']) ? (int) explode('/', $paymentInfo['expiration_date'])[0] : null,
            'expiration_year' => isset($paymentInfo['expiration_date']) ? (int) explode('/', $paymentInfo['expiration_date'])[1] : null,
            'cvv' => isset($paymentInfo['cvv']) ? Crypt::encrypt($paymentInfo['cvv']) : null,
        ];
    }
}
