<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserSubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'user_subscriptions',
            function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedInteger('user_id');
                $table->string('email');
                $table->enum('subscription_provider', ['shopify', 'apple','gocardless', 'directdebit', 'other'])
                    ->default('other');
                $table->enum('subscription_plan', ['free', 'standard', 'premium'])
                    ->default('standard');
                $table->enum('subscription_frequency', ['monthly', 'annual'])->default('annual');
                $table->dateTime('purchase_date')->nullable();
                $table->timestamp('expiry_date');
                $table->string('shopify_customer_id')->nullable();
                $table->string('subscription_reference')->nullable();
                $table->timestamps();

                $table->foreign('user_id')->on('users')->references('id')
                    ->onUpdate('cascade')->onDelete('cascade');
            }
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_subscriptions');
    }
}
