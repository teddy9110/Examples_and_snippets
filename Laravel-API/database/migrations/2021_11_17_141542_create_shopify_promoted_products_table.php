<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShopifyPromotedProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shopify_promoted_products', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->string('website_image')->nullable();
            $table->string('mobile_image')->nullable();
            $table->boolean('active')->default(false);
            $table->boolean('website_only')->default(false);
            $table->integer('shopify_product_id');
            $table->string('shopify_product_type');
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
        Schema::dropIfExists('shopify_promoted_products');
    }
}
