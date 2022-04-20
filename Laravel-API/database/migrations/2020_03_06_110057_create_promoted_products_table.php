<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePromotedProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('promoted_products', function (Blueprint $table) {
            $table->increments('id');
            $table->string('placement_slug');

            $table->enum('type', ['shopify-category', 'shopify-product']);
            $table->string('value');
            $table->boolean('active')->default(0);
            $table->string('name');
            $table->text('description');
            $table->string('image');

            $table->timestamps();

            $table->foreign('placement_slug')
                ->references('slug')
                ->on('promoted_product_placements')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('promoted_products');
    }
}
