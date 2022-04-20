<?php

namespace Rhf\Modules\User\Controllers;

use Exception;
use Illuminate\Support\Str;
use Rhf\Exceptions\FitnessBadRequestException;
use Rhf\Http\Controllers\Controller;
use Rhf\Modules\User\Models\User;
use Rhf\Modules\User\Requests\BulkUpdateExpiriesRequest;
use Rhf\Modules\User\Requests\GetUsersByEmailRequest;
use Rhf\Modules\User\Requests\ManagePendingUserRequest;
use Rhf\Modules\User\Requests\MarkUserPaidRequest;

/**
 * NEVER CHANGE THIS UNLESS THERE IS A GOOD REASON TO DO SO.
 *
 * These endpoints are used by the direct debit service to update user subscriptions.
 */
class DirectDebitController extends Controller
{
     /**
     * Create a pending user in the system that can be tested through onboarding.
     * Used to create a user account when they sign up for a life plan membership on store
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function upsertUser(ManagePendingUserRequest $request)
    {
        try {
            // Check for existing user
            $user = User::where('email', '=', $request->get('email'))->first();
            if ($user) {
                $user->expiry_date = date('Y-m-d 23:59:59', strtotime($request->get('expires')));
                $user->paid = $request->get('paid');
                $user->save();
            } else {
                $user = User::create([
                    'first_name'    => $request->get('first_name'),
                    'surname'       => $request->get('surname'),
                    'email'         => $request->get('email'),
                    'paid'          => $request->get('paid'),
                    'password'      => bcrypt(Str::random(10)),
                    'expiry_date'   => date('Y-m-d 00:00:00', strtotime($request->get('expires'))),
                    'next_payment_date' => date('Y-m-d', strtotime($request->get('next_payment_date'))),
                ]);

                $user->preferences()->create();
                $user->workoutPreferences()->create();
            }

            $this->updateSubscription($user);
        } catch (Exception $e) {
            throw new FitnessBadRequestException('Error, unable to manage user. Please try again later.');
        }

        return response()->json(['data' => $user]);
    }

    /**
     * Get user by email.
     */
    public function getUserByEmail(string $email)
    {
        $user = User::where('email', $email)->firstOrFail();
        return response()->json(['data' => $user]);
    }

    /**
     * Get multiple users by email addresses.
     */
    public function getUsersByEmail(GetUsersByEmailRequest $request)
    {
        $emails = $request->json('emails');
        $users = User::whereIn('email', $emails)->get();
        return response()->json(['data' => $users]);
    }

    /**
     * Get user by ID.
     */
    public function getUserById(int $id)
    {
        $user = User::findOrFail($id);
        return response()->json(['data' => $user]);
    }

    /**
     * Mark a user as not paid.
     * Used when a failed collection is detected via reconciliation reports.
     *
     * IMPORTANT.
     * This endpoint is used by the direct debit service to mark users not paid if their payment collection
     * has failed.
     */
    public function markUserNotPaid(int $id)
    {
        $user = User::findOrFail($id);
        $user->update(['paid' => 0]);
        return response()->json(['data' => $user]);
    }

    /**
     * Mark user as paid.
     *
     * IMPORTANT.
     * This endpoint is used by the direct debit service to mark user as paid and upate their expiry if needed, given
     * their payment collection has been successful
     */
    public function markUserPaid(int $id, MarkUserPaidRequest $request)
    {
        $user = User::findOrFail($id);

        $data = [
            'paid' => 1,
        ];
        if ($request->has('expiry_date')) {
            $data['expiry_date'] = $request->json('expiry_date');
        }
        if ($request->has('expiry_date')) {
            $data['next_payment_date'] = $request->json('next_payment_date');
        }
        $user->update($data);

        return response()->json(['data' => $user]);
    }

    /**
     * @deprecated
     *
     * Bulk update user expiry dates and set next payment dates where applicable.
     *
     * IMPORTANT. This is pre-deprecated, and endpoint should be removed after use as this is a one-off task.
     */
    public function bulkUpdateExpiries(BulkUpdateExpiriesRequest $request)
    {
        $updatedIds = [];
        $notFoundIds = [];
        foreach ($request->json('users') as $userData) {
            $user = User::find($userData['id']);
            if ($user) {
                $user->update([
                    'next_payment_date' => $userData['next_payment_date'],
                    'expiry_date' => $userData['expiry_date'],
                ]);
                $updatedIds[] = $userData['id'];
            } else {
                $notFoundIds[] = $userData['id'];
            }
        }
        return response()->json(['data' => [
            'updated_ids' => $updatedIds,
            'not_found_ids' => $notFoundIds,
        ]]);
    }

    private function updateSubscription(User $user)
    {
        if ($user->subscription()->exists() && $user->subscription->subscription_provider === 'smartdebit') {
            $this->updateUserSubscription($user);
        } else {
            $this->createUserSubscription($user);
        }
    }

    private function createUserSubscription(User $user)
    {
        $user->subscription()->create([
            'email' => $user->email,
            'subscription_provider' => 'smartdebit',
            'subscription_frequency' => 'monthly',
            'purchase_date' => now(),
            'expiry_date' => $user->expiry_date,
        ]);
    }

    private function updateUserSubscription(User $user)
    {
        $user->subscription->update([
            'subscription_frequency' => 'monthly',
            'expiry_date' => $user->expiry_date,
        ]);
    }
}
