<?php

namespace Rhf\Modules\Subscription\Services;

use Exception;
use Illuminate\Support\Str;
use Rhf\Modules\Subscription\Models\AppleSubscriptions;

class AppleSubscriptionService
{
    protected $apple = null;
    protected $user = null;

    /**
     * get the users Apple Subscription
     *
     * @throws Exception
     */
    public function getAppleSubscription()
    {
        if (!isset($this->apple)) {
            throw new Exception('Apple Subscription is not set');
        }
        return $this->apple;
    }

    /**
     * Set the users Apple Subscription
     *
     * @param AppleSubscriptions $appleSubscriptions
     * @return $this
     */
    public function setAppleSubscription(AppleSubscriptions $appleSubscriptions)
    {
        $this->apple = $appleSubscriptions;
        return $this;
    }

    /**
     * Set User
     *
     * @param $user
     * @return $this
     */
    public function setUser($user)
    {
        $this->user = $user;
        $this->apple = $user->apple ?: null;
        return $this;
    }

    /**
     * return User
     *
     * @return null
     * @throws Exception
     */
    public function getUser()
    {
        if (!isset($this->user)) {
            throw new Exception('User is not set');
        }
        return $this->user;
    }

    /**
     * Create a users apple subscription
     *
     * @param array $data
     * @return AppleSubscriptions
     */
    public function createAppleSubscription(array $data)
    {
        $appleSubscription = $this->retrieveAppleSubscription($data) ?: new AppleSubscriptions();

        if (!is_null($this->apple)) {
            $appleSubscription = $this->apple;
        }
        $this->setAppleSubscription($appleSubscription);
        $this->updateAppleSubscription($data);
        return $appleSubscription;
    }

    /**
     * Retrieve apple subscription if the current and original transaction id's match
     *
     * @param $data
     * @return mixed
     */
    public function retrieveAppleSubscription($data)
    {
        return AppleSubscriptions::where('original_transaction_id', $data['original_transaction_id'])
            ->where('current_transaction_id', $data['current_transaction_id'])
            ->first();
    }

    /**
     * Update a notification
     *
     * @param array $data
     */
    public function updateAppleSubscription(array $data)
    {
        $appleSubscription = $this->getAppleSubscription();

        foreach ($appleSubscription->getFillable() as $key) {
            if (isset($data[$key])) {
                $appleSubscription[$key] = $data[$key];
            }
        }
        $appleSubscription->save();
    }

    /**
     * Transform Data
     *
     * @param $data
     * @return array
     */
    public function transformReceiptData($data)
    {
        $purchaseData = [];

        $purchaseData['bundle_id'] = $data->getBundleId();
        $purchaseData['receipt_data'] = $data->getLatestReceipt();

        foreach ($data->getPurchases() as $purchase) {
            $purchaseData['subscription_reference'] = $purchase->getTransactionId();
            $purchaseData['subscription_frequency'] =
                Str::contains($purchase->getProductId(), 'monthly') ? 'monthly' : 'annual';
            $purchaseData['product_id'] = $purchase->getProductId();
            $purchaseData['is_trial'] = $purchase->isTrialPeriod();
            $purchaseData['intro_offer'] = $purchase->isInIntroOfferPeriod();

            if ($purchase->getPurchaseDate() != null) {
                $purchaseData['purchase_date'] = $purchase->getPurchaseDate()->format('Y-m-d H:i:s');
                $purchaseData['expiry_date'] = $purchase->getExpiresDate()->format('Y-m-d H:i:s');
            }
        }
        foreach ($data->getPendingRenewalInfo() as $renewal) {
            $purchaseData['auto-renew'] = $renewal->getAutoRenewStatus();
        }

        foreach ($data->getLatestReceiptInfo() as $latest) {
            $purchaseData['current_transaction_id'] = $latest['transaction_id'];
            $purchaseData['original_transaction_id'] = $latest->getOriginalTransactionId();
        }

        return $purchaseData;
    }

    /**
     * Update User & Subscription
     *
     * @param $data
     */
    public function updateUser($data): void
    {
        $this->user->update(
            [
                'transaction_id' => $data['original_transaction_id'],
                'expiry_date' => $data['expiry_date'],
            ]
        );

        $this->user->subscription->update(
            [
                'subscription_reference' => $data['current_transaction_id'],
                'subscription_frequency' => $data['subscription_frequency'],
                'subscription_provider' => 'apple',
                'purchase_date' => $data['purchase_date'],
                'apple_original_transaction_id' => $data['original_transaction_id'],
                'expiry_date' => $data['expiry_date'],
            ]
        );
    }

    /**
     * Apple Status codes
     *
     * @param $code
     * @return string
     */
    public function statusCode($code)
    {
        switch ($code) {
            case '21000':
                return 'The request to the App Store was not made using the HTTP POST request method.';
            case '21001':
                return 'This status code is no longer sent by the App Store.';
            case '21002':
                return 'The data in the receipt-data property was malformed or the service experienced ' .
                'a temporary issue. Try again.';
            case '21003':
                return 'The receipt could not be authenticated.';
            case '21004':
                return 'The shared secret you provided does not match the shared secret on file for your account.';
            case '21005':
                return 'The receipt server was temporarily unable to provide the receipt. Try again.';
            case '21006':
                return 'This receipt is valid but the subscription has expired. When this status code is returned ' .
                    'to your server, the receipt data is also decoded and returned as part of the response. ' .
                    'Only returned for iOS 6-style transaction receipts for auto-renewable subscriptions.';
            case '21007':
                return 'This receipt is from the test environment, but it was sent to the production environment ' .
                    'for verification.';
            case '21008':
                return 'This receipt is from the production environment, but it was sent to the test environment ' .
                    'for verification.';
            case '21009':
                return 'Internal data access error. Try again later.';
            case '21010':
                return 'The user account cannot be found or has been deleted.';
        }
    }
}
