<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class EditUserSubscriptionsAddFreeSubscriptions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("
            ALTER TABLE user_subscriptions
            MODIFY subscription_provider
            ENUM(
                'shopify',
                'apple',
                'gocardless',
                'directdebit',
                'other',
                'smartdebit',
                'free'
            )
            NOT NULL
            DEFAULT 'other'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_subscriptions', function (Blueprint $table) {
            // No down migration, as it could cause issues with existing data
        });
    }
}
