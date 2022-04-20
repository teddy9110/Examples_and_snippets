<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAppleSubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('apple_subscriptions_data', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('product_id')->nullable();
            $table->string('bundle_id')->nullable();
            $table->date('purchase_date');
            $table->string('original_transaction_id')->index();
            $table->string('current_transaction_id')->nullable()->index();
            $table->boolean('auto_renew')->default(0);
            $table->boolean('is_trial')->default(0); // potentially not needed
            $table->boolean('intro_offer')->default(0); // potentially not needed
            $table->text('receipt_data');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('apple_subscriptions');
    }
}
