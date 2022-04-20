<?php

use Database\Seeders\SubscriptionSeeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('subscriptions')) {
            Schema::create('subscriptions', function (Blueprint $table) {
                $table->increments('id');
                $table->string('reference_name');
                $table->string('product_id')->unique();
                $table->string('duration');
                $table->boolean('active');
                $table->timestamps();
            });
        }

        // Seed the new subscriptions table with existing subscriptions
        $seeder = new SubscriptionSeeder();
        $seeder->run();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('subscriptions');
    }
}
