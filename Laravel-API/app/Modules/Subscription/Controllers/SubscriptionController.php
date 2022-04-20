<?php

namespace Rhf\Modules\Subscription\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Rhf\Exceptions\FitnessBadRequestException;
use Rhf\Http\Controllers\Controller;
use Rhf\Modules\Subscription\Models\Subscription;
use Rhf\Modules\Subscription\Resources\SubscriptionResource;
use Rhf\Modules\User\Models\User;
use Rhf\Modules\User\Models\UserSubscriptions;
use Rhf\Modules\User\Resources\UserResource;

class SubscriptionController extends Controller
{
    /**
     * Returns a list of available Apple renewable subscription product ID's
     *
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function availableSubscriptions(Request $request)
    {
        $availableSubscriptions = Subscription::all();
        return SubscriptionResource::collection($availableSubscriptions);
    }

    /**
     * Returns information on a specific subscription
     *
     * @param Request $request
     * @param $productId
     * @return SubscriptionResource
     */
    public function retrieveSubscription(Request $request, $productId)
    {
        $subscription = Subscription::where('product_id', $productId)->firstOrFail();
        return new SubscriptionResource($subscription);
    }

    /**
     * Updates a subscription for a user
     *
     * @param Request $request
     * @return UserResource
     */
    public function applySubscription(Request $request)
    {
        /** @var \Rhf\Modules\User\Models\User $user */
        $user = auth('api')->user();
        $transactionId = $request->get('transaction_id');

        $userTransactionIds = User::where('transaction_id', $transactionId)
            ->where('id', '<>', $user->id)
            ->get();

        if ($userTransactionIds->count() > 0) {
            throw new FitnessBadRequestException(
                'Our records show this subscription is tied to another Team RH Fitness account. '
                    . 'If you think this is incorrect please contact Team RH Fitness. '
                    . 'Transaction ID: ' . $transactionId
            );
        }

        // Set the date to the end of the expiry date past in the request body
        $user->transaction_id = $transactionId;
        $user->expiry_date = Carbon::parse($request->get('expiry_date'))->endOfDay();
        $user->paid = true;
        $user->save();

        $this->userSubscriptionInformation($user, 'standard', 'annual', 'apple');

        return new UserResource(auth('api')->user());
    }

    private function userSubscriptionInformation($user, $plan, $frequency, $provider)
    {
        $exists = UserSubscriptions::where('user_id', $user->id)
            ->where('email', $user->email)
            ->where('subscription_provider', $provider)
            ->first();
        if ($exists) {
            $exists->update([
                'subscription_plan' => $plan,
                'subscription_frequency' => $frequency,
                'expiry_date' => $user->expiry_date,
                'subscription_reference' => $user->transaction_id
            ]);
            $exists->save();
        } else {
            UserSubscriptions::create(
                [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'subscription_provider' => 'apple',
                    'subscription_plan' => $plan,
                    'subscription_frequency' => $frequency,
                    'purchase_date' => now(),
                    'expiry_date' => $user->expiry_date,
                    'subscription_reference' => $user->transaction_id
                ]
            );
        }
    }
}
