<?php

namespace Rhf\Modules\Admin\Controllers;

use Rhf\Http\Controllers\Controller;
use Rhf\Modules\Admin\Requests\AdminCreateSubscriptionRequest;
use Rhf\Modules\Admin\Requests\AdminSubscriptionRequest;
use Rhf\Modules\Admin\Resources\AdminUserSubscriptionResource;
use Rhf\Modules\User\Models\User;
use Rhf\Modules\User\Models\UserSubscriptions;
use Illuminate\Support\Carbon;

class AdminSubscriptionController extends Controller
{
    public function createSubscription(AdminCreateSubscriptionRequest $request)
    {
        $expiry = Carbon::parse($request->json('expiry_date'))->startOfDay()->toDateTimeString();
        $user = User::find($request->json('user_id'));
        $sub = $user->subscriptions()->create([
            'email' => $user->email,
            'subscription_provider' => $request->json('subscription_provider'),
            'subscription_frequency' => $request->json('subscription_frequency'),
            'purchase_date' => now(),
            'expiry_date' => $expiry,
        ]);
        $user->expiry_date = $expiry;
        $user->save();
        return $sub;
    }

    public function index(AdminSubscriptionRequest $request)
    {
        return AdminUserSubscriptionResource::collection(
            User::find($request->input('user_id'))
                ->subscriptions()
                ->get()
        );
    }
}
