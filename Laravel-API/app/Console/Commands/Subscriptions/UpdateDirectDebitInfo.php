<?php

namespace Rhf\Console\Commands\Subscriptions;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Rhf\Modules\User\Models\User;
use Rhf\Modules\User\Models\UserSubscriptions;

class UpdateDirectDebitInfo extends Command
{
    public $run = true;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:update-directdebit';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Users to direct debit';

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
        while ($this->run == true) {
            $this->processsCSV();
        }
    }

    public function processsCSV()
    {
        $filename = storage_path('app/subscriptions/Ashbourne.csv');

        $rows = array_map('str_getcsv', file($filename));
        $header = array_shift($rows);
        $count = 0;

        while ($count < count($rows)) {
            $exists = User::where('email', $rows[$count][20])->first();
            if (
                $exists
                && Str::contains($rows[$count][11], ['Live', 'New'])
                && Str::contains($rows[$count][12], ['Continuation', 'New'])
            ) {
                $result = [
                    'dd_reference' => $rows[$count][3],
                    'created_at' => $rows[$count][15]
                ];
                $this->createUserSubscription($exists, $result, 'directdebit');
            }
            $count++;
        }
        $this->run = false;
    }

    public function createUserSubscription(
        $user,
        $data = null,
        $provider = 'shopify',
        $plan = 'standard',
        $freq = 'monthly'
    ) {
        $date = Date('d-m-Y', strtotime($data['created_at']));
        $created = Carbon::parse($date);
        UserSubscriptions::updateOrCreate(
            [
                'user_id' => $user->id,
                'email' => $user->email,
            ],
            [
                'subscription_provider' => $provider,
                'subscription_plan' => $plan,
                'subscription_frequency' => $freq,
                'purchase_date' => !is_null($data) ? $created : null,
                'subscription_reference' => !is_null($data) ? $data['dd_reference'] : null,
                'expiry_date' => is_null($user->expiry_date) ?
                    $created->addYear() :
                    $user->expiry_date,
            ]
        );
    }
}
