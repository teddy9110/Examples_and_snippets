<?php

namespace Rhf\Console\Commands\Subscriptions;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Rhf\Modules\User\Models\User;
use Rhf\Modules\User\Models\UserSubscriptions;

class UpdateUsersGoCardless extends Command
{
    public $activeRun = true;
    public $inactiveRun = false;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:update-gocardless';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check if user exists as an annual subscription, if not add in as direct debit';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        while ($this->activeRun) {
            $this->error('active run');
            $this->findActive();
        }
        while ($this->inactiveRun) {
            $this->error('inactive run');
            $this->findInactive();
        }
    }

    public function findActive()
    {
        $filename = storage_path('app/subscriptions/gcl_active.csv');

        $rows = array_map('str_getcsv', file($filename));
        $header = array_shift($rows);
        $count = 0;

        while ($count < count($rows)) {
            $exists = User::where('email', $rows[$count][2])->first();
            if ($exists) {
                $result = [
                    'gocardless_id' => $rows[$count][0],
                    'created_at' => $rows[$count][1]
                ];
                $this->createUserSubscription($exists, $result, 'gocardless');
            }
            $count++;
        }
        $this->activeRun = false;
        $this->inactiveRun = true;
    }

    public function findInactive()
    {
        $filename = storage_path('app/subscriptions/gcl_inactive.csv');
        $rows = array_map('str_getcsv', file($filename));
        $header = array_shift($rows);
        $count = 0;

        while ($count < count($rows)) {
            $exists = User::where('email', $rows[$count][2])->first();
            if ($exists) {
                $result = [
                    'gocardless_id' => $rows[$count][0],
                    'created_at' => $rows[$count][1]
                ];
                $this->createUserSubscription($exists, $result, 'gocardless');
            }
            $count++;
        }
        $this->inactiveRun = false;
    }

    public function createUserSubscription(
        $user,
        $goCardless = null,
        $provider = 'shopify',
        $plan = 'standard',
        $freq = 'monthly'
    ) {
        UserSubscriptions::updateOrCreate(
            [
                'user_id' => $user->id,
                'email' => $user->email,
            ],
            [
                'subscription_provider' => $provider,
                'subscription_plan' => $plan,
                'subscription_frequency' => $freq,
                'purchase_date' => !is_null($goCardless) ? $goCardless['created_at'] : null,
                'expiry_date' => is_null($user->expiry_date) ?
                    Carbon::parse($user->created_at)->endOfDay() :
                    $user->expiry_date,
                'subscription_reference' => !is_null($goCardless) ? $goCardless['gocardless_id'] : null
            ]
        );
    }
}
