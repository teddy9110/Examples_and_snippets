<?php

namespace Database\seeders;

use Illuminate\Database\Seeder;
use Rhf\Modules\Subscription\Models\Subscription;

class SubscriptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Subscription::create([
            "reference_name" => 'RH Fitness IAP Monthly',
            "product_id" => config('app.subscriptions_monthly'),
            "duration" => 'month',
            "active" => false
        ]);

        Subscription::create([
            "reference_name" => 'RH Fitness IAP Yearly',
            "product_id" => config('app.subscriptions_annual'),
            "duration" => 'year',
            "active" => true
        ]);
    }
}
