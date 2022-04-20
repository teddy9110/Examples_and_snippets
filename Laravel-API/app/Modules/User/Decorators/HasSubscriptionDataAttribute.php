<?php

namespace Rhf\Modules\User\Decorators;

use Carbon\Carbon;
use Rhf\Modules\User\Models\UserSubscriptions;

trait HasSubscriptionDataAttribute
{
    /**
     * Retrieve user's subscription information.
     */
    public function getSubscriptionDataAttribute()
    {
        return [
            'expiry_info' => $this->getExpiryInfo(),
        ];
    }

    /**
     * Get expiry info based on subscription type.
     *
     * @return string
     */
    private function getExpiryInfo()
    {
        switch ($this->getAttribute('subscription')->subscription_provider ?? null) {
            case UserSubscriptions::SUBSCRIPTION_TYPE_APPLE:
            case UserSubscriptions::SUBSCRIPTION_TYPE_SHOPIFY:
                return $this->getAnnualSubscriptionExpiryInfo();

            case UserSubscriptions::SUBSCRIPTION_TYPE_SMARTDEBIT:
            case UserSubscriptions::SUBSCRIPTION_TYPE_ASHBOURNE:
                return $this->getDirectDebitExpiryInfo($this->getAttribute('subscription'));

            case UserSubscriptions::SUBSCRIPTION_TYPE_GOCARDLESS:
                return $this->getGoCardlessSubscriptionExpiryInfo();
            case UserSubscriptions::SUBSCRIPTION_TYPE_FREE:
                return $this->getFreeSubscriptionExpiryInfo();
            default:
                return $this->getNoSubscriptionExpiryInfo();
        }
    }

    /**
     * Expiry info for no subscription data.
     *
     * @return string
     */
    private function getNoSubscriptionExpiryInfo()
    {
        /** @var Carbon $expiryDate */
        $expiryDate = $this->getAttribute('expiry_date');
        $setTime = $this->getTimeSetterMethod($expiryDate);
        if ($expiryDate->{$setTime}()->gt(now()->{$setTime}())) {
            return "Your subscription ends on {$this->formatExpiryInfoDate($expiryDate)}.";
        }

        return $this->rollingContractMessage();
    }

    /**
     * Expiry info for an annual subscription.
     *
     * @return string
     */
    private function getAnnualSubscriptionExpiryInfo()
    {
        /** @var Carbon $expiryDate */
        $expiryDate = $this->getAttribute('expiry_date');
        return "Your subscription ends on {$this->formatExpiryInfoDate($expiryDate)}.";
    }

    /**
     * Expiry info for GoCardless subscription.
     *
     * @return string
     */
    private function getGoCardlessSubscriptionExpiryInfo()
    {
        return "Rolling monthly contract.";
    }

    /**
     * Expiry info for direct debit based subscription.
     *
     * @param UserSubscriptions $subscription
     * @return string
     */
    private function getDirectDebitExpiryInfo(UserSubscriptions $subscription)
    {
        $subscriptionExpiryDate = Carbon::parse($subscription->expiry_date);
        $setTime = $this->getTimeSetterMethod($subscriptionExpiryDate);
        $subscriptionExpiryDate = $subscriptionExpiryDate->{$setTime}();
        $outOfContract = $subscriptionExpiryDate->lte(now()->{$setTime}());

        if ($outOfContract) {
            return $this->rollingContractMessage();
        }

        return "Your contract ends on {$this->formatExpiryInfoDate($subscriptionExpiryDate)}.";
    }

    /**
     * Get message for a rolling contract, based on next payment date if present.
     *
     * @return string
     */
    private function rollingContractMessage()
    {
        $nextPaymentDate = $this->getAttribute('next_payment_date');
        return is_null($nextPaymentDate) ?
            "Rolling monthly contract." :
            "Rolling monthly contract. Next payment on {$this->formatExpiryInfoDate($nextPaymentDate)}.";
    }

    /**
     * Date format helper.
     *
     * @param mixed $date
     * @return string
     */
    private function formatExpiryInfoDate(Carbon $date)
    {
        return $date->format('d/m/Y');
    }

    /**
     * Get time setter method based on the hour of day.
     *
     * @param Carbon $date
     * @return string
     */
    private function getTimeSetterMethod(Carbon $date)
    {
        if ($date->hour == 0) {
            return 'startOfDay';
        }
        return 'endOfDay';
    }
    /**
     * Expiry info for free subscription data.
     *
     * @return string
     */
    private function getFreeSubscriptionExpiryInfo()
    {
        /** @var Carbon $expiryDate */
        $expiryDate = $this->getAttribute('expiry_date');
        return "Your subscription ends on {$this->formatExpiryInfoDate($expiryDate)}.";
    }
}
