<?php

namespace Rhf\Modules\Shopify\Controllers;

use Exception;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redis;
use PHPShopify\ShopifySDK;
use Rhf\Http\Controllers\Controller;
use Rhf\Mail\AnnualEmail;
use Rhf\Mail\WelcomeEmail;
use Rhf\Modules\Subscription\Services\DirectDebitApiService;
use Rhf\Modules\User\Models\User;
use Rhf\Modules\User\Models\UserSubscriptions;

class ShopifyController extends Controller
{
    private $shopifySDK;
    private $directDebitApiService;

    /**
     * ContentController constructor.
     */
    public function __construct(DirectDebitApiService $directDebitApiService)
    {
        $this->directDebitApiService = $directDebitApiService;

        $config = array(
            'ShopUrl' => config('shopify.SHOPIFY_URL'),
            'ApiKey' => config('shopify.SHOPIFY_API_KEY'),
            'Password' => config('shopify.SHOPIFY_PASSWORD'),
        );

        $this->shopifySDK = ShopifySDK::config($config);
    }


    /**
     * Get the available content.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws Exception
     */
    public function orderPaid(Request $request)
    {
        // TODO: This should handle each product in order, not loop through all and essentially only
        //       handle latest or combination of all.

        // This endpoint is never called by a human, so debugbar is not useful.
        debugbar()->disable();

        $this->verify($request);

        $data = $request->json()->all();

        $redisTtl = 60 * 60 * 72;
        $redisKey = 'shopify:order-paid:' . $data['id'];
        $processed = Redis::get($redisKey);
        if ($processed) {
            return response()->json(['success' => true]);
        };

        Redis::set($redisKey, json_encode(['status' => 'processing']), 'EX', $redisTtl);
        $shouldFullfill = true;
        $months = 0;
        $emailTemplate = null;
        foreach ($data['line_items'] as $line_item) {
            if ($line_item['product_id'] == config('app.shopify_annual_product')) {
                // Annual plan
                // Increase by year
                $months = max(12, $months);
                $emailTemplate = AnnualEmail::class;
            } elseif (
                $line_item['product_id'] == config('app.shopify_monthly_product') ||
                $line_item['product_id'] == config('app.shopify_monthly_special_offer_product')
            ) {
                // Monthly plan (overriden by annual, if they somehow buy both)
                if (!$emailTemplate) {
                    // Don't create an account, but do send them an email
                    $months = max(0, $months);
                    $emailTemplate = WelcomeEmail::class;
                }
            } elseif ($line_item['product_id'] == config('app.shopify_quarterly_product')) {
                // QUARTERLY 3 MONTH PLAN
                if (!$emailTemplate) {
                    $months = max(3, $months);
                    $emailTemplate = AnnualEmail::class;
                }
            } else {
                // It's not a life plan
                $shouldFullfill = false;
            }
        }

        $email = null;
        $shopifyCustomerId = null;
        if (isset($data['customer'])) {
            $first_name = $data['customer']['first_name'];
            $last_name = $data['customer']['last_name'];
            $email = $data['email'];
            $shopifyCustomerId = $data['customer']['id'];
        }

        // They've bought a life plan
        if ($months > 0) {
            $frequency = $months === 3 ? 'quarterly' : 'annual';
            // Check for existing user
            $user = User::where('email', '=', $request->get('email'))->first();
            if ($user) {
                // Default is to start from today
                $expiry = now()->setTime(23, 59, 59);
                // If they already have a sub, extend it
                if ($expiry->lessThan($user->expiry_date)) {
                    $expiry = $user->expiry_date;
                }
                $user->expiry_date = $expiry->addMonths($months);
                $user->paid = true;
                $user->save();

                //add into user_subscriptions table
                $this->userSubscriptionInformation($user, $shopifyCustomerId, 'standard', $frequency);
            } else {
                // Set expiry to now + number of months bought
                $expiry = now()->setTime(23, 59, 59)->addMonths($months);
                $user = User::create([
                    'first_name' => $first_name,
                    'surname' => $last_name,
                    'email' => $email,
                    'paid' => true,
                    'password' => bcrypt(Str::random(10)),
                    'expiry_date' => $expiry,
                ]);

                // Must setup preferences!
                $user->preferences()->create();
                $user->workoutPreferences()->create();

                //add into user_subscriptions table
                $this->userSubscriptionInformation($user, $shopifyCustomerId, 'standard', $frequency);
            }
        }

        if ($email) {
            Log::info("Shopify: $email made a purchase");
        } elseif (!$email && $emailTemplate) {
            Log::info("Shopify: Life Plan purchase has no email");
        }

        if ($emailTemplate) {
            // Send an email to the user

            if ($emailTemplate == WelcomeEmail::class) {
                $signup = $this->directDebitApiService->createDirectDebitSignup(
                    DirectDebitApiService::TYPE_NEW_CONTRACT_SIGNUP,
                    $email
                );
                if (!is_null($signup)) {
                    $ukSignupUrl = $this->directDebitApiService->generateUkSignupUrl($signup['payer_reference']);
                    Mail::to($email)->queue(new $emailTemplate(
                        $first_name ?? $email ?? 'New User',
                        now()->toDateString(),
                        $ukSignupUrl
                    ));
                }
            } else {
                Mail::to($email)->queue(new $emailTemplate(
                    $first_name ?? $email ?? 'New User',
                    now()->toDateString()
                ));
            }

            Log::info("Shopify: Sent welcome email to $email");
        }

        if ($shouldFullfill) {
            try {
                $response = $this->shopifySDK->Order($data['id'])->Fulfillment->post([
                    'location_id' => config('shopify.SHOPIFY_LOCATION_ID'), // TODO: customisable via admin panel?
                    'tracking_number' => null
                ]);
            } catch (Exception $e) {
                // Do nothing - delivery staff will fulfill instead.
            }
        }

        Redis::set($redisKey, json_encode(['status' => 'processed']), 'EX', $redisTtl);

        // Empty response, because JSON webhook
        return response()->json(['success' => true]);
    }

    private function verifyWebhook($data, $hmac_header)
    {
        $calculated_hmac = base64_encode(hash_hmac('sha256', $data, config('shopify.SHOPIFY_APP_SECRET'), true));
        return hash_equals($hmac_header, $calculated_hmac);
    }

    private function verify(Request $request)
    {
        $hmac_header = $request->server('HTTP_X_SHOPIFY_HMAC_SHA256');
        $data = file_get_contents('php://input');
        $verified = $this->verifyWebhook($data, $hmac_header);

        if (!$verified) {
            throw new Exception('Could not verify shopify token');
        }

        return $verified;
    }

    private function userSubscriptionInformation($user, $customerId, $plan, $frequency)
    {
        UserSubscriptions::updateOrCreate(
            [
                'user_id' => $user->id,
                'email' => $user->email,
                'subscription_provider' => 'shopify',
            ],
            [
                'subscription_plan' => $plan,
                'subscription_frequency' => $frequency,
                'purchase_date' => now(),
                'expiry_date' => $user->expiry_date,
                'shopify_customer_id' => $customerId
            ]
        );
    }
}
