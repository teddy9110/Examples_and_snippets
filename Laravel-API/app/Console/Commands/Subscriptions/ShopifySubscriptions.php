<?php

namespace Rhf\Console\Commands\Subscriptions;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use PHPShopify\ShopifySDK;
use Rhf\Modules\User\Models\User;
use Rhf\Modules\User\Models\UserSubscriptions;

class ShopifySubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shopify:annual {plan=Annual Life Plan Subscription}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Retrieve all annual orders places via shopify and add record into ' .
        'user_subscriptions table where a matching email is found.';

    private $shopify;
    private $hasNextPage = true;
    private $cursor;
    private $plan;
    private $planType;

    public function __construct()
    {
        parent::__construct();

        //different Config for GraphQL
        $config = array(
            'ShopUrl' => config('shopify.SHOPIFY_URL'),
            'ApiKey' => config('shopify.SHOPIFY_API_KEY'),
            'AccessToken' => config('shopify.SHOPIFY_PASSWORD'),
        );
        $this->shopify = ShopifySDK::config($config);
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('starting');
        // gets the plan argument
        $this->plan = $this->argument('plan');
        // sets the plan type based on the passed in plan
        $this->planType = strpos($this->plan, 'Annual Life Plan') !== false ? 'annual' : 'monthly';

        // Stop job if false is detected
        if ($this->hasNextPage === false) {
            Log::info('end of query');
            return;
        }
        // only run script whilst the hasNextPage is true
        while ($this->hasNextPage == true) {
            Log::info('Current Cursor: ' . $this->cursor);
            sleep(20); // sleep to replenish the api, potentially can be reduced
            $this->shopifyQuery();
        }
    }

    public function shopifyQuery()
    {
        // graphQL query based on shopifySDK docs
        $query = <<<Query
            query(\$query: String, \$cursor: String) {
                orders(first: 240, query: \$query, after: \$cursor) {
                    edges {
                        node {
                            createdAt
                            refunds {
                                id
                            }
                            customer {
                                email
                                id
                            },
                            transactions {
                                id
                            }
                        }
                        cursor
                    }
                    pageInfo {
                      hasNextPage
                      hasPreviousPage
                    }
                }
            }
        Query;

        // Variables for the graphQL query, cursor starts empty and is populated after the first run
        $variables = [
            'query' => $this->plan,
            'cursor' => $this->cursor
        ];

        //post data to api with variables
        $data = $this->shopify->GraphQL->post($query, null, null, $variables);

        // updates cursor to last record in the list (no pagination inside the pageInfo
        // need to use the last node position to get the next set of nodes
        $this->cursor = last($data['data']['orders']['edges'])['cursor'];

        $this->createUserSubscriptionRecord($data);
    }

    public function createUserSubscriptionRecord($data)
    {
        collect($data['data']['orders']['edges'])->each(
            function ($value) {
                if (!is_null($value['node']['customer']) && empty($value['node']['refunds'])) {
                    $user = User::where('email', $value['node']['customer']['email'])->first();
                    if ($user) {
                        //potential bottleneck
                        UserSubscriptions::updateOrCreate(
                            ['user_id' => $user->id, 'email' => $user->email],
                            [
                                'subscription_provider' => 'shopify',
                                'subscription_plan' => 'standard',
                                'subscription_frequency' => $this->planType,
                                'purchase_date' => Carbon::parse($value['node']['createdAt']),
                                'expiry_date' => $user->expiry_date,
                                'shopify_customer_id' => explode('Customer/', $value['node']['customer']['id'])[1],
                                'subscription_reference' => explode(
                                    'OrderTransaction/',
                                    $value['node']['transactions'][0]['id']
                                )[1],
                            ]
                        );
                    } else {
                        Log::info('Email Not Found: ' . $value['node']['customer']['email']);
                    }
                }
            }
        );

        $this->updatePages($data);
    }

    private function updatePages($data)
    {
        if ($data['data']['orders']['pageInfo']['hasNextPage'] !== true) {
            $this->hasNextPage = false;
        }
    }
}
